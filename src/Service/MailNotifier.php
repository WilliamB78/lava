<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 01/04/18
 * Time: 16:51
 */

namespace App\Service;


use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Twig_Environment;

class MailNotifier
{
    protected $mailer;
    protected $templating;

    /**
     * MailNotifier constructor.
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     */
    public function __construct(\Swift_Mailer $mailer, Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendWelcomeMessage($user, $password)
    {
        $template = 'email/testTemplate.html.twig';

        $from = 'admin@lava.com';

        $to = $user->getEmail();

        $subject = "[$from] Lava Booking System Account is Active";

        $body = $this->templating->render($template, array(
            'user' => $user,
            'password' => $password,
            'adminEmail' => 'admin@example.com'
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