<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 10:27.
 */

namespace App\EvenSuscriber;

use App\Event\ForgotPasswordEvent;
use App\Service\UserMail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForgotPasswordSuscriber implements EventSubscriberInterface
{
    protected $mailer;

    public function __construct(UserMail $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ForgotPasswordEvent::NAME => 'onForgotPassword',
        );
    }

    /**
     * @param ForgotPasswordEvent $event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onForgotPassword(ForgotPasswordEvent $event)
    {
        /*
         * Using UserMail Service to send Welcome Email to new User
         */
        $this->mailer->sendResetPassword($event->getUser(), $event->getLink());
    }
}
