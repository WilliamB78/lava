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
     * Liste des salles pour la secretaire et l'administrateur
     * @Route("/", name="room_index", methods="GET")
     * @Security("is_granted('ROLE_SECRETARY') or is_granted('ROLE_ADMIN')")
     *
     * @param RoomRepository $roomRepository
     * @return Response
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/index.html.twig', ['rooms' => $roomRepository->findAll()]);
    }

    /**
     * @Route("/new", name="room_new", methods="GET|POST")
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
     */
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', ['room' => $room]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods="GET|POST")
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_edit', ['id' => $room->getId()]);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods="DELETE")
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
