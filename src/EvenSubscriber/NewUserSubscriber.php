<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 01/04/18
 * Time: 20:02.
 */

namespace App\EvenSubscriber;

use App\Event\NewUserEvent;
use App\Service\UserMail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewUserSubscriber implements EventSubscriberInterface
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
            NewUserEvent::NAME => 'onNewUser',
        );
    }

    /**
     * @param NewUserEvent $event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onNewUser(NewUserEvent $event)
    {
        /*
         * Using UserMail Service to send Welcome Email to new User
         */
        $this->mailer->sendWelcomeMessage($event->getUser(), $event->getPassword());
    }
}
