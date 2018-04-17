<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * Retourne le nombre total de salle créé.
     *
     * @return int|mixed
     */
    public function findTotalRoom()
    {
        try {
            return $this->createQueryBuilder('r')
                ->select('COUNT(r)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Retourne le nombre.
     *
     * @return int|mixed
     */
    public function findTotalRoomHS()
    {
        try {
            return $this->createQueryBuilder('r')
                ->select('COUNT(r)')
                ->where('r.state = :state')
                ->setParameter('state', 1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Retourne un array de salles ouvertes.
     *
     * @return int|mixed
     */
    public function findTotalRoomOpen()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state = :state')
            ->setParameter('state', 0)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne un array de salles fermés.
     *
     * @return int|mixed
     */
    public function findTotalRoomClosed()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state = :state')
            ->setParameter('state', 1)
            ->getQuery()
            ->getResult();
    }
}
