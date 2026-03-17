<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Reservation;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findAvailableRooms(\DateTimeInterface $date_arrived, \DateTimeInterface $date_return): array
    {   
        $subQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('IDENTITY(res.room)')
            ->from(Reservation::class, 'res')
            ->where('res.status = :status')
            ->andWhere('res.date_arrived < :date_return')
            ->andWhere('res.date_return > :date_arrived')
            ->getDQL();

        return $this->createQueryBuilder('r')
            ->where('r.id NOT IN (' . $subQuery . ')')
            ->setParameter('date_arrived', $date_arrived)
            ->setParameter('date_return', $date_return)
            ->setParameter('status', 'paid')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Room[] Returns an array of Room objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Room
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
