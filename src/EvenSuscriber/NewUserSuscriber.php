<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 01/04/18
 * Time: 20:02
 */

namespace App\EvenSuscriber;


use App\Event\NewUserEvent;
use App\Service\MailNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewUserSuscriber implements EventSubscriberInterface
{
    protected $mailer;

    public function __construct(MailNotifier $mailer)
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
            NewUserEvent::NAME => 'onNewUser'
        );
    }

    public function onNewUser(NewUserEvent $event){
        /**
         * Using MailerNotifier Service to send Welcome Email to new User
         */
        $this->mailer->sendWelcomeMessage($event->getUser(), $event->getPassword());
    }
}