<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 13/04/2018
 * Time: 09:31
 */

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/parametrage", name="parametrage_")
 */
class ParametrageController extends Controller
{
    /**
     * @Route("/index", name="index")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index()
    {

    }
}