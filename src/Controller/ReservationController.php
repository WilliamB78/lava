<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * @Route("/reservation", name="reservation_")
 */
class ReservationController extends Controller
{
    /**
     * @Route("/", name="index", methods="GET")
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     *
     * @param ReservationRepository $reservationRepository
     *
     * @return Response
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', ['reservations' => $reservationRepository->findAll()]);
    }

    /**
     * @Route("/mes-reservations", name="mes_reservations", methods={"GET"})
     */
    public function mesReservations()
    {
        $reservations = $this
            ->getDoctrine()
            ->getRepository(Reservation::class)
            ->findBy(['user' => $this->getUser()]);

        return $this->render('reservation/mes-reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/{id}/new/{date}", name="new", methods="GET|POST")
     *
     * @param Request  $request
     * @param Registry $workflows
     * @param Room     $room
     * @param $date
     *
     * @return Response
     */
    public function new(Request $request, Registry $workflows, Room $room, $date): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->add('start', DateTimeType::class, array(
            'data' => new \DateTime($date),
        ));
        $form->add('end', DateTimeType::class, array(
            'data' => new \DateTime($date),
        ));
        $form->handleRequest($request);
        $workflow = $workflows->get($reservation);

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO setState
            //TODO setUser
            $reservation->setRoom($room);
            $reservation->setUser($this->getUser());
            $reservation->setState('created');
            //dump($reservation);exit;
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('room_calendar_show', array('id' => $room->getId()));
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods="GET")
     *
     * @param Reservation $reservation
     *
     * @return Response
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', ['reservation' => $reservation]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     *
     * @param Request     $request
     * @param Reservation $reservation
     *
     * @return Response
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_edit', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods="DELETE")
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
}
