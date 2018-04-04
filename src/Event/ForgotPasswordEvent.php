<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 09:52
 */

namespace App\Event;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\Event;

class ForgotPasswordEvent extends Event
{
    const NAME = 'user.forgotPassword';
    /** @var User $user */
    protected $user;
    protected $token;
    protected $router;

    /**
     * ForgotPasswordEvent constructor.
     * @param $user
     * @param Router $router
     */
    public function __construct($user, Router $router)
    {
        $this->user = $user;
        $this->router = $router;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        $link = $this->router->generate(
            'security_reset_password',
            [
                'token' => $this->user->getTokenResetPassword()
            ],
            $this->router::ABSOLUTE_URL);

        return $link;
    }


}