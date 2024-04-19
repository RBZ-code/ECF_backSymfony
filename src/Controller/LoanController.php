<?php

namespace App\Controller;

use DateTime;
use App\Entity\Loan;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/confirm-reservation/{bookId}', name: 'app_confirm_reservation')]
    public function confirmReservation(int $bookId, Request $request, UserRepository $userRepo, BookRepository $bookRepo, EntityManagerInterface $entityManager): Response
    {
        // $userId = $request->getSession()->get('user_id');

        $userId = 1;

        if (!$userId) {
            return new JsonResponse(['error' => 'Unauthorized access'], 401);
        }

        $user = $userRepo->findOneBy(['id' => $userId]);
        $book = $bookRepo->findOneBy(['id' => $bookId]);

        dd($user);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        // Create a new Loan entity
        $loan = new Loan();
        $loan->setStartDate(new DateTime());
        $loan->setEndDate((new DateTime())->modify('+6 days'));
        $loan->setBook($book);
        $loan->setBorrower($user);

        // Persist the new loan record in the database
        // $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($loan);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }
    
}
