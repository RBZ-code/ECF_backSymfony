<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileUserController extends AbstractController
{
    #[Route('/profile/user', name: 'app_profile_user')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        // dd($user->getReservations());

        return $this->render('profile_user/index.html.twig', [
            'mesResas' => 'ProfileUserController',
        ]);
    }


}
