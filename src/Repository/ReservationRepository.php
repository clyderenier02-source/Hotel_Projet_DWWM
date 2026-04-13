<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findActiveReservationForUser(User $user): ?Reservation
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $now->setTime(0, 0, 0);

        return $this->createQueryBuilder('r')
            ->leftJoin('r.opinion', 'o')
            ->where('r.user = :user')
            ->andWhere('r.date_arrived <= :now')
            ->andWhere('r.date_return >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', $now, \Doctrine\DBAL\Types\Types::DATE_MUTABLE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
