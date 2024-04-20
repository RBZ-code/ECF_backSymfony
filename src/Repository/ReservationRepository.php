<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\Reservation;
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

    public function findReservationsByRoomAndHour(Room $room, \DateTimeInterface $hour): array
{
    $startHour = clone $hour;
    $endHour = clone $hour;
   

    return $this->createQueryBuilder('r')
        ->where('r.idRoom = :room')
        ->andWhere(':startHour < r.end_date') // Vérifiez si l'heure de début de la réservation est après l'heure fournie
        ->andWhere(':endHour > r.start_date') // Vérifiez si l'heure de fin de la réservation est avant l'heure fournie
        ->setParameter('room', $room)
        ->setParameter('startHour', $startHour)
        ->setParameter('endHour', $endHour)
        ->getQuery()
        ->getResult();
}
 
}
