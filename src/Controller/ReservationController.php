<?php

namespace App\Controller;

// Dans ReservationController.php

use App\Entity\Room;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    #[Route('/heureReservation/{id}', name: 'heure_reservation')]
    public function reservation(Room $room, Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, Security $security): Response
    {
       
        $selectedDate = $request->query->get('selectedDate');
        //dd($selectedDate);

        $currentDate = new \DateTime();
        $currentDate->modify('+ 2hour');
        //dd($currentDate);

        $currentHour = (new \DateTime())->format('H');
        $currentHour = (new \DateTime())->modify('+ 2hour')->format('H');
        //dd($currentHour);


        $reservation = new Reservation();
        $user = $security->getUser();
        //dd($user);


        $reservation->setIdRoom($room);

        $reservation->setUser($user);


        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($reservation->getStartDate() < $currentDate) {
                $this->addFlash('danger', 'La date de réservation doit être postérieure à la date du jour');
                return $this->redirectToRoute('app_reservation');
            }
            if (!$user) {
                $this->addFlash('danger', 'Vous devez être connecté pour réserver une salle');
                return $this->redirectToRoute('app_login');
            }
            else{
                $entityManager->persist($reservation);
                $entityManager->flush();
            }

            return $this->render('reservation/confirmReservation.html.twig', [
                'reservation' => $reservation,
            ]);
        }

        $hoursOfDay = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $hoursOfDay[] = sprintf('%02d:00', $hour); // Formatage de l'heure
        }

        $fullHours = [];

        // Vérifier pour chaque heure si le total des réservations atteint la capacité de la salle
        foreach ($hoursOfDay as $hour) {
            $selectedDateTime = new \DateTime($selectedDate . ' ' . $hour);
            $selectedDateTime->modify('+ 3hour');
            //dd($selectedDateTime);
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
            'current_date' => $currentDate,
            'form' => $form->createView(), 
        ]);
    }

   
    
}
