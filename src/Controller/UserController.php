<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/login/process', name: 'login_process', methods: ['POST'])]
    public function loginProcess(Request $request): Response
    {
        // Simulate user authentication for testing (replace with actual logic)
        $userId = 1; // Replace with the ID of the logged-in user for testing

        // Store the user ID in the session to simulate login
        $request->getSession()->set('user_id', $userId);

        return $this->redirectToRoute('app_library'); // Redirect to homepage or desired page
    }
}
