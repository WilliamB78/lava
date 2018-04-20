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
    public function countByState($state)
    {
        try {
            return $this->createQueryBuilder('r')
                ->select('COUNT(r)')
                ->where('r.state LIKE :state')
                ->setParameter('state', "%$state%")
                ->orderBy('r.start', 'DESC')
                ->getQuery()
                ->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return 0;
            }
    }

    public function findByState($state)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.state LIKE :state')
            ->setParameter('state', "%$state%")
            ->orderBy('r.start', 'DESC')
            ->getQuery()
            ->getResult();
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
    public function findReservationBetweenDate($start, $end, $roomId)
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

    public function findReservationBetweenTime($start, $end, $roomId, $date)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date = :date')
            ->andWhere('r.start < :end')
           ->andWhere('r.end > :start')
            ->andWhere('r.room = :roomId')
            ->andWhere('r.state LIKE :state')
            ->setParameter('date', $date)
            ->setParameter('start', $start)
           ->setParameter('end', $end)
            ->setParameter('roomId', $roomId)
            ->setParameter('state', '%accepted%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $start
     * @param $end
     * @param $roomId
     * @param $date
     * @return mixed
     */
    public function findReservationStartTimeAtDate($start, $roomId, $date)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date = :date')
            ->andWhere('r.start = :start')
            ->andWhere('r.room = :roomId')
            ->andWhere('r.state LIKE :accepted')
            ->setParameter('date', $date)
            ->setParameter('start', $start)
            ->setParameter('roomId', $roomId)
            ->setParameter('accepted', '%accepted%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $start
     * @param $end
     * @param $roomId
     * @param $date
     * @return mixed
     */
    public function findReservationEndTimeAtDate($end, $roomId, $date)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date = :date')
            ->andWhere('r.end = :end')
            ->andWhere('r.room = :roomId')
            ->andWhere('r.state LIKE :accepted')
            ->setParameter('date', $date)
            ->setParameter('end', $end)
            ->setParameter('roomId', $roomId)
            ->setParameter('accepted', '%accepted%')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return mixed
     */
    public function findCreatedReservations()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :created')
            ->setParameter('created', '%created%')
            ->orderBy('r.start', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findAcceptedRequest()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :accepted')
            ->setParameter('accepted', '%accepted%')
            ->orderBy('r.start', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findRefusedRequest()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :refused')
            ->setParameter('refused', '%refused%')
            ->orderBy('r.start', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function findCancelRequest()
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :cancelled')
            ->setParameter('cancelled', '%cancelled%')
            ->orderBy('r.start', 'desc')
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
            ->orderBy('r.start', 'desc')
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
            ->orderBy('r.start', 'DESC')
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
    public function findCreatedByUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :created')
            ->setParameter('created', '%created%')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.start', 'DESC')
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
    public function findAcceptedRequestByUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :accepted')
            ->setParameter('accepted', '%accepted%')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.start', 'DESC')
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
    public function findCancelRequestByUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.state LIKE :cancelled')
            ->setParameter('cancelled', '%cancelled%')
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.start', 'DESC')
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
