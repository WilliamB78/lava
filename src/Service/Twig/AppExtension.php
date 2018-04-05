<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 05/04/2018
 * Time: 14:51
 */

namespace App\Service\Twig;


use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return [
            new \Twig_Function('reservation_state', function($state){
                if ($state == 'created') {
                    $result = "En attente du secretariat";
                }
                if ($state == 'accepted') {
                    $result = "Accepté";
                }
                if ($state == 'refused') {
                    $result = "Refusé";
                }
                if ($state == 'cancelled') {
                    $result = "Annulé";
                }
                if ($state == 'cancelled_ok') {
                    $result = "Supprimé";
                }
                return $result;
            })
        ];
    }

}
