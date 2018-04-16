<?php

namespace App\Controller;

use App\Controller\Utils\Room\RoomIsFullHandler;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room")
 */
class RoomController extends Controller
{
    /**
     * Liste des salles.
     *
     * @Route("/", name="room_index", methods="GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param RoomRepository $roomRepository
     *
     * @return Response
     */
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findTotalRoomOpen();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Liste des salles pour la secretaire et l'administrateur.
     *
     * @Route("/hors_service", name="room_closed", methods="GET")
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     *
     * @param RoomRepository $roomRepository
     *
     * @return Response
     */
    public function horsService(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findTotalRoomClosed();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * @Route("/new", name="room_new", methods="GET|POST")
     *
     * @param Request           $request
     * @param RoomIsFullHandler $fullHandler
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function new(Request $request, RoomIsFullHandler $fullHandler): Response
    {
        if (!$fullHandler->isFull()) {
            $room = new Room();
            $form = $this->createForm(RoomType::class, $room);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($room);
                $em->flush();

                $this->addFlash('success', 'Vous venez de créer une nouvelle salle');

                return $this->redirectToRoute('room_index');
            }

            return $this->render('room/new.html.twig', [
                'room' => $room,
                'form' => $form->createView(),
            ]);
        } else {
            $this->addFlash('error', 'Vous ne avez atteins le nombre maximal de salle');

            return $this->redirectToRoute('room_index');
        }
    }

    /**
     * @Route("/{id}", name="room_show", methods="GET")
     * @Security("is_granted('ROLE_SECRETARY')")
     *
     * @param Room $room
     *
     * @return Response
     */
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', ['room' => $room]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods="GET|POST")
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param Room    $room
     *
     * @return Response
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('room_edit', ['id' => $room->getId()]);
            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods="DELETE")
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($room);
            $em->flush();
        }

        return $this->redirectToRoute('room_index');
    }
}
