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

    // retourne les chambres disponibles sur la période de date demandé
    public function findAvailableRooms(\DateTimeInterface $date_arrived, \DateTimeInterface $date_return): array
    {   
        // sous requête : je récupère les ID des chambres déja réservé pour les dates demandé
        $subQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('IDENTITY(res.room)') // je récupère ID de la room
            ->from(Reservation::class, 'res') // dans l'entité réservtions
            ->where('res.status = :status') // que les réservation payé
            ->andWhere('res.date_arrived < :date_return') // la résa commence avant la fin de la période
            ->andWhere('res.date_return > :date_arrived') // La résa se termine après le début de la période
            ->getDQL(); // retourne la requête en DQL sans l'exécuter

        // requête principale : retourne ID des chambres qui ne sont pas dans la sous requête
        return $this->createQueryBuilder('r')
            ->where('r.id NOT IN (' . $subQuery . ')')
            ->setParameter('date_arrived', $date_arrived)
            ->setParameter('date_return', $date_return)
            ->setParameter('status', 'paid') // on exclut uniquement les réservations payées
            ->getQuery()
            ->getResult();
    }
}
