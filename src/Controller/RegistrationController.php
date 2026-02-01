<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
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
            $roleType = $form->get('roleType')->getData();
            $roleMapping = [
                'Agriculteur' => 'ROLE_AGRICULTEUR',
                'Client' => 'ROLE_CLIENT',
                'Donateur' => 'ROLE_DONATEUR',
            ];
            $user->setRole($roleMapping[$roleType] ?? 'ROLE_USER');

            // Handle Profile
            $profile = new Profile();
            $profile->setUser($user);
            $profile->setNom($form->get('nom')->getData());
            $profile->setPrenom($form->get('prenom')->getData());
            $profile->setTelephone($form->get('telephone')->getData());
            $profile->setRoleType($roleType);
            
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
                    $profile->setImageUrl('/uploads/profiles/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $profile->setDateInscription(new \DateTime());
            $profile->setStatut('ACTIF');

            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->flush();

            // Success redirect to login
            $this->addFlash('success', 'Votre compte a été créé avec succès ! Connectez-vous maintenant.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
