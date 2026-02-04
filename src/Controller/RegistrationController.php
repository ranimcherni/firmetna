<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set role based on selection
            $roleType = $user->getRoleType();
            $roleMapping = [
                'Agriculteur' => 'ROLE_AGRICULTEUR',
                'Client' => 'ROLE_CLIENT',
                'Donateur' => 'ROLE_DONATEUR',
            ];
            $user->setRole($roleMapping[$roleType] ?? 'ROLE_USER');

            // Handle File Upload
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/profiles',
                        $newFilename
                    );
                    $user->setImageUrl('/uploads/profiles/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $user->setDateInscription(new \DateTime());
            $user->setStatut('Actif');

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                // Success redirect to login
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Connectez-vous pour accéder à votre espace.');
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Erreur lors de la création du compte : ' . $e->getMessage());
            }
        }

        // Display validation errors if form was submitted but invalid
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            if (!empty($errors)) {
                $this->addFlash('danger', 'Erreurs de validation : ' . implode(', ', $errors));
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
