<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPassword(Request $request)
    {
        # Création d'un nouvel utilisateur
        if($request->isMethod('post')) {
            $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $request->request->get('forgot_password')['email']]);
        } else {
            $user = new User();
        }

        # Formulaire de mot de passe oublié
        $form = $this->createForm(ForgotPasswordType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted()) {

            if($user) {
                // TODO : envoyer un email avec le service mailer
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
}
