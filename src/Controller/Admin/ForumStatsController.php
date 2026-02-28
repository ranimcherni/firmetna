<?php

namespace App\Controller\Admin;

use App\Repository\CommentaireRepository;
use App\Repository\LikeRepository;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum-stats')]
class ForumStatsController extends AbstractController
{
    #[Route('/', name: 'app_admin_forum_stats')]
    public function index(
        PublicationRepository $publicationRepository,
        CommentaireRepository $commentaireRepository,
        LikeRepository $likeRepository,
        UserRepository $userRepository
    ): Response {
        // Global Metrics
        $totalPublications = $publicationRepository->count([]);
        $totalCommentaires = $commentaireRepository->count([]);
        $totalLikes = $likeRepository->count([]);
        $avgEngagement = $totalPublications > 0 
            ? round(($totalCommentaires + $totalLikes) / $totalPublications, 1) 
            : 0;

        // Top Entities
        $topLiked = $publicationRepository->findMostLiked(5);
        $topCommented = $publicationRepository->findMostCommented(5);
        $topContributors = $userRepository->findTopContributors(5);

        return $this->render('admin/forum_stats/index.html.twig', [
            'totalPublications' => $totalPublications,
            'totalCommentaires' => $totalCommentaires,
            'totalLikes' => $totalLikes,
            'avgEngagement' => $avgEngagement,
            'topLiked' => $topLiked,
            'topCommented' => $topCommented,
            'topContributors' => $topContributors,
        ]);
    }
}
