<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 05/04/2018
 * Time: 14:51.
 */

namespace App\Service\Twig;

use Twig_Extension;
use Twig_Function;

class AppExtension extends Twig_Extension
{
    /**
     * @return array|Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('reservation_state', [AppRuntime::class, 'statesSwitcher'])
        ];
    }
}
