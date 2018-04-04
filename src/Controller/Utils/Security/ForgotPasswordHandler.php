<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 03/04/18
 * Time: 20:12
 */

namespace App\Controller\Utils\Security;


use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Service\UserMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordHandler
{
    private $em;
    private $formFactory;
    private $userMailer;
    private $tokenGenerator;

    /**
     * ForgotPasswordHandler constructor.
     * @param UserMail $userMail
     * @param EntityManagerInterface $entityManager
     * @param TokenGeneratorInterface $tokenGenerator
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(UserMail $userMail, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, FormFactoryInterface $formFactory)
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
        $this->userMailer = $userMail;
        $this->tokenGenerator = $tokenGenerator;
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
     * @param $form
     * @param $request
     * @return bool
     */
    public function process($form, $request){
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            return true;
        }
    }

    /**
     * @param $user
     * @return bool
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function success($user){
        if ($user) {
            $date = new \DateTime();
            $user->setTokenResetPassword($this->tokenGenerator->generateToken());
            $user->setTokenExpire($date->add(new \DateInterval('P1D')));

            $this->em->persist($user);
            $this->em->flush();

            $this->userMailer->sendResetPassword($user);
            return true;
        } else {
            return false;
        }
    }
}