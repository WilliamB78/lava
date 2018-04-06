<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 11:05.
 */

namespace App\Service;

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
     * @param $reservation
     * @param $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function statuChangeMessage($reservation, $user)
    {
        $template = 'email/testTemplate.html.twig';

        $from = 'reservation@lava.com';

        $to = $user->getEmail();

        $subject = "Reservation";

        $body = $this->templating->render($template, array(
            'user' => $user
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
