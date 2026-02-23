<?php

namespace App\Controller;

use App\Service\ChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/chatbot')]
class ChatbotController extends AbstractController
{
    #[Route('/message', name: 'api_chatbot_message', methods: ['POST'])]
    public function handleMessage(Request $request, ChatbotService $chatbotService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $userMessage = $data['message'] ?? '';
            $userRole = $data['role'] ?? 'client';

            if (empty($userMessage)) {
                return $this->json(['error' => 'Message vide'], Response::HTTP_BAD_REQUEST);
            }

            // Limit message length
            $userMessage = substr($userMessage, 0, 500);

            // Process message through chatbot service
            $response = $chatbotService->processMessage($userMessage, $userRole);

            return $this->json([
                'success' => true,
                'message' => $response,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors du traitement: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/info', name: 'api_chatbot_info', methods: ['GET'])]
    public function getInfo(): JsonResponse
    {
        return $this->json([
            'name' => 'Firmetna Bot',
            'version' => '1.0',
            'available' => true,
            'description' => 'Assistant virtuel pour Firmetna'
        ]);
    }
}
