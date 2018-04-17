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
     *
     * @return mixed
     */
    public function findBetween($start, $end, $roomId)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date > :start')
            ->andWhere('r.date < :end')
            ->andWhere('r.room = :roomId')
            ->andWhere('r.state LIKE :state')
            ->setParameter('start', $start->format('Y-m-d 00:00:00'))
            ->setParameter('end', $end->format('Y-m-d 23:59:59'))
            ->setParameter('roomId', $roomId)
            ->setParameter('state', '%accepted%')
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
            ->orWhere('r.state LIKE :cancelled')
            ->setParameter('cancelled', '%cancelled%')
            ->andWhere('r.state NOT LIKE :cancelled_ok')
            ->setParameter('cancelled_ok', '%cancelled_ok%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des réservations d'un utilisateur.
     *
     * @param $user
     *
     * @return mixed
     */
    public function findInProgressUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :created')
            ->setParameter('created', '%created%')
            ->orWhere('r.state LIKE :accepted')
            ->setParameter('accepted', '%accepted%')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne la liste des reservations qui sont a l'état de création 36h avant la date d'écheance.
     */
    public function findWarningReservation()
    {
        $echeance = new \DateTime();
        $limite = new \DateTime();
        $echeance->modify('+36 hours');
        $limite->modify('+12 hours');

        return $this->createQueryBuilder('r')
            ->where('r.start < :start')
            ->andWhere('r.start > :limite')
            ->andWhere('r.state LIKE :state')
            ->setParameter('start', $echeance->format('Y-m-d H:m:s'))
            ->setParameter('limite', $limite->format('Y-m-d H:m:s'))
            ->setParameter('state', '%created%')
            ->getQuery()
            ->getResult();
    }

    public function findReservationNotComplete()
    {
        $echeance = new \DateTime();
        $echeance->modify('+12 hours');

        return $this->createQueryBuilder('r')
            ->where('r.start < :start')
            ->andWhere('r.state LIKE :state')
            ->setParameter('start', $echeance->format('Y-m-d H:m:s'))
            ->setParameter('state', '%created%')
            ->getQuery()
            ->getResult();
    }
}
