<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 04/04/2018
 * Time: 13:24.
 */

namespace App\Controller\Utils\Room;

use App\Repository\ParametrageRepository;
use App\Repository\RoomRepository;

class RoomIsFullHandler
{
    /** @var RoomRepository $room */
    private $room;
    /** @var ParametrageRepository $params */
    private $params;

    /**
     * RoomIsFullHandler constructor.
     *
     * @param RoomRepository        $roomRepository
     * @param ParametrageRepository $parametrageRepository
     */
    public function __construct(RoomRepository $roomRepository, ParametrageRepository $parametrageRepository)
    {
        $this->room = $roomRepository;
        $this->params = $parametrageRepository;
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        if ($this->room->findTotalRoom() >= $this->params->findOneBy(['name' => 'max_room'])->getValue()) {
            return true;
        }

        return false;
    }
}
