<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    public function navUser()
    {
        $mesReservations = $this
            ->getDoctrine()
            ->getRepository(Reservation::class)
            ->findUserReversationByState($this->getUser(), 'accepted');

        return $this->render('navbar/navbar-user.html.twig', [
            'mesReservations' => $mesReservations,
        ]);
    }

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

    public function navAdmin()
    {
        return $this->render('navbar/navbar-admin.html.twig');
    }
}
