<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 11:05.
 */

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\User;
use Twig_Environment;

class WorkflowMail
{
    protected $mailer;
    protected $templating;

    /**
     * WorkflowMail constructor.
     *
     * @param \Swift_Mailer    $mailer
     * @param Twig_Environment $templating
     */
    public function __construct(\Swift_Mailer $mailer, Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param Reservation $reservation
     * @param User        $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function statuChangeMessage(Reservation $reservation, User $user)
    {
        $template = 'email/workflowStatuChange.html.twig';

        $from = 'reservation@lava.com';

        $to = $user->getEmail();

        $subject = 'Reservation';

        $body = $this->templating->render($template, array(
            'user' => $user,
            'subject' => $subject,
            'reservation' => $reservation,
        ));

        $this->sendMessage($from, $to, $subject, $body);
    }

    protected function sendMessage($from, $to, $subject, $body)
    {
        $mail = (new \Swift_Message());

        $mail
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }
}
