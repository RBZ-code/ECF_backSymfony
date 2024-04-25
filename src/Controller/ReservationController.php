<?php

namespace App\Controller;

// Dans ReservationController.php
use DateTime;
use DateInterval;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class ReservationController extends AbstractController
{
    #[Route('/heureReservation/{id}', name: 'heure_reservation')]
    public function reservation(Room $room, Request $request, ReservationRepository $reservationRepository, EntityManagerInterface $entityManager, Security $security, TranslatorInterface $translator): Response
    {

        if (!$this->isGranted('ROLE_USER')) {


            $message = $translator->trans('You must be logged in to book a room');
            $this->addFlash(
                'danger',
                $message
            );
            return $this->redirectToRoute('app_login');
        }
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
        if (!$user) {

            $message = $translator->trans('You must be logged in to book a room');
            $this->addFlash(
                'danger',
                $message
            );
            return $this->redirectToRoute('app_login');
        }


        $reservation->setIdRoom($room);

        $reservation->setUser($user);


        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $selectedDateTime = $reservation->getStartDate();
            $existingReservation = $reservationRepository->findExistingReservation($room, $selectedDateTime, $security->getUser());

            if ($existingReservation) {
                
                $message = $translator->trans('You already have a reservation for this date and time.');
                $this->addFlash('danger', $message);
                return $this->redirectToRoute('heure_reservation', ['id' => $room->getId()]);
            }

            if ($reservation->getStartDate() < $currentDate) {

                $message = $translator->trans('The reservation date must be after the current date');
                $this->addFlash('danger', $message);
                return $this->redirectToRoute('app_reservation');
            }



            $chosenDuration = $request->request->get('choixEndDate');
            //dd($chosenDuration);
            $startDate = $reservation->getStartDate();
            $endDate = new \DateTime();
            $endDate = clone $startDate;
            //dd($endDate);
            $endDate->modify('+' . $chosenDuration . ' hours');
            //dd($endDate);
            $reservation->setEndDate($endDate);

            if ($endDate->format('H') >= 20) {


                $message = $translator->trans('The reservation cannot end after the room closes (8:00 PM)');
                $this->addFlash('danger', $message);
                return $this->redirectToRoute('heure_reservation', ['id' => $room->getId()]);
            }


            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->render('reservation/confirmReservation.html.twig', [
                'reservation' => $reservation,
            ]);
        }




        $hoursOfDay = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $hoursOfDay[] = sprintf('%02d:00', $hour); // Formatage de l'heure
        }

        $fullHours = [];

        $selectedDates = new \DateTime($selectedDate);
        //dd($selectedDates);
        //dd($room);
        //dd($security->getUser());
        //dd($selectedDates); 


        // system Indien !
        $userReservations = $reservationRepository->findReservationsByUserAndDate($room, $selectedDates, $security->getUser());
        // dd($selectedDates);

        // dd($userReservations);



        $userHasReservations = [];
        foreach ($hoursOfDay as $hour) {
            //dd($hour);
            $selectedDateTime = new \DateTime($selectedDate . ' ' . $hour);
            //dd($selectedDateTime);

            // Vérifier si l'une des réservations de l'utilisateur chevauche cette heure
            foreach ($userReservations as $userReservation) {
                if ($userReservation->getStartDate() <= $selectedDateTime && $userReservation->getEndDate() > $selectedDateTime) {
                    $userHasReservations[$hour] = true;
                    break;
                }
            }
        }
        // dd($userHasReservations);

        // Vérification pour chaque heure si le total des réservations atteint la capacité de la salle
        foreach ($hoursOfDay as $hour) {
            $selectedDateTime = new \DateTime($selectedDate . ' ' . $hour);
            // $selectedDateTime->modify('+ 2hour');
            // dd($selectedDateTime);
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
            'userHasReservations' => $userHasReservations,
        ]);
    }
}
