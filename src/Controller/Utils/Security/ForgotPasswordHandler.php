<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 20:12
 */

namespace App\Controller\Utils\Security;


use App\Entity\User;
use App\Event\ForgotPasswordEvent;
use App\Form\ForgotPasswordType;
use App\Service\UserMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordHandler
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var UserMail $userMailer */
    private $userMailer;

    /** @var TokenGeneratorInterface $tokenGenerator */
    private $tokenGenerator;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    /** @var RouterInterface $router */
    protected $router;

    /**
     * ForgotPasswordHandler constructor.
     * @param UserMail $userMail
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(
        UserMail $userMail,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $entityManager,
        TokenGeneratorInterface $tokenGenerator,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    )
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->userMailer = $userMail;
        $this->tokenGenerator = $tokenGenerator;
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }

    /**
     * @param $user
     * @return FormInterface
     */
    public function createForm($user){
        return $this->formFactory->create(ForgotPasswordType::class,$user);
    }

    /**
     * @param Request $request
     * @return User|null|object
     */
    public function getUser(Request $request){
        if($request->isMethod('post')) {
            return $user = $this->em
                ->getRepository(User::class)
                ->findOneBy(
                    ['email' => $request
                        ->request
                        ->get('forgot_password')['email']
                    ]);
        } else {
            return $user = new User();
        }
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     */
    public function process($form, $request){
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            return true;
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function success($user){
        if ($user) {
            $date = new \DateTime();
            $user->setTokenResetPassword($this->tokenGenerator->generateToken());
            $user->setTokenExpire($date->add(new \DateInterval('P1D')));

            $this->em->persist($user);
            $this->em->flush();

            /**
             * Trigger Event for sending reset password link
             */
            $event = new ForgotPasswordEvent($user, $this->router);
            $this->dispatcher->dispatch('custom.event.forgot_password_event', $event);
            return true;
        } else {
            return false;
        }
    }
}