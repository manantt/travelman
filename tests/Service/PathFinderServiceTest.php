<?php

namespace App\tests\Service;

use App\Service\PathFinderService;
use App\Entity\City;
use App\Entity\Path;
use PHPUnit\Framework\TestCase;

class PathFinderServiceTest extends TestCase
{
    public function testGetShortestPath()
    {
        $pathfinder = new PathFinderService();
        $citiesList = [
        	new City(1, "City1", 1, 1),
        	new City(2, "City2", 2, 2),
        	new City(3, "City3", 3, 3),
        	new City(4, "City4", 4, 4),
        	new City(5, "City5", 5, 5)
        ]; 
        $maxExecutionTime = new \DateInterval('PT15S');
        $startTime = new \Datetime();
        $pathfinder->getShortestPath($citiesList, $maxExecutionTime);
        $endTime = new \Datetime();
        $this->assertGreaterThan($endTime, $startTime->add($maxExecutionTime));
    }

    public function testSortPaths()
    {
        $pathfinder = new PathFinderService();
        $city1 = new City(1, "City1", 1, 1);
        $city2 = new City(2, "City2", 1, 2);
        $city3 = new City(3, "City3", 1, 3);
        $city4 = new City(4, "City4", 1, 5);
        $path1 = new Path([$city1, $city3, $city2, $city4]);
        $path2 = new Path([$city1, $city4, $city2, $city3]);
        $path3 = new Path([$city1, $city2, $city3, $city4]);

        $pathList = $pathfinder->sortPaths([$path1, $path2, $path3]);
        $this->assertEquals($pathList[0], $path3);
        $this->assertEquals($pathList[1], $path1);
        $this->assertEquals($pathList[2], $path2);
    }

    public function testMutatePaths()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$city6 = new City(6, "San Francisco", 37.47, -122.26);
		$city7 = new City(7, "Auckland", -36.52, 174.45);
		$city8 = new City(8, "London", 51.32, -0.5);
		$city9 = new City(9, "Reykjavík", 64.4, -21.58);
		$city10 = new City(10, "Paris", 48.86, 2.34);
		$city11 = new City(11, "Prague", 50.5, 14.26);
		$city12 = new City(12, "New York", 40.47, -73.58);
		$city13 = new City(13, "New Delhi", 28.60, 77.22);
		$city14 = new City(14, "Rio", -22.57, -43.12);
		$city15 = new City(15, "Mexico City", 19.26, -99.7);
		$city16 = new City(16, "Lima", -12, -77.2);
		$city17 = new City(17, "Moscow", 55.45, 37.36);
		$city18 = new City(18, "Cairo", 30.2, 31.21);
		$city19 = new City(19, "Toronto", 43.40, -79.24);
		$city20 = new City(20, "Santiago", -12.56, -38.27);
		$city21 = new City(21, "Caracas", 10.28, -67.2);
		$city22 = new City(22, "San Jose", 9.55, -84.02);
		$city23 = new City(23, "Lusaka", -15.25, 28.16);
		$city24 = new City(24, "Casablanca", 33.35, -7.39);
		$city25 = new City(25, "Astana", 51.10, 71.30);
		$city26 = new City(26, "Bangkok", 13.45, 100.30);
		$city27 = new City(27, "Perth", -31.57, 115.52);
		$city28 = new City(28, "Melbourne", -37.47, 144.58);
		$city29 = new City(29, "Vancouver", 49.16, -123.07);
		$city30 = new City(30, "Anchorage", 61.17, -150.02);
		$city31 = new City(31, "Accra", 5.35, -0.06);
		$city32 = new City(32, "Jerusalem", 31.78, 35.22);

        $citiesList = [$city1, $city2, $city3, $city4, $city5, $city6, $city7, $city8, 
        	$city9, $city10, $city11, $city12, $city13, $city14, $city15, $city16, 
        	$city17, $city18, $city19, $city20, $city21, $city22, $city23, $city24, 
        	$city25, $city26, $city27, $city28, $city29, $city30, $city31, $city32];

        $pathList = $pathfinder->generatePaths($citiesList);

