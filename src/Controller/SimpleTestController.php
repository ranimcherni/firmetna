<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleTestController
{
    #[Route('/simple', name: 'app_simple_test')]
    public function test(): Response
    {
        return new Response('<h1 style="color: red;">Ô£à TEST REUSSI!</h1><p>Symfony fonctionne.</p>');
    }
}
