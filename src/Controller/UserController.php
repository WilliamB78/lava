<?php

namespace App\Controller;

use App\Controller\Utils\User\NewUserHandler;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     *
     * @param Request                      $request
     * @param NewUserHandler               $handler
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function new(Request $request, NewUserHandler $handler): Response
    {
        $user = new User();
        $form = $handler->createForm($user);

        if ($handler->process($form, $request)) {
            $handler->success($user);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/status", name="account_status", methods={"GET"})
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeIsBlocked(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$user->getisBlocked()) {
            $user->setIsBlocked(1);
            $this->addFlash('success', 'Le compte de l\'utilisateur : '. $user->getId(). " - " .$user->getLastname() .' à été bloqué.');
        } else {
            $user->setIsBlocked(0);
            $this->addFlash('success', 'Le compte de l\'utilisateur : '. $user->getId(). " - " .$user->getLastname() .' à été activé.');
        }
        $em->persist($user);
        //dump($user);exit;
        $em->flush();

        return $this->redirectToRoute('user_index');
    }
}