        foreach ($pathList as $key => $path) {
        	// cities count must be kept
        	$this->assertEquals(count($path->getCities()), 32);
        	foreach ($citiesList as $key2 => $city) {
        		// every path must contain all cities
        		$this->assertTrue(in_array($city, $path->getCities()));
        	}
        }
    }

    public function testCrossoverPaths()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$city6 = new City(6, "San Francisco", 37.47, -122.26);
		$city7 = new City(7, "Auckland", -36.52, 174.45);
		$city8 = new City(8, "London", 51.32, -0.5);
		$city9 = new City(9, "Reykjavík", 64.4, -21.58);
		$city10 = new City(10, "Paris", 48.86, 2.34);
		$city11 = new City(11, "Prague", 50.5, 14.26);
		$city12 = new City(12, "New York", 40.47, -73.58);
		$city13 = new City(13, "New Delhi", 28.60, 77.22);
		$city14 = new City(14, "Rio", -22.57, -43.12);
		$city15 = new City(15, "Mexico City", 19.26, -99.7);
		$city16 = new City(16, "Lima", -12, -77.2);
		$city17 = new City(17, "Moscow", 55.45, 37.36);
		$city18 = new City(18, "Cairo", 30.2, 31.21);
		$city19 = new City(19, "Toronto", 43.40, -79.24);
		$city20 = new City(20, "Santiago", -12.56, -38.27);
		$city21 = new City(21, "Caracas", 10.28, -67.2);
		$city22 = new City(22, "San Jose", 9.55, -84.02);
		$city23 = new City(23, "Lusaka", -15.25, 28.16);
		$city24 = new City(24, "Casablanca", 33.35, -7.39);
		$city25 = new City(25, "Astana", 51.10, 71.30);
		$city26 = new City(26, "Bangkok", 13.45, 100.30);
		$city27 = new City(27, "Perth", -31.57, 115.52);
		$city28 = new City(28, "Melbourne", -37.47, 144.58);
		$city29 = new City(29, "Vancouver", 49.16, -123.07);
		$city30 = new City(30, "Anchorage", 61.17, -150.02);
		$city31 = new City(31, "Accra", 5.35, -0.06);
		$city32 = new City(32, "Jerusalem", 31.78, 35.22);

        $citiesList = [$city1, $city2, $city3, $city4, $city5, $city6, $city7, $city8, 
        	$city9, $city10, $city11, $city12, $city13, $city14, $city15, $city16, 
        	$city17, $city18, $city19, $city20, $city21, $city22, $city23, $city24, 
        	$city25, $city26, $city27, $city28, $city29, $city30, $city31, $city32];

        $pathList = $pathfinder->generatePaths($citiesList);

        foreach ($pathList as $key => $path) {
        	// cities count must be kept
        	$this->assertEquals(count($path->getCities()), 32);
        	foreach ($citiesList as $key2 => $city) {
        		// every path must contain all cities
        		$this->assertTrue(in_array($city, $path->getCities()));
        	}
        }
    }

    public function testChoosePaths()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$city6 = new City(6, "San Francisco", 37.47, -122.26);
		$city7 = new City(7, "Auckland", -36.52, 174.45);
		$city8 = new City(8, "London", 51.32, -0.5);
		$city9 = new City(9, "Reykjavík", 64.4, -21.58);
		$city10 = new City(10, "Paris", 48.86, 2.34);
		$city11 = new City(11, "Prague", 50.5, 14.26);
		$city12 = new City(12, "New York", 40.47, -73.58);
		$city13 = new City(13, "New Delhi", 28.60, 77.22);
		$city14 = new City(14, "Rio", -22.57, -43.12);
		$city15 = new City(15, "Mexico City", 19.26, -99.7);
		$city16 = new City(16, "Lima", -12, -77.2);
		$city17 = new City(17, "Moscow", 55.45, 37.36);
		$city18 = new City(18, "Cairo", 30.2, 31.21);
		$city19 = new City(19, "Toronto", 43.40, -79.24);
		$city20 = new City(20, "Santiago", -12.56, -38.27);
		$city21 = new City(21, "Caracas", 10.28, -67.2);
		$city22 = new City(22, "San Jose", 9.55, -84.02);
		$city23 = new City(23, "Lusaka", -15.25, 28.16);
		$city24 = new City(24, "Casablanca", 33.35, -7.39);
		$city25 = new City(25, "Astana", 51.10, 71.30);
		$city26 = new City(26, "Bangkok", 13.45, 100.30);
		$city27 = new City(27, "Perth", -31.57, 115.52);
		$city28 = new City(28, "Melbourne", -37.47, 144.58);
		$city29 = new City(29, "Vancouver", 49.16, -123.07);
		$city30 = new City(30, "Anchorage", 61.17, -150.02);
		$city31 = new City(31, "Accra", 5.35, -0.06);
		$city32 = new City(32, "Jerusalem", 31.78, 35.22);

        $citiesList = [$city1, $city2, $city3, $city4, $city5, $city6, $city7, $city8, 
        	$city9, $city10, $city11, $city12, $city13, $city14, $city15, $city16, 
        	$city17, $city18, $city19, $city20, $city21, $city22, $city23, $city24, 
        	$city25, $city26, $city27, $city28, $city29, $city30, $city31, $city32];

        $pathList = $pathfinder->generatePaths($citiesList);
        $pathList = array_merge($pathList, $pathList);
        $pathList = $pathfinder->choosePaths($pathList);
        // path number = MAX_PATHS
        $this->assertEquals(count($pathList), $pathfinder::MAX_PATHS);
    }

    // this three function may interact and cause an error
    // for example if two paths get referenced, 
    // the best path (elite) could be altered making algorithm loss efficacy 
    public function testCrossMutateChoosePaths()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$city6 = new City(6, "San Francisco", 37.47, -122.26);
		$city7 = new City(7, "Auckland", -36.52, 174.45);
		$city8 = new City(8, "London", 51.32, -0.5);
		$city9 = new City(9, "Reykjavík", 64.4, -21.58);
		$city10 = new City(10, "Paris", 48.86, 2.34);
		$city11 = new City(11, "Prague", 50.5, 14.26);
		$city12 = new City(12, "New York", 40.47, -73.58);
		$city13 = new City(13, "New Delhi", 28.60, 77.22);
		$city14 = new City(14, "Rio", -22.57, -43.12);
		$city15 = new City(15, "Mexico City", 19.26, -99.7);
		$city16 = new City(16, "Lima", -12, -77.2);
		$city17 = new City(17, "Moscow", 55.45, 37.36);
		$city18 = new City(18, "Cairo", 30.2, 31.21);
		$city19 = new City(19, "Toronto", 43.40, -79.24);
		$city20 = new City(20, "Santiago", -12.56, -38.27);
		$city21 = new City(21, "Caracas", 10.28, -67.2);
		$city22 = new City(22, "San Jose", 9.55, -84.02);
		$city23 = new City(23, "Lusaka", -15.25, 28.16);
		$city24 = new City(24, "Casablanca", 33.35, -7.39);
		$city25 = new City(25, "Astana", 51.10, 71.30);
		$city26 = new City(26, "Bangkok", 13.45, 100.30);
		$city27 = new City(27, "Perth", -31.57, 115.52);
		$city28 = new City(28, "Melbourne", -37.47, 144.58);
		$city29 = new City(29, "Vancouver", 49.16, -123.07);
		$city30 = new City(30, "Anchorage", 61.17, -150.02);
		$city31 = new City(31, "Accra", 5.35, -0.06);
		$city32 = new City(32, "Jerusalem", 31.78, 35.22);

        $citiesList = [$city1, $city2, $city3, $city4, $city5, $city6, $city7, $city8, 
        	$city9, $city10, $city11, $city12, $city13, $city14, $city15, $city16, 
        	$city17, $city18, $city19, $city20, $city21, $city22, $city23, $city24, 
        	$city25, $city26, $city27, $city28, $city29, $city30, $city31, $city32];

        $pathList = $pathfinder->generatePaths($citiesList);
        $pathList = $pathfinder->sortPaths($pathList);
        for ($i=0; $i < 100; $i++) { 
        	$bestDistance = $pathfinder->getTravelDistance($pathList[0]);
        	$pathList = $pathfinder->crossoverPaths($pathList);
            $pathList = $pathfinder->mutatePaths($pathList);
            $pathList = $pathfinder->choosePaths($pathList);
        	$newBestDistance = $pathfinder->getTravelDistance($pathList[0]);
        	$this->assertGreaterThanOrEqual($newBestDistance, $bestDistance);
        }
    }

    public function testGeneratePaths()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);

        $citiesList = [$city1, $city2, $city3, $city4, $city5];

        $pathList = $pathfinder->generatePaths($citiesList);
        $this->assertEquals(count($pathList), $pathfinder::MAX_PATHS);
        $this->assertContainsOnlyInstancesOf(Path::class, $pathList);
    }

    public function testGetTravelDistance()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);

        $citiesList = [$city1, $city2, $city3, $city4, $city5];

        $path = new Path($citiesList);
        $travelDistance = $pathfinder->getTravelDistance($path);
        $this->assertEquals(29327730.64943589, $travelDistance);
    }

    public function testDistanceBetween()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);

        $travelDistance = $pathfinder->distanceBetween($city1, $city2);
        $this->assertEquals(2084050.496008833, $travelDistance);
        $this->assertArrayHasKey('1_2', $pathfinder->cachedDistances);
        $this->assertEquals(2084050.496008833, $pathfinder->cachedDistances['1_2']);
    }

    public function testGetNNPath()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$citiesList = [$city1, $city2, $city3, $city4, $city5];

        $nNPath = $pathfinder->getNNPath($citiesList);
        $testPath = new Path([$city1, $city3, $city2, $city5, $city4]);
        $this->assertEquals($testPath, $nNPath);
    }

    public function testGetBestPossibleTravel()
    {
        $pathfinder = new PathFinderService();

        $city1 = new City(1, "Beijing", 39.93,	116.40);
    	$city2 = new City(2, "Tokyo", 35.40, 139.45);
		$city3 = new City(3, "Vladivostok", 43.8, 131.54);
		$city4 = new City(4, "Dakar", 14.40, -17.28);
		$city5 = new City(5, "Singapore", 1.14, 103.55);
		$citiesList = [$city1, $city2, $city3, $city4, $city5];

        $pathfinder->getBestPossibleTravel($citiesList);
        $testPath = new Path([$city1, $city3, $city2, $city5, $city4]);
        $this->assertEquals($testPath, $pathfinder->shortestRoute);
    }
}