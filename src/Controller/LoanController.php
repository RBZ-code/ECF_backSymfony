<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoanController extends AbstractController
{
    #[Route('/loan/{id}', name: 'app_loan')]
    public function index(int $id, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->findOneBy(['id'=>$id]);

        return $this->render('loan/index.html.twig', [
            'book' => $book,
        ]);
    }
}
