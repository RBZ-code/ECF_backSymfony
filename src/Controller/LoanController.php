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
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoanController extends AbstractController
{

    #[Route('/confirm-reservation/{bookId}', name: 'app_confirm_reservation')]
    public function confirmReservation(int $bookId, Request $request, UserRepository $userRepo, BookRepository $bookRepo, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_USER')) {

            $message = $translator->trans('You must be logged in to reserve a book.');
            $this->addFlash(
                'danger',
                $message
            );
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized access'], 401);
        }

        $book = $bookRepo->findOneBy(['id' => $bookId]);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        if (!$book->isAvailable()) {
            $this->addFlash('warning', 'Ce livre est déjà réservé; il n\'est pas disponible.');
            return $this->redirectToRoute('show_books');
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
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }

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

    #[Route('/confirm-return/{bookId}', name: 'app_confirm_return')]
    public function confirmReturn(int $bookId, LoanRepository $loanRepo, BookRepository $bookRepo, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }

        $book = $bookRepo->findOneBy(['id' => $bookId]);
        $loan = $book->getLoans();

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        $loans = $loanRepo->findBy(['book' => $book]);

        foreach ($loans as $loan) {
            if ($loan->getReturnDate() === null) {
                $returnDate = new DateTime();
                $loan->setReturnDate($returnDate);
    
                // Update book availability to true
                $book->setAvailable(true);
                $book->setOverdue(false);
    
                // Persist changes for the loan and book entities
                $entityManager->persist($loan);
                $entityManager->persist($book);
                $entityManager->flush();
    
                return $this->redirectToRoute('app_admin_book_index');
            }
        }
        return new JsonResponse(['error' => 'No unreturned loan found for this book'], 404);
    }

}
