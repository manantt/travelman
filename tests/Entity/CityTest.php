<?php

namespace App\tests\Service;

use App\Entity\City;
use PHPUnit\Framework\TestCase;

class CityTest extends TestCase
{
    public function testCity()
    {
        $city = new City(1, "City1", 5, -1);
        $this->assertInstanceOf(City::class, $city);
        $this->assertObjectHasAttribute('id', $city);
        $this->assertObjectHasAttribute('name', $city);
        $this->assertObjectHasAttribute('latitude', $city);
        $this->assertObjectHasAttribute('longitude', $city);
        $this->assertEquals(1, $city->getId());
        $this->assertEquals("City1", $city->getName());
        $this->assertEquals(5, $city->getLatitude());
        $this->assertEquals(-1, $city->getLongitude());
    }
}