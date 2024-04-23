<?php

namespace App\Controller;

use App\Repository\LoanRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/my-loans', name: 'app_my_loans')]
    public function loans(LoanRepository $loanRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $loans = $loanRepo->findBy([
            'borrower' => $user,
            'return_date' => null
        ]);

        return $this->render('user/loans.html.twig', [
            'loans' => $loans,
        ]);
    }

}
