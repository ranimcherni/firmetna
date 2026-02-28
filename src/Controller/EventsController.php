<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenements')]
class EventsController extends AbstractController
{
    #[Route('/', name: 'app_events')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $fromParam = $request->query->get('from');
        $toParam = $request->query->get('to');

        $from = $fromParam ? new \DateTime($fromParam) : null;
        $to = $toParam ? (new \DateTime($toParam))->setTime(23, 59, 59) : null;

        $events = $eventRepository->findByDateRange($from, $to);

        return $this->render('front/events/index.html.twig', [
            'events' => $events,
            'from' => $fromParam,
            'to' => $toParam,
        ]);
    }

    #[Route('/{id}/qrcode', name: 'app_events_qrcode', methods: ['GET'])]
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

    #[Route('/participer/{id}', name: 'app_events_participer')]
    public function participer(
        Event $event,
        EntityManagerInterface $entityManager,
        ParticipationRepository $participationRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour participer à un évènement.');
            return $this->redirectToRoute('app_login');
        }

        if ($participationRepository->userParticipatesInEvent($user, $event)) {
            $this->addFlash('info', 'Vous participez déjà à cet évènement.');
            return $this->redirectToRoute('app_events');
        }

        $participation = new Participation();
        $participation->setEvent($event);
        $participation->setUser($user);

        $entityManager->persist($participation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre participation à l\'évènement "' . $event->getNom() . '" a été enregistrée !');
        return $this->redirectToRoute('app_events');
    }

    #[Route('/annuler-participation/{id}', name: 'app_events_annuler_participation')]
    public function annulerParticipation(
        Event $event,
        EntityManagerInterface $entityManager,
        ParticipationRepository $participationRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour gérer vos participations.');
            return $this->redirectToRoute('app_login');
        }

        $participation = $participationRepository->findOneBy([
            'event' => $event,
            'user' => $user,
        ]);

        if ($participation) {
            $entityManager->remove($participation);
            $entityManager->flush();
            $this->addFlash('success', 'Votre participation à l\'évènement "' . $event->getNom() . '" a été annulée.');
        } else {
            $this->addFlash('info', 'Aucune participation trouvée pour cet évènement.');
        }

        return $this->redirectToRoute('app_events');
    }
}
