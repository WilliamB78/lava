<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 20/04/2018
 * Time: 12:23
 */

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Twig_Environment;

class RoomMail
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
     * @param User $user
     * @param Room $room
     *
     * @param $reversations
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function roomDisabled(User $user, Room $room, $reversations)
    {
        $template = 'email/roomDisabled.html.twig';

        $from = 'reservation@lava.com';

        $to = $user->getEmail();

        $subject = 'Reservation';

        $body = $this->templating->render($template, array(
            'user' => $user,
            'subject' => $subject,
            'room' => $room,
            'reservations' => $reversations
        ));

        $this->sendMessage($from, $to, $subject, $body);
    }

    /**
     * @param User $user
     * @param Room $room
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function roomEnabled(User $user, Room $room, $reversations)
    {
        $template = 'email/roomEnabled.html.twig';

        $from = 'reservation@lava.com';

        $to = $user->getEmail();

        $subject = 'Reservation';

        $body = $this->templating->render($template, array(
            'user' => $user,
            'subject' => $subject,
            'room' => $room,
            'reservations' => $reversations
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