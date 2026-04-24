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
        // Date du jour a minuit heure de Paris
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $now->setTime(0, 0, 0);

        return $this->createQueryBuilder('r')
            // même si avis null on retourne la réservation a l'utilisateur
            ->leftJoin('r.opinion', 'o')
            ->where('r.user = :user')
            // si séjour en cours date d'arrivée passée et date retour pas encore passé peut mettre un avis
            ->andWhere('r.date_arrived <= :now')
            ->andWhere('r.date_return >= :now')
            ->setParameter('user', $user)
            // Remplace :now par la valeur de $now, en précisant que c'est une date
            ->setParameter('now', $now, \Doctrine\DBAL\Types\Types::DATE_MUTABLE)
            // on limite à 1 le résultat exécute la requête et retourne la réservation ou null
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
