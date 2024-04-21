<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\EquipmentRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function test(Room $room, ReservationRepository $reservationRepository): Response
    {
        // Récupérer la date actuelle
        $currentDate = new \DateTime();
    
        // Récupérer toutes les heures possibles dans une journée
        $hoursOfDay = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hoursOfDay[] = sprintf('%02d:00', $hour); // Formatage de l'heure
        }
    
        // Tableau pour stocker les heures pleines
        $fullHours = [];
    
        // Vérifier pour chaque heure si le total des réservations atteint la capacité de la salle
        foreach ($hoursOfDay as $hour) {
            // Créer une instance de DateTime pour cette heure
            $hourDateTime = \DateTime::createFromFormat('H:i', $hour);
            
            // Récupérer le nombre total de réservations pour cette salle à cette heure
            $totalReservationsAtHour = $reservationRepository->findReservationsByRoomAndHour($room, $hourDateTime);
            
            // Vérifier si le nombre total de réservations pour cette heure atteint la capacité de la salle
            if ($totalReservationsAtHour >= $room->getCapacity()) {
                $fullHours[] = $hour;
            }
        }
        //dd($fullHours);
    
        return $this->render('room/calendrier.html.twig', [
            'unavailable_hours' => $fullHours
        ]);
    }
}
