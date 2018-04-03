<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 11:05
 */

namespace App\Service;


use Twig_Environment;

class WorkflowMail
{
    protected $mailer;
    protected $templating;

    /**
     * WorkflowMail constructor.
     * @param \Swift_Mailer $mailer
     * @param Twig_Environment $templating
     */
    public function __construct(\Swift_Mailer $mailer, Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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