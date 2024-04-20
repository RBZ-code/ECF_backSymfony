<?php

namespace App\Controller;

use DateTime;
use App\Entity\Loan;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoanController extends AbstractController
{

    #[Route('/confirm-reservation/{bookId}', name: 'app_confirm_reservation')]
    public function confirmReservation(int $bookId, Request $request, UserRepository $userRepo, BookRepository $bookRepo, EntityManagerInterface $entityManager): Response
    {
        // Get the logged-in user using Symfony's getUser() method
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized access'], 401);
        }

        $book = $bookRepo->findOneBy(['id' => $bookId]);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        // Create a new Loan entity
        $loan = new Loan();
        $loan->setStartDate(new DateTime());
        $loan->setEndDate((new DateTime())->modify('+6 days'));
        $loan->setBook($book);
        $loan->setBorrower($user); // Set the logged-in user as the borrower

        // Persist the new loan record in the database
        $entityManager->persist($loan);

        $book->setAvailable(false);

        $entityManager->flush();

        return $this->redirectToRoute('app_library');
    }

    #[Route('/extend-loan/{loanId}', name: 'app_extend_loan')]
    public function extendLoan(int $loanId, LoanRepository $loanRepo, EntityManagerInterface $entityManager): Response
    {
        $loan = $loanRepo->findOneBy(['id' => $loanId]);

        if (!$loan) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        $endDate = $loan->getEndDate();
        $endDate->modify('+6 days');
        // Question : Why didn't it work to just use setEndDate() to modify my end_date ??
        $loan->setExtensionDate($endDate);
        $loan->setExtension(true);

        // dd($loan);

        $entityManager->persist($loan);
        $entityManager->flush();

        return $this->redirectToRoute('app_my_loans');
    }
}
