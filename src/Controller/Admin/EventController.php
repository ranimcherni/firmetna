<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\HuggingFaceService;
use App\Service\PhpMailerEventService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_admin_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $q = $request->query->get('q');
        $fromParam = $request->query->get('from');
        $toParam = $request->query->get('to');

        $queryBuilder = $eventRepository->createQueryBuilder('e');

        if ($q) {
            $queryBuilder->andWhere('e.nom LIKE :q OR e.organisateur LIKE :q')
                ->setParameter('q', '%' . $q . '%');
        }

        if ($fromParam) {
            $from = new \DateTime($fromParam);
            $queryBuilder->andWhere('e.date >= :from')
                ->setParameter('from', $from);
        }

        if ($toParam) {
            $to = (new \DateTime($toParam))->setTime(23, 59, 59);
            $queryBuilder->andWhere('e.date <= :to')
                ->setParameter('to', $to);
        }

        $queryBuilder->orderBy('e.date', 'ASC');
        $events = $queryBuilder->getQuery()->getResult();

        return $this->render('admin/event/index.html.twig', [
            'events' => $events,
            'from' => $fromParam,
            'to' => $toParam,
        ]);
    }

    #[Route('/new', name: 'app_admin_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', '├ëv├®nement cr├®├® avec succ├¿s !');
            return $this->redirectToRoute('app_admin_event_index');
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', '├ëv├®nement modifi├® avec succ├¿s !');
            return $this->redirectToRoute('app_admin_event_index');
        }

        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/participations', name: 'app_admin_event_participations', methods: ['GET'])]
    public function participations(Event $event): Response
    {
        return $this->render('admin/event/participations.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/generate-description', name: 'app_admin_event_generate_description', methods: ['POST'])]
    public function generateDescription(Request $request, HuggingFaceService $huggingFaceService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $eventName = $data['nom'] ?? '';

        if (empty($eventName)) {
            return new JsonResponse(['error' => 'Le nom de l\'événement est requis.'], 400);
        }

        try {
            $description = $huggingFaceService->generateDescription($eventName);
            return new JsonResponse(['description' => $description]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/qrcode', name: 'app_admin_event_qrcode', methods: ['GET'])]
    public function qrcode(Event $event): Response
    {
        $lieu = $event->getLieu();
        $lieuText = $lieu ? $lieu->getVille() . ' - ' . $lieu->getAdresse() : 'Non defini';

        $qrText = "Evenement: " . $event->getNom() . "\n"
            . "Date: " . $event->getDate()->format('d/m/Y H:i') . "\n"
            . "Organisateur: " . ($event->getOrganisateur() ?: 'Non defini') . "\n"
            . "Lieu: " . $lieuText . "\n"
            . "Description: " . ($event->getDescription() ?: 'Aucune');

        $builder = new Builder(writer: new SvgWriter());
        $result = $builder->build(data: $qrText, size: 300, margin: 10);

        return new Response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_event_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Event $event,
        EntityManagerInterface $entityManager,
        PhpMailerEventService $phpMailer
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            // Collecter les emails des participants AVANT suppression (cascade remove les participations)
            $participantEmails = [];
            foreach ($event->getParticipations() as $participation) {
                if ($participation->getUser() && $participation->getUser()->getEmail()) {
                    $participantEmails[] = $participation->getUser()->getEmail();
                }
            }

            // Envoyer un email à chaque participant via PHPMailer
            if (!empty($participantEmails)) {
                $htmlContent = $this->renderView('emails/event_cancelled.html.twig', ['event' => $event]);
                $subject = 'Annulation de l\'événement : ' . $event->getNom();
                $sendFailed = false;
                foreach (array_unique($participantEmails) as $recipientEmail) {
                    if (!$phpMailer->sendHtmlEmail($recipientEmail, $subject, $htmlContent)) {
                        $sendFailed = true;
                    }
                }
                if ($sendFailed) {
                    $this->addFlash('warning', 'Événement supprimé mais certains emails n\'ont pas pu être envoyés.');
                }
            }

            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_event_index');
    }
}
