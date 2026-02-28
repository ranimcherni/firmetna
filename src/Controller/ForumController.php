<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Like;
use App\Entity\Notification;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Form\PublicationType;
use App\Repository\LikeRepository;
use App\Repository\NotificationRepository;
use App\Repository\PublicationRepository;
use App\Service\ModerationService;
use App\Service\ShareService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum')]
    public function index(Request $request, PublicationRepository $publicationRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        $type = $request->query->get('type', '');
        $sort = $request->query->get('sort', 'date');

        $query = $publicationRepository->searchQuery($search ?: null, $type ?: null, $sort);
        
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // limit per page
        );

        return $this->render('forum/index.html.twig', [
            'publications' => $pagination,
            'search' => $search,
            'type' => $type,
            'sort' => $sort,
        ]);
    }

    #[Route('/nouveau', name: 'app_forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ModerationService $moderation): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour créer une publication.');
            return $this->redirectToRoute('app_login');
        }

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        // Important: les champs "auteur" et "dateCreation" ne sont pas dans le formulaire,
        // mais ils sont obligatoires (validation). On les définit AVANT isValid().
        if ($form->isSubmitted()) {
            $publication->setAuteur($this->getUser());
            if (!$publication->getDateCreation()) {
                $publication->setDateCreation(new \DateTimeImmutable());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // --- Moderation Check ---
            $textToCheck = ($publication->getTitre() ?? '') . ' ' . strip_tags($publication->getContenu() ?? '');
            if ($moderation->isToxic($textToCheck)) {
                $this->addFlash('danger', '⚠️ Votre publication a été refusée car elle contient du contenu inapproprié ou offensant.');
                return $this->render('forum/new.html.twig', ['form' => $form->createView()]);
            }
            // --- End Moderation Check ---

            try {
                // Gestion de l'image
                $imageFile = $form->get('imageFile')->getData();
                if ($imageFile) {
                    // Créer le dossier s'il n'existe pas
                    $uploadDir = $this->getParameter('publications_directory');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                    
                    try {
                        $imageFile->move($uploadDir, $newFilename);
                        // Le chemin doit être relatif depuis public/
                        $publication->setImageFilename('uploads/publications/'.$newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image : '.$e->getMessage());
                    }
                }

                // Sauvegarder
                $entityManager->persist($publication);
                $entityManager->flush();

                $this->addFlash('success', 'Votre publication a été partagée !');
                // Rediriger vers la page de détail de la publication créée
                return $this->redirectToRoute('app_forum_show', ['id' => $publication->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la création de la publication : '.$e->getMessage());
            }
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voir/{id}', name: 'app_forum_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, PublicationRepository $publicationRepository, EntityManagerInterface $entityManager, LikeRepository $likeRepository, ShareService $shareService, ModerationService $moderation): Response
    {
        $publication = $publicationRepository->findWithCommentaires($id);

        if (!$publication) {
            throw $this->createNotFoundException('La publication n\'existe pas.');
        }

        $user = $this->getUser();
        $isLiked = false;
        if ($user) {
            $like = $likeRepository->findLikeByUserAndPublication($user, $publication);
            $isLiked = $like !== null;
        }

        $parentId = $request->request->get('parent_id');
        $commentaire = new Commentaire();
        
        if ($parentId) {
            $parent = $entityManager->getRepository(Commentaire::class)->find($parentId);
            if ($parent && $parent->getPublication() === $publication) {
                $commentaire->setParent($parent);
            }
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        // Important: auteur/publication/dateCreation sont obligatoires mais pas dans le formulaire
        if ($form->isSubmitted()) {
            if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
                return $this->redirectToRoute('app_login');
            }
            $commentaire->setAuteur($user);
            $commentaire->setPublication($publication);
            if (!$commentaire->getDateCreation()) {
                $commentaire->setDateCreation(new \DateTimeImmutable());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // --- Moderation Check ---
            if ($moderation->isToxic($commentaire->getContenu() ?? '')) {
                $this->addFlash('danger', '⚠️ Votre commentaire a été refusé car il contient du contenu inapproprié ou offensant.');
                return $this->redirectToRoute('app_forum_show', ['id' => $id]);
            }
            // --- End Moderation Check ---

            $entityManager->persist($commentaire);

            // Créer une notification si ce n'est pas l'auteur de la publication
            if ($publication->getAuteur() !== $user) {
                $notification = new Notification();
                $notification->setDestinataire($publication->getAuteur());
                $notification->setAuteur($user);
                $notification->setPublication($publication);
                $notification->setCommentaire($commentaire);
                $notification->setType($commentaire->getParent() ? Notification::TYPE_REPLY : Notification::TYPE_COMMENT);
                $entityManager->persist($notification);
            }

            // Si c'est une réponse, notifier le parent
            if ($commentaire->getParent() && $commentaire->getParent()->getAuteur() !== $user) {
                $notification = new Notification();
                $notification->setDestinataire($commentaire->getParent()->getAuteur());
                $notification->setAuteur($user);
                $notification->setPublication($publication);
                $notification->setCommentaire($commentaire);
                $notification->setType(Notification::TYPE_REPLY);
                $entityManager->persist($notification);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté !');
            return $this->redirectToRoute('app_forum_show', ['id' => $id]);
        }

        // Créer un formulaire de réponse pour chaque commentaire
        $replyForms = [];
        foreach ($publication->getCommentaires() as $com) {
            if (!$com->getParent()) {
                $replyCommentaire = new Commentaire();
                $replyForm = $this->createForm(CommentaireType::class, $replyCommentaire);
                $replyForms[$com->getId()] = $replyForm->createView();
            }
        }

        return $this->render('forum/show.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
            'isLiked' => $isLiked,
            'replyForms' => $replyForms,
            'shareService' => $shareService,
        ]);
    }
    #[Route('/modifier/{id}', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, PublicationRepository $publicationRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $publication = $publicationRepository->find($id);

        if (!$publication) {
            throw $this->createNotFoundException('La publication n\'existe pas.');
        }

        if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cette publication.');
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Créer le dossier s'il n'existe pas
                $uploadDir = $this->getParameter('publications_directory');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($uploadDir, $newFilename);
                    // Le chemin doit être relatif depuis public/
                    $publication->setImageFilename('uploads/publications/'.$newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('warning', 'Erreur lors de l\'upload de l\'image : '.$e->getMessage());
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a été mise à jour !');
            return $this->redirectToRoute('app_forum_show', ['id' => $id]);
        }

        return $this->render('forum/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            if ($publication->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de cette publication.');
            }
            
            $entityManager->remove($publication);
            $entityManager->flush();
            $this->addFlash('success', 'Publication supprimée.');
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/like/{id}', name: 'app_forum_like', methods: ['POST'])]
    public function like(int $id, Request $request, PublicationRepository $publicationRepository, LikeRepository $likeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'Vous devez être connecté'], 401);
        }

        $publication = $publicationRepository->find($id);
        if (!$publication) {
            return new JsonResponse(['error' => 'Publication non trouvée'], 404);
        }

        $user = $this->getUser();
        $existingLike = $likeRepository->findLikeByUserAndPublication($user, $publication);

        if ($existingLike) {
            // Retirer le like
            $entityManager->remove($existingLike);
            $liked = false;
        } else {
            // Ajouter le like
            $like = new Like();
            $like->setUser($user);
            $like->setPublication($publication);
            $entityManager->persist($like);
            $liked = true;

            // Créer une notification si ce n'est pas l'auteur
            if ($publication->getAuteur() !== $user) {
                $notification = new Notification();
                $notification->setDestinataire($publication->getAuteur());
                $notification->setAuteur($user);
                $notification->setPublication($publication);
                $notification->setType(Notification::TYPE_LIKE);
                $entityManager->persist($notification);
            }
        }

        $entityManager->flush();

        $likesCount = $likeRepository->countLikesByPublication($publication);

        return new JsonResponse([
            'liked' => $liked,
            'count' => $likesCount,
        ]);
    }

    #[Route('/notifications', name: 'app_notifications')]
    public function notifications(NotificationRepository $notificationRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $notificationRepository->findByUser($this->getUser(), 50);
        $unreadCount = $notificationRepository->countUnreadByUser($this->getUser());

        return $this->render('forum/notifications.html.twig', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/notifications/marquer-lu/{id}', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markNotificationAsRead(int $id, NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        $notification = $notificationRepository->find($id);
        if (!$notification || $notification->getDestinataire() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Notification non trouvée'], 404);
        }

        $notification->setLu(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/notifications/marquer-tout-lu', name: 'app_notifications_mark_all_read', methods: ['POST'])]
    public function markAllNotificationsAsRead(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        $notifications = $notificationRepository->findUnreadByUser($this->getUser());
        foreach ($notifications as $notification) {
            $notification->setLu(true);
        }
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/commentaire/modifier/{id}', name: 'app_forum_comment_edit', methods: ['GET', 'POST'])]
    public function editComment(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
        
        if (!$commentaire) {
            throw $this->createNotFoundException('Le commentaire n\'existe pas.');
        }

        if ($commentaire->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de ce commentaire.');
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setDateModification(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été modifié !');
            return $this->redirectToRoute('app_forum_show', ['id' => $commentaire->getPublication()->getId()]);
        }

        return $this->render('forum/edit_comment.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commentaire/supprimer/{id}', name: 'app_forum_comment_delete', methods: ['POST'])]
    public function deleteComment(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
        
        if (!$commentaire) {
            throw $this->createNotFoundException('Le commentaire n\'existe pas.');
        }

        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            if ($commentaire->getAuteur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas l\'auteur de ce commentaire.');
            }

            $publicationId = $commentaire->getPublication()->getId();
            $entityManager->remove($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé.');
            return $this->redirectToRoute('app_forum_show', ['id' => $publicationId]);
        }

        return $this->redirectToRoute('app_forum');
    }

    #[Route('/mes-publications', name: 'app_forum_my_posts', methods: ['GET'])]
    public function myPosts(Request $request, PublicationRepository $publicationRepository, PaginatorInterface $paginator): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour voir vos publications.');
            return $this->redirectToRoute('app_login');
        }

        $query = $publicationRepository->createQueryBuilder('p')
            ->where('p.auteur = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('p.dateCreation', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10 // limit per page
        );

        return $this->render('forum/my_posts.html.twig', [
            'publications' => $pagination,
        ]);
    }
}
