<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/book')]
class AdminBookController extends AbstractController
{
    #[Route('/', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {

  
   if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }

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
                        }
                    }
                }
            }
        }


        return $this->render('admin_book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }
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
    public function show(Book $book): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('admin_book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }
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
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
