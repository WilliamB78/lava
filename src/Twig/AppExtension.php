<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 05/04/2018
 * Time: 14:51.
 */

namespace App\Twig;

use Twig\TwigFilter;
use Twig_Extension;
use Twig_Function;

class AppExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('clone', array(AppRuntime::class, 'cloneVar')),
            new TwigFilter('room_name', array(AppRuntime::class, 'getRoomName')),
            new TwigFilter('is_enabled', array(AppRuntime::class, 'isEnabled')),
        );
    }

    /**
     * @return array|Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('reservation_state', [AppRuntime::class, 'statesSwitcher']),
            new Twig_Function('day_next_Value', [AppRuntime::class, 'dayNextValue']),
            new Twig_Function('day_reservations', [AppRuntime::class, 'dayReservations']),
            new Twig_Function('day_in_the_month', [AppRuntime::class, 'dayInTheMonth']),
        ];
    }
}
