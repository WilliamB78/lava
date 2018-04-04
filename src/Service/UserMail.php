<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 01/04/18
 * Time: 16:51
 */

namespace App\Service;


use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class UserMail
{
    protected $mailer;
    protected $templating;
    protected $router;

    /**
     * UserMail constructor.
     * @param \Swift_Mailer $mailer
     * @param Twig_Environment $templating
     */
    public function __construct(\Swift_Mailer $mailer, Twig_Environment $templating,UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * @param $user
     * @param $password
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendWelcomeMessage($user, $password)
    {
        $template = 'email/testTemplate.html.twig';

        $from = 'admin@lava.com';

        $to = $user->getEmail();

        $subject = "[$from] Lava Booking System Account is Active";

        $body = $this->templating->render($template, array(
            'user' => $user,
            'password' => $password,
            'adminEmail' => $from
        ));

        $this->sendMessage($from, $to, $subject, $body);
    }

    /**
     * Envoi un email avec le lien pour changer le mot de passe
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendResetPassword(User $user, $link)
    {
        $template = 'email/resetPasswordTemplate.html.twig';

        $from = 'admin@lava.com';

        $to = $user->getEmail();

        $subject = "Mot de passe oublié";

//        $lien  = $this->router->generate(
//            'security_reset_password',
//            [
//                'token' => $user->getTokenResetPassword()
//            ],
//            $this->router::ABSOLUTE_URL);
        $lien = $link;

        $body = $this->templating->render($template, array(
            'user' => $user,
            'url' => $lien
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