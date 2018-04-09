<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Permet d'avoir le nombre de reservation pour un utilisateur.
     *
     * @param $user
     * @param $state
     *
     * @return int|mixed
     */
    public function findUserReversationByState($user, $state)
    {
        try {
            return $this->byState($state)
                ->andWhere('r.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Retourne le nombre de reservation par etat.
     *
     * @param $state
     *
     * @return int|mixed
     */
    public function findByState($state)
    {
        try {
            return $this->byState($state)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Creation d'une query vis a vis d'un state.
     *
     * @param $state
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function byState($state)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.state LIKE :state')
            ->setParameter('state', "%$state%");
    }

    /**
     * @param $start
     * @param $end
     * @param $roomId
     * @return mixed
     */
    public function findBetween($start, $end, $roomId)
    {
        return $this->createQueryBuilder('r')
            ->where('r.start > :start')
            ->andWhere('r.end < :end')
            ->andWhere('r.room = :roomId')
            ->setParameter('start', $start->format('Y-m-d 00:00:00'))
            ->setParameter('end', $end->format('Y-m-d 23:59:59'))
            ->setParameter('roomId', $roomId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findInProgress()
    {
        return $this->createQueryBuilder('r')
           ->where('r.state LIKE :created')
           ->setParameter('created', '%created%')
           ->andWhere('r.state LIKE :cancelled')
           ->setParameter('cancelled', '%cancelled%')
           ->getQuery()
           ->getResult();
    }

    /**
     * Retourne la liste des réservations d'un utilisateur
     * @param $user
     * @return mixed
     */
    public function findInProgressUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :created')
            ->setParameter('created', '%created%')
            ->andWhere('r.state LIKE :accepted')
            ->setParameter('accepted' , '%accepted%')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

    }
}
