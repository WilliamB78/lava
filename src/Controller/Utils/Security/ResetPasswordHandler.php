<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 08:44
 */

namespace App\Controller\Utils\Security;


use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordHandler
{
    private $em;
    private $formFactory;
    private $passwordEncoder;

    /**
     * ResetPasswordHandler constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $user
     * @return FormInterface
     */
    public function createForm($user){
        return $this->formFactory->create(ResetPasswordType::class,$user);
    }

    public function process($form, $request){
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $this->success($user);

            return true;
        }
    }

    public function success($user){
        $password = $this->passwordEncoder->encodePassword($user,$user->getPassword());

        /** @var User $user */
        $user->setPassword($password);

        # Pur eviter qu'il est a repasser par ici
        $user->setTokenResetPassword(null);

        # On persiste en base
        $this->em->persist($user);
        $this->em->flush();
    }


}