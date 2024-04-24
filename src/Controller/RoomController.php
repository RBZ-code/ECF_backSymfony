<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\EquipmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoomController extends AbstractController
{
    #[Route('/room', name: 'app_room')]
    public function index(RoomRepository $roomRepository, EquipmentRepository $equipmentRepository): Response
    {
        $availableRooms = $roomRepository->findAvailableRooms();

        // Récupérer les équipements pour chaque salle
        $equipmentsByRoom = [];
        foreach ($availableRooms as $room) {
            $equipments = $equipmentRepository->findEquipmentsByRoom($room);
            $equipmentsByRoom[$room->getId()] = $equipments;
        }

        return $this->render('room/index.html.twig', [
            'availableRooms' => $availableRooms,
            'equipmentsByRoom' => $equipmentsByRoom,
        ]);
    }


    #[Route('/reservation/{id}', name: 'app_reservation')]
    public function reservation(Room $room,): Response
    {
        //dd($room);
    
        return $this->render('room/calendrier.html.twig', [
            'room' => $room,
        ]);
    }
}
