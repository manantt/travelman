<?php

namespace App\tests\Service;

use App\Entity\Path;
use App\Entity\City;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testPath()
    {
        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);

        $citiesList = [$city1, $city2, $city3, $city4, $city5];
        $path = new Path($citiesList);

        $this->assertInstanceOf(Path::class, $path);
        $this->assertObjectHasAttribute('cities', $path);
        $this->assertEquals($citiesList, $path->getCities());
    }

    public function testClone()
    {
        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);

        $citiesList = [$city1, $city2, $city3, $city4, $city5];
        $path = new Path($citiesList);
        $clone = $path->clone();

        $this->assertEquals($path, $clone);
    }

    public function testMutate()
    {
        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);

        $citiesList = [$city1, $city2, $city3, $city4, $city5];
        $path = new Path($citiesList);
        $path->mutate();

		// the path must contain all cities once
		$this->assertEquals(count($path->getCities()), 5);
        foreach ($citiesList as $key => $city) {
    		$this->assertTrue(in_array($city, $path->getCities()));
    	}
    }

    public function testCrossover()
    {
        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
        $city6 = new City(6, "San Francisco", 37.47, -122.26);
        $city7 = new City(7, "Auckland", -36.52, 174.45);

        $citiesList1 = [$city1, $city3, $city2, $city5, $city4, $city6, $city7];
        $citiesList2 = [$city1, $city7, $city5, $city6, $city2, $city4, $city3];
        $path1 = new Path($citiesList1);
        $path2 = new Path($citiesList2);
        $children = $path1->crossover($path2);

        $this->assertEquals(new Path([$city1, $city3, $city5, $city6, $city2, $city4, $city7]), $children[0]);
        $this->assertEquals(new Path([$city1, $city7, $city2, $city5, $city4, $city6, $city3]), $children[1]);
    }
}