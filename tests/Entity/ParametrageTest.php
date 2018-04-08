<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 07/04/2018
 * Time: 21:00
 */

namespace App\Tests\Entity;


use App\Entity\Parametrage;
use PHPUnit\Framework\TestCase;

class ParametrageTest extends TestCase
{
    public function testCanBeCreate()
    {
        $this->assertInstanceOf(
            Parametrage::class,
            new Parametrage()
        );
    }

    public function testParamName()
    {
        $params = new Parametrage();
        $params->setName("param");

        $this->assertEquals("param", $params->getName());
    }

    public function testParamValue()
    {
        $params = new Parametrage();
        $params->setValue(12);

        $this->assertEquals(12, $params->getValue());
    }
}