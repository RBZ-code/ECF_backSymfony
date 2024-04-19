<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\Equipment;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Equipment>
 *
 * @method Equipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipment[]    findAll()
 * @method Equipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    public function findEquipmentsByRoom(Room $room): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.room', 'r')
            ->where('r.id = :roomId')
            ->setParameter('roomId', $room->getId())
            ->getQuery()
            ->getResult();
    }
}
