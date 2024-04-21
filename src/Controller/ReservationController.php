<?php

namespace App\Controller;

// Dans ReservationController.php

use App\Entity\Room;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'form_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($reservation);
            $entityManager->flush();

            
            return $this->render('reservation/confirmReservation.html.twig', [
                'reservation' => $reservation,
            ]);
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/heureReservation/{id}', name: 'heure_reservation')]
    public function reservation(Room $room, Request $request, ReservationRepository $reservationRepository): Response
    {
        // Récupérer la date sélectionnée depuis la requête
        $selectedDate = $request->query->get('selectedDate');
        //dd($selectedDate);

        
        $currentDate = new \DateTime();
        $currentHour = date('H');

       
        $hoursOfDay = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $hoursOfDay[] = sprintf('%02d:00', $hour); // Formatage de l'heure
        }

       
        $fullHours = [];

        
        // Vérifier pour chaque heure si le total des réservations atteint la capacité de la salle
        foreach ($hoursOfDay as $hour) {
            
            $selectedDateTime = new \DateTime($selectedDate . ' ' . $hour);

            
            $totalReservationsAtHour = $reservationRepository->findReservationsByRoomAndHour($room, $selectedDateTime);

            
            if ($totalReservationsAtHour >= $room->getCapacity()) {
                $fullHours[] = $hour;

            }
        }
        //dd( $fullHours);

        return $this->render('reservation/heureReservation.html.twig', [
            'unavailable_hours' => $fullHours,
            'selectedDate' => $selectedDate, 
            'room' => $room,
            'current_hour' => $currentHour,
            'current_date' => $currentDate
        ]);
    }
}
