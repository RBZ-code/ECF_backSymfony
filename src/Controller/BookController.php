<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(BookRepository $bookRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $library = $bookRepo->findAll();
        // $library = $bookRepo->findAvailable();

        $pagination = $paginator->paginate(
            $library,
            $request->query->getInt('page', 1),
            40
        );
        

        return $this->render('book/index.html.twig', [
            'library' => $library,
            'pagination' => $pagination
        ]);
    }

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, BookRepository $bookRepo): Response
    {
        $book = $bookRepo->findOneBy(['id'=>$id]);
        

        return $this->render('book/details.html.twig', [
            'book' => $book,

        ]);
    }
}
