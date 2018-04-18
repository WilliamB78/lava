<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 07/04/2018
 * Time: 21:00.
 */

namespace App\Tests\Entity;

use App\Entity\Parametrage;
use PHPUnit\Framework\TestCase;

class ParametrageTest extends TestCase
{
    protected $params;

    public function setUp()
    {
        $this->params = new Parametrage();
        $this->params->setValue(12);
        $this->params->setName('param');
    }

    public function testCanBeCreate()
    {
        $this->assertInstanceOf(
            Parametrage::class,
            new Parametrage()
        );
    }

    public function testParamName()
    {
        $this->assertEquals('param', $this->params->getName());
    }

    public function testParamValue()
    {
        $this->assertEquals(12, $this->params->getValue());
    }
}
