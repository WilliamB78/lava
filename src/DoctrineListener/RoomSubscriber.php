<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 23/04/2018
 * Time: 11:43
 */

namespace App\DoctrineListener;


use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\RoomMail;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class RoomSubscriber implements EventSubscriber
{

    protected $roomMailer;

    public function __construct(RoomMail $roomMailer)
    {
        $this->roomMailer = $roomMailer;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate'
        ];
    }

    /**
     * @param PreUpdateEventArgs $args
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        /** @var User $entity */
        $entity = $args->getEntity();
        if (!$entity instanceof  Room) {
            return;
        }

        if ($args->hasChangedField('state')) {
            // if state == true
            if($entity->getState()) {
                $this->roomDisabled($args);
            } else {
                $this->roomEnabled($args);
            }
        }
    }

    /**
     * @param $args PreUpdateEventArgs
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function roomEnabled($args)
    {
        /** @var ReservationRepository $userRoom */
        $reservationRepository = $args->getEntityManager()->getRepository(Reservation::class);
        // Va chercher toutes les reservations d'une salle a partir d'une date
        $reservations = $reservationRepository->findRoomReservationWithDate($args->getEntity(), new \DateTime());
        /** @var Reservation $reservation */
        foreach ($reservations as $reservation) {
            /** @var User $userReservations */
            $userReservations = $reservationRepository->findUserReservationsInDate($reservation->getUser(),new \DateTime(), $args->getEntity());
            $this->roomMailer->roomEnabled($reservation->getUser(), $args->getEntity(), $userReservations);
        }
    }

    /**
     * @param $args PreUpdateEventArgs
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function roomDisabled($args)
    {
        /** @var ReservationRepository $userRoom */
        $reservationRepository = $args->getEntityManager()->getRepository(Reservation::class);
        // Va chercher toutes les reservations d'une salle a partir d'une date
        // Va chercher tous les utilisateurs
        $users = $reservationRepository->findRoomReservationWithDate($args->getEntity() , new \DateTime());
        /** @var Reservation $reservation */
        foreach ($users as $user) {
            //dump($user);die;
            /** @var User $userReservations */
            $userReservations = $reservationRepository->findUserReservationsInDate($user,new \DateTime(), $args->getEntity());
            $user = $args->getEntityManager()->getRepository(User::class)->find($user[1]);
            $this->roomMailer->roomDisabled($user, $args->getEntity(), $userReservations);
        }
    }
}