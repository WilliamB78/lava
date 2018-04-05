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
     * Permet d'avoir le nombre de reservation pour un utilisateur
     * @param $user
     * @param $state
     * @return int|mixed
     */
    public function findUserReversationByState($user,$state)
    {

        try {
            return $this->byState($state)
                ->andWhere('r.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getSingleScalarResult();
        }catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Retourne le nombre de reservation par etat
     * @param $state
     * @return int|mixed
     */
    public function findByState($state)
    {
        try {
            return $this->byState($state)
                ->getQuery()
                ->getSingleScalarResult();
        }catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    /**
     * Creation d'une query vis a vis d'un state
     * @param $state
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function byState($state)
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.state LIKE :state')
            ->setParameter('state', "%$state%");
    }
}
