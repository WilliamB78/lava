<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 11:07.
 */

namespace App\EvenSuscriber;

use App\Event\WorkflowStatusEvent;
use App\Service\WorkflowMail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowSuscriber implements EventSubscriberInterface
{
    /** @var WorkflowMail $mailer */
    protected $mailer;

    public function __construct(WorkflowMail $mailer)
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
        return [
            'workflow.reservation.completed' => 'onStatuChange',
        ];
    }

    /**
     * @param Event $event
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onStatuChange(Event $event)
    {
        $this->mailer->statuChangeMessage($event->getSubject(),$event->getSubject()->getUser());
    }
}
