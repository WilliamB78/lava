<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 06/04/2018
 * Time: 12:28
 */

namespace App\Service\Twig;


class AppRuntime
{
    public function statesSwitcher ($state) {
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
    }
}