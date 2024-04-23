<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/room')]
class AdminRoomController extends AbstractController
{
    #[Route('/', name: 'app_admin_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('admin_room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_room/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        $reservations = $room->getReservations();

    // Fetch related user information for each reservation
    $reservationEvents = [];
    foreach ($reservations as $reservation) {
        $reservationEvent = [
            'id' => $reservation->getId(), // Unique identifier for the event
            'title' => $reservation->getUser() ? $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName() : 'Unknown User', // Display user name
            'start' => $reservation->getStartDate()->format('Y-m-d\TH:i:s'), // Start date and time in ISO 8601 format
            'end' => $reservation->getEndDate()->format('Y-m-d\TH:i:s'), // End date and time in ISO 8601 format
        ];
        $reservationEvents[] = $reservationEvent;
    }
    // dd($reservationEvents);

    return $this->render('admin_room/show.html.twig', [
        'room' => $room,
        'reservations' => $reservations,
        'reservationEvents' => $reservationEvents, // Pass reservation event data to the template
    ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $room->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
    }
}
