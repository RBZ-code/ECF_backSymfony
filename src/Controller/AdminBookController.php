<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Loan;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/book')]
class AdminBookController extends AbstractController
{
    #[Route('/', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {

        $books = $bookRepository->findAll();
        $currentLoan = null;

        foreach ($books as $book) {
            if (!$book->isAvailable()) {
                $loans = $book->getLoans();
                foreach ($loans as $loan) {
                    if (!$loan->getReturnDate()) {
                        $currentLoan = $loan;

                        if ($currentLoan) {
                            $endDate = $currentLoan->getEndDate();
                            $extensionDate = $currentLoan->getExtensionDate();

                            if ($extensionDate && new \DateTime() > $extensionDate) {
                                $book->setOverdue(true);
                            } else if (!$extensionDate && new \DateTime() > $endDate) {
                                $book->setOverdue(true);
                            }

                            $entityManager->persist($book);
                            $entityManager->flush();
                        }
                    }
                }
            } else {
                $book->setOverdue(false);

                $entityManager->persist($book);
                $entityManager->flush();

            }
        }

        return $this->render('admin_book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/overdue', name: 'app_admin_book_overdue', methods: ['GET'])]
    public function showOverdueBooks(BookRepository $bookRepository): Response
    {
        $overdueBooks = $bookRepository->findOverdueBooks();

        return $this->render('admin_book/index.html.twig', [
            'books' => $overdueBooks,
            'showOnlyOverdue' => true, // Flag to control display
        ]);
    }

    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_book_show', methods: ['GET'])]
    public function show(int $id, BookRepository $bookRepo, LoanRepository $loanRepo, Book $book): Response
    {
        $book = $bookRepo->findOneBy(['id' => $id]);
        $loans = $loanRepo->findLoansByBookOrderedByStartDateDesc($book);

        return $this->render('admin_book/show.html.twig', [
            'book' => $book,
            'loans' => $loans,
        ]);
    }

    #[Route('/{id}/loan', name: 'update_loan_comment', methods: ['GET', 'POST'])]
    public function updateLoanComment(int $id, Request $request, LoanRepository $loanRepo, EntityManagerInterface $entityManager): Response
    {
        $comment = $request->request->get('comment');

        $loan = $loanRepo->findOneBy(['id' => $id]);

        $loan->setAdminComment($comment);

        $entityManager->persist($loan);
        // dd($loan);

        $entityManager->flush();

        return $this->redirectToRoute('app_admin_book_show', ['id' => $loan->getBook()->getId()]); 

    }

    #[Route('/{id}/edit', name: 'app_admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
