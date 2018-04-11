<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 04/04/2018
 * Time: 10:45.
 */

namespace App\DataFixtures;

use App\Entity\Parametrage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ParametrageFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $datas = [
            ['name' => 'max_room', 'value' => '50'],
        ];

        foreach ($datas as $data) {
            $parametrage = new Parametrage();
            $parametrage->setName($data['name']);
            $parametrage->setValue($data['value']);
            $manager->persist($parametrage);
        }
        $manager->flush();
    }
}
