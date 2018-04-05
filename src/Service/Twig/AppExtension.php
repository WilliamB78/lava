<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 05/04/2018
 * Time: 14:51.
 */

namespace App\Service\Twig;

use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new \Twig_Function('reservation_state', function ($state) {
                if ('created' == $state) {
                    $result = 'En attente du secretariat';
                }
                if ('accepted' == $state) {
                    $result = 'Accepté';
                }
                if ('refused' == $state) {
                    $result = 'Refusé';
                }
                if ('cancelled' == $state) {
                    $result = 'Annulé';
                }
                if ('cancelled_ok' == $state) {
                    $result = 'Supprimé';
                }

                return $result;
            }),
        ];
    }
}
