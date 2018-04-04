<?php

namespace App\Controller;

use App\Controller\Utils\Security\ForgotPasswordHandler;
use App\Controller\Utils\Security\ResetPasswordHandler;
use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/security", name="security")
     */
    public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/", name="security_connexion", methods={"GET", "POST"})
     * @Security("not is_granted('IS_AUTHENTICATED_FULLY')")

     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // On récupère le messagte d'erreur si il y en a un
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',[
            'last_email' => $lastEmail,
            'error' => $error
        ]);
    }

    /**
     * @Route("/forgot-password",name="security_forgot_password", methods={"GET","POST"})
     * @Security("not is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param ForgotPasswordHandler $handler
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function forgotPassword(Request $request, ForgotPasswordHandler $handler)
    {
        # Création d'un nouvel utilisateur
        $user = $handler->getUser($request);

        # Formulaire de mot de passe oublié
        $form = $handler->createForm($user);

        if($handler->process($form, $request)) {
            if($handler->success($user)){
                $this->addFlash('success' , 'Un email a été envoyé');
            } else {
                $this->addFlash('danger' , 'Email invalide !');
            }
            return $this->redirectToRoute('security_forgot_password');
        }

        return $this->render('security/forgot-password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Seul les utilisateurs non authentifié peuvent avoir accès a leur reset
     * @Route("/reset-password/{token}", name="security_reset_password", requirements={"token"}, methods={"GET|POST"})
     * @Security("not is_granted('IS_AUTHENTICATED_FULLY')")
     * @Entity("user", expr="repository.findOneByTokenResetPassword(token)")
     *
     * @param User $user
     * @param ResetPasswordHandler $handler
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(User $user,ResetPasswordHandler $handler,Request $request)
    {
        # Si il n'y a pas d'utilisateur c'est qu'il n'a pas fait de demande de mot de passe
        # Ou de le token n'est plus valide dans la periode voulu
        if (!$user || ($user->getTokenExpire()->format('Y-m-d H:i:s') < date("Y-m-d H:i:s"))) {
            throw new \InvalidArgumentException("Votre token de remise à jour de votre mot de passe est incorrect.");
        }

        $form = $handler->createForm($user);

        if($handler->process($form, $request)) {
            $this->addFlash('success' , 'Votre mot de passe à bien été  mit à jour.');
            return $this->redirectToRoute('security_connexion');
        }

        return $this->render('security/reset-password.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
