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

    /**
     * NewUserEvent constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }



}