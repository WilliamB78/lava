<?php

namespace App\Controller;

use App\Entity\Reservation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * Class ReservationWorkflowController
 * @package App\Controller
 */
class ReservationWorkflowController extends Controller
{
    /**
     * @Route("/reservations/secretary/approve/{state}/{id}", name="reservation_workflow_secretary_approve")
     *
     * @Security("has_role('ROLE_SECRETARY')")
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
     *
     * @Security("has_role('ROLE_UTILISATEUR')")
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
    }
}
