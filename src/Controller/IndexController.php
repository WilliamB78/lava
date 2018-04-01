<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MailNotifier;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    /**
     * @Route("/index", name="index")
     */
    public function index(MailNotifier $mailNotifier)
    {

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/mailtest/{id}", name="index_mailtest")
     */
    public function mailtest(MailNotifier $mailNotifier, User $user)
    {
        $mailNotifier->sendContactMessage($user);
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
