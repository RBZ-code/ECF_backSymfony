<?php

namespace App\Repository;

use DateTime;
use App\Entity\Room;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findReservationsByRoomAndHour(Room $room, DateTime $hour): int
    {
        $startHour = clone $hour;
        $endHour = clone $hour;
        $endHour->modify('+1 hour'); 

        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.idRoom = :room')
            ->andWhere(':startHour < r.end_date') 
            ->andWhere(':endHour > r.start_date') 
            ->setParameter('room', $room)
            ->setParameter('startHour', $startHour)
            ->setParameter('endHour', $endHour)
            ->getQuery()
            ->getSingleScalarResult(); // Récupère le résultat sous forme nombre total de réservations
    }

    public function findReservationsByUserAndDate(Room $room, DateTime $selectedDate, Utilisateur $user): array
{
    return $this->createQueryBuilder('r')
        ->select('r')
        ->where('r.idRoom = :room')
        ->andWhere('r.start_date = :selectedDate')
        ->andWhere('r.User = :user') 
        ->setParameter('room', $room)
        ->setParameter('selectedDate', $selectedDate) 
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}
}