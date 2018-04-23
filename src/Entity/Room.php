<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=75)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     *
     * @ORM\Column(type="integer")
     */
    private $nbPlaces;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $commentState;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="room", cascade={"remove"})
     */
    private $reservations;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->nbPlaces = 0;
        $this->reservations = new ArrayCollection();
        $this->state = false;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNbPlaces()
    {
        return $this->nbPlaces;
    }

    /**
     * @param mixed $nbPlaces
     */
    public function setNbPlaces($nbPlaces): void
    {
        $this->nbPlaces = $nbPlaces;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getCommentState()
    {
        return $this->commentState;
    }

    /**
     * @param mixed $commentState
     */
    public function setCommentState($commentState): void
    {
        $this->commentState = $commentState;
    }

    /**
     * @return mixed
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param mixed $reservations
     */
    public function setReservations($reservations)
    {
        $this->reservations = $reservations;
    }

    public function addReservation(Reservation $reservation)
    {
        $this->reservations[] = $reservation;
    }
}
