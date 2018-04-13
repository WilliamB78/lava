<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * @Route("/index", name="index")
     *
     */
    //@Security("has_role('ROLE_USER')")
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function navUser()
    {
        dump($this->getUser());
        $mesReservations = $this
            ->getDoctrine()
            ->getRepository(Reservation::class)
            ->findUserReversationByState($this->getUser(), 'accepted');

        return $this->render('navbar/navbar-user.html.twig', [
            'mesReservations' => $mesReservations,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function navSecretary()
    {
        $countHS = $this
            ->getDoctrine()
            ->getRepository(Room::class)
            ->findTotalRoomHS();

        $demReservation = $this
            ->getDoctrine()
            ->getRepository(Reservation::class)
            ->findByState('created');

        return $this->render('navbar/navbar-secretary.html.twig', [
            'countHS' => $countHS,
            'demReservation' => $demReservation,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function navAdmin()
    {
        return $this->render('navbar/navbar-admin.html.twig');
    }

    /**
     * Show user content for ROLE_ADMIN
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function IndexContentAdmin()
    {
        $userRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);

        $last5User = $userRepository->findLast5User();
        $last5BlockedUser = $userRepository->findLast5BlockedUser();

        return $this->render('index/content/admin.html.twig', [
            'last5User' => $last5User,
            'last5BlockedUser' => $last5BlockedUser
        ]);
    }

    /**
     * Show user content for ROLE_SECRETARY
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function IndexContentSecretary()
    {
        $reservationRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Reservation::class);

        $roomRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Room::class);

        return $this->render('index/content/secretary.html.twig');
    }

    public function IndexContentUser()
    {
        $reservationRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Reservation::class);

        $userReservation = $reservationRepository->findInProgressUser($this->getUser());
        $userReservationAccepted = $reservationRepository->findByState('accepted');

        return $this->render('index/content/user.html.twig', [
            'userReservation' => $userReservation,
            'userReservationAccepted' => $userReservationAccepted
        ]);
    }
}
