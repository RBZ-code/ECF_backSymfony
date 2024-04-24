<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/room')]
class AdminRoomController extends AbstractController
{
    #[Route('/', name: 'app_admin_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('admin_room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),

        ]);
    }

    #[Route('/new', name: 'app_admin_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            $message = $translator->trans('Room successfully created.');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_room/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_room_show', methods: ['GET'])]
    public function show(RoomRepository $roomrepo, Room $room ): Response
    {

        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('app_home');
        // }

        //  $reservations = $room->getReservations();

        // $reservationEvents = [];
        // foreach ($reservations as $reservation) {
        //     $reservationEvent = [
        //         'id' => $reservation->getId(),
        //         'title' => $reservation->getUser() ? $reservation->getUser()->getFirstName() . ' ' . $reservation->getUser()->getLastName() : 'Unknown User',
        //         'start' => $reservation->getStartDate()->format('Y-m-d\TH:i:s'),
        //         'end' => $reservation->getEndDate()->format('Y-m-d\TH:i:s'),
        //     ];
        //     $reservationEvents[] = $reservationEvent;
        // }
        // // dd($reservationEvents);


        // $data = json_encode($reservationEvents);
        $roomReservations = $roomrepo->findAll();

        return $this->render('admin_room/show.html.twig', [
            'roomrepo' => $roomReservations,
            'room' => $room,
            // 'reservations' => $reservations,
            // 'reservationEvents' => $data, // Pass reservation event data to the template
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_room_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $message = $translator->trans('Room successfully updated.');
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {


        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }


        $reservations = $room->getReservations();
        
        foreach ($reservations as $reservation) {
            if ($reservation->getStartDate() > new \DateTime()) {

                $message = $translator->trans('There are reservations linked to this room. Delete them first.');
                $this->addFlash('danger', $message);
                //dd($reservation);
                return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
            }
            else {
                $reservation->setIdRoom(null);
                // dd($reservation);
            }
        }

        if ($this->isCsrfTokenValid('delete' . $room->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
            $message = $translator->trans('Room successfully deleted.');
            $this->addFlash('danger', $message);
            return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_admin_room_index', [], Response::HTTP_SEE_OTHER);
    }
}
