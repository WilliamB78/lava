<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 01/04/18
 * Time: 20:39
 */

namespace App\Event;


use Symfony\Component\EventDispatcher\Event;

class NewUserEvent extends Event
{
    const NAME = 'user.created';

    protected $user;
    protected $password;

    /**
     * NewUserEvent constructor.
     * @param $user
     */
    public function __construct($user, $plainPassword)
    {
        $this->user = $user;
        $this->password = $plainPassword;
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
    public function getPassword()
    {
        return $this->password;
    }



}