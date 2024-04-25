<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    
    #[Route('/', name: 'app_home')]
    public function index( UtilisateurRepository $utilisateurRepo): Response
    {
        // Récupérer le prénom de l'utilisateur à partir des informations de session
        
        $user = $this->getUser();
        

       

        $welcomeMessage = 'Welcome!';

   

        return $this->render('home/index.html.twig', [
            'welcomeMessage' => $welcomeMessage,
        ]);
    }

    
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale($locale, Request $request): Response
    {
       
        $request->getSession()->set('_locale', $locale);

   
        return $this->redirect($request->headers->get('referer'));
    }
}
