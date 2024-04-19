<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Repository\EquipmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
}
