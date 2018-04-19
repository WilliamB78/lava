<?php

namespace App\Controller;

use App\Entity\Reservation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * Class ReservationWorkflowController.
 */
class ReservationWorkflowController extends Controller
{
    /**
     * @Route("/reservations/secretary/approve/{state}/{id}", name="reservation_workflow_secretary_approve")
     *@Security("is_granted('ROLE_SECRETARY')")
     * @IsGranted("ROLE_SECRETARY" , statusCode=403, message="Accès Refusé! Vos droits ne sont pas suffisant !")
     *
     * @param Request     $request
     * @param Reservation $reservation
     * @param Registry    $registry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reservationSecretaryApprove(Request $request, Reservation $reservation, Registry $registry)
    {
        $this->reservationApprove($request, $reservation, $registry);

        return $this->redirectToRoute('reservation_index');
    }

    /**
     * @Route("/reservations/user/approve/{state}/{id}", name="reservation_workflow_user_approve")
     * @Security("is_granted('ROLE_UTILISATEUR')")
     *
     * @IsGranted("ROLE_UTILISATEUR" , statusCode=403, message="Accès Refusé! Vos droits ne sont pas suffisant !")
     *
     * @param Request     $request
     * @param Reservation $reservation
     * @param Registry    $registry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reservationUserApprove(Request $request, Reservation $reservation, Registry $registry)
    {
        $this->reservationApprove($request, $reservation, $registry);

        return $this->redirectToRoute('reservation_mes_reservations');
    }

    /**
     * @param Request     $request
     * @param Reservation $reservation
     * @param Registry    $registry
     */
    private function reservationApprove(Request $request, Reservation $reservation, Registry $registry)
    {
        $workflow = $registry->get($reservation);
        $state = $request->get('state');

        if ($workflow->can($reservation, $state)) {
            $workflow->apply($reservation, $state);
            $this->getDoctrine()->getManager()->flush();
        }
        if ($state == 'cancel_booking') {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();
            $this->addFlash('success', 'La réservation à bien été annulée.');
        }
    }
}
