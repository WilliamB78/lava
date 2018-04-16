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
            new TwigFilter('equal_or_greater_than_today', array(AppRuntime::class, 'isEqualOrGreaterThanToday'))
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
            new Twig_Function('can_book', [AppRuntime::class, 'isRoleCanDoBooking']),
            new Twig_Function('equal_today', array(AppRuntime::class, 'isEqualToday'))
        ];
    }
}
