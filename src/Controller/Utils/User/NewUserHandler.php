<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 20:32.
 */

namespace App\Controller\Utils\User;

use App\Entity\User;
use App\Event\NewUserEvent;
use App\Form\UserType;
use App\Service\UserMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NewUserHandler
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var UserMail $userMailer */
    private $userMailer;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * NewUserHandler constructor.
     *
     * @param UserMail                     $userMail
     * @param EventDispatcherInterface     $dispatcher
     * @param EntityManagerInterface       $entityManager
     * @param FormFactoryInterface         $formFactory
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserMail $userMail,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->userMailer = $userMail;
        $this->dispatcher = $dispatcher;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $user
     *
     * @return FormInterface
     */
    public function createForm($user)
    {
        $form = $this->formFactory->create(UserType::class, $user);
        $form->add('roles', ChoiceType::class, array(
            'choices' => array(
                'USER' => 'ROLE_USER',
                'SECRETARY' => 'ROLE_SECRETARY',
                'ADMIN' => 'ROLE_ADMIN',
            ),
        ));

        return $form;
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return bool
     */
    public function process($form, $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return true;
        }
    }

    /**
     * @param User $user
     */
    public function success($user)
    {
        // gestion du mot de passe
        $plainPassword = $user->getPassword();
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        /**
         * Trigger Event for sending Welcome Email.
         */
        $event = new NewUserEvent($user, $plainPassword);
        $this->dispatcher->dispatch('custom.event.new_user_event', $event);
    }
}
