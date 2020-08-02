<?php

namespace App\Service;

use App\Entity\Path;

/**
 * Path-finding functions, used to solve Travel Man technical test
 */
class PathFinderService
{
    // genetic algorithm hyperparameters
    const MAX_PATHS = 30; // population
    const MUTATION_PROBABILITY = 50; // population mutates x% of the time
    const CROSS_PROBABILITY = 90; // population generates offspring x% of the time
    const ELITISM = 1; // best x paths wont mutate to be preserved
    // distance calculation vars
    public $cachedDistances = [];
    // exact algorithm vars
    public $shortestRoute;
    private $minDistance = PHP_FLOAT_MAX;

    /**
     * Attemps to get a route as short as possible at specified runtime
     * Generates random routes and applies genetic operators on them
     *
     * @param array $citiesList: all the cities to pass throught
     * @param DateInterval: the available time to do the calculation
     * @return Path: best solution found
     */
    public function getShortestPath($citiesList, \DateInterval $maxExecutionTime)
    {
        $endDate = (new \Datetime())
            ->add($maxExecutionTime)
            ->sub(new \DateInterval('PT10S')); // subs 10s to ensure
        $pathList = $this->generatePaths($citiesList);
        $pathList = $this->sortPaths($pathList);
        $generation = 0;
        //$genTime = [];
        while (new \Datetime() < $endDate) {
            //$gS = new \Datetime();
            $generation++;
            $pathList = $this->crossoverPaths($pathList);
            $pathList = $this->mutatePaths($pathList);
            $pathList = $this->choosePaths($pathList);
            //$genTime[] = $gS->diff(new \Datetime());
        }
        //var_dump($generation);
        $bestPath = $pathList[0];
        return $pathList[0];
    }

    /**
     * Sorts all paths by total travel distance
     */
    public function sortPaths($pathList)
    {
        usort($pathList, function(Path $a, Path $b) { 
            return $this->getTravelDistance($a) <=> $this->getTravelDistance($b);
        });
        return $pathList;
    }

    /**
     * Aplies the mutation operator to random Paths and returns all them
     * Elite (best paths) must be always preserved
     * @param array $pathList: a list of paths
     * @return array: a list of paths similar to $pathList
     */
    public function mutatePaths($pathList)
    {
        $pathList = $this->sortPaths($pathList);
        foreach ($pathList as $key => $path) {
            if($key > self::ELITISM && rand(0, 100) < self::MUTATION_PROBABILITY) {
                $path->mutate();
            }
        }
        $pathList = $this->sortPaths($pathList);
        return $pathList;
    }

    /**
     * Selects routes in pairs and generate new ones from them
     * @param array $pathList: a list of paths
     * @return array: a list of paths that includes new ones
     */
    public function crossoverPaths($pathList)
    {
        $parents = $pathList;
        $pathList = [];

        if(count($parents) % 2) { // must be pair
            $parents[] = $parents[array_rand($parents)]->clone();
        }
        while (count($parents)) {
            // choose two random parents
            $randomKey1 = rand(0, count($parents) - 1);
            $randomParent1 = $parents[$randomKey1];
            array_splice($parents, $randomKey1, 1);

            $randomKey2 = rand(0, count($parents) - 1);
            $randomParent2 = $parents[$randomKey2];
            array_splice($parents, $randomKey2, 1);

            $pathList[] = $randomParent1;
            $pathList[] = $randomParent2;
            if($randomParent1 != $randomParent2) { // avoid inbreeding
                if(rand(1, 100) < self::CROSS_PROBABILITY) {
                    foreach ($randomParent1->crossover($randomParent2) as $key => $child) {
                        $pathList[] = $child;
                    }
                }
            }
        }
        return $pathList;
    }

    /**
     * Choose the best paths via tournament
     * @param array $pathList: a list of paths
     * @return array: a list with length MAX_PATH that contains the best paths
     */
    public function choosePaths($pathList)
    {
        $selec = [];
        foreach ([1, 2] as $key => $round) { // 2 rounds
            $participants = $pathList;
            
            if(count($participants) % 2) { // must be pair
                if($round == 1) {
                    unset($participants[array_rand($participants)]);
                } else {
                    $participants[] = $participants[array_rand($participants)]->clone();
                }
            }
            
            while ($participants) {
                $competitor1Key = array_rand($participants);
                $competitor1 = $participants[$competitor1Key];
                unset($participants[$competitor1Key]);
                $competitor2Key = array_rand($participants);
                $competitor2 = $participants[$competitor2Key];
                unset($participants[$competitor2Key]);
                if($this->getTravelDistance($competitor1) < $this->getTravelDistance($competitor2)) {
                    $selec[] = $competitor1->clone();
                } else {
                    $selec[] = $competitor2->clone();
                }
            }
        }
        $selec = $this->sortPaths($selec);
        array_splice($selec, self::MAX_PATHS, count($selec) - self::MAX_PATHS);        

        return $selec;
    }

    /**
     * Generates a list of random paths. Includes the path generated by the Nearest neighbour algorithm
     * @param array $citiesList: all cities to visit
     * @return array: list of paths
     */
    public function generatePaths($citiesList)
    {
        $pathList = [$this->getNNPath($citiesList)];
        for ($i=0; $i < self::MAX_PATHS - 1; $i++) { 
            $startCity = array_shift($citiesList); // preserve beginning city
            shuffle($citiesList);
            array_unshift($citiesList, $startCity);
            $pathList[] = new Path($citiesList);
        }
        return $pathList;
    }

    /**
     * Calculates the total distance of the whole cities path [in meters]
     * @param Path $path: a route
     * @return float
     */
    public function getTravelDistance($path)
    {
        $totalDistance = 0;
        $actualCity = false;
        foreach ($path->getCities() as $key => $destinyCity) {
            if(!$actualCity) {
                $actualCity = $destinyCity;
            } else {
                $totalDistance += $this->distanceBetween($actualCity, $destinyCity);
                $actualCity = $destinyCity;
            }
        }
        return $totalDistance;
    }

    /**
     * Calculates the distance between two cities
     * Uses a cache-system
     * @param City $city1: origin city
     * @param City $city2: destination city
     * @return float: distance in meters between the cities
     */
    public function distanceBetween($city1, $city2)
    {
        $cacheKey = $city1->getId() < $city2->getId() ? 
            $city1->getId()."_".$city2->getId() : $city2->getId()."_".$city1->getId();

        if(isset($this->cachedDistances[$cacheKey])) { // this distance is already calculated
            return $this->cachedDistances[$cacheKey];
        }
        $earthDist = $this->haversineGreatCircleDistance(
            $city1->getLatitude(), $city1->getLongitude(), 
            $city2->getLatitude(), $city2->getLongitude());

        $this->cachedDistances[$cacheKey] = $earthDist; // caches it
        return $earthDist;
    }

    /**
     * Calculates the great-circle distance between two points, with the Haversine formula.
     * https://stackoverflow.com/questions/14750275/haversine-formula-with-php
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    private function haversineGreatCircleDistance(
      $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + 
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /*****************************************************************************************
     * OTHER SOLUTIONS
     ****************************************************************************************/

    /**
     * Returns a travel route using Nearest Neighbour Algorithm
     * https://en.wikipedia.org/wiki/Nearest_neighbour_algorithm
     * Generates a route always choosing the nearest city as destiny
     * Recursive
     * @param array $citiesList: all cities to visit
     * @return Path: selected route
     */
    public function getNNPath($citiesList, $actualCity = null, $visitedCities = null)
    {
        if(!$actualCity) { // init route
            $actualCity = $citiesList[0];
            $visitedCities = [$actualCity];
        }
        $unvisitedCities = [];
        foreach ($citiesList as $key => $city) {
            if(!in_array($city, $visitedCities)) {
                $unvisitedCities[] = $city;
            }
        }
        if($unvisitedCities) {
            $closestCity = null;
            $closestCityDistance = PHP_FLOAT_MAX;
            foreach ($unvisitedCities as $key => $destinyCity) {
                if(($dist = $this->distanceBetween($actualCity, $destinyCity)) < $closestCityDistance) {
                    $closestCity = $destinyCity;
                    $closestCityDistance = $dist;
                }
            }
            $newActualCity = $closestCity;
            $newVisitedCities = $visitedCities;
            $newVisitedCities[] = $closestCity;
            return $this->getNNPath($citiesList, $newActualCity, $newVisitedCities);
        } else {            
            return new Path($visitedCities);
        }
    }

    /**
     * Checks all possible routes and stores the best one in a global variable
     * Recursive
     */
    public function getBestPossibleTravel($citiesList, $actualCity = null, $visitedCities = null, $traveledDistance = null)
    {
        if(!$actualCity) { // init route
            $actualCity = $citiesList[0];
            $visitedCities = [$actualCity];
            $traveledDistance = 0;
        }
        $unvisitedCities = [];
        foreach ($citiesList as $key => $city) {
            if(!in_array($city, $visitedCities)) {
                $unvisitedCities[] = $city;
            }
        }
        if($unvisitedCities) {
            foreach ($unvisitedCities as $key => $destinyCity) {
                $newActualCity = $destinyCity;
                $newVisitedCities = $visitedCities;
                $newVisitedCities[] = $destinyCity;
                $newTraveledDistance = $traveledDistance + $this->distanceBetween($actualCity, $newActualCity);
                $this->getBestPossibleTravel($citiesList, $newActualCity, $newVisitedCities, $newTraveledDistance);
            }
        } else {
            if($traveledDistance < $this->minDistance) {
                $this->minDistance = $traveledDistance;
                $this->shortestRoute = new Path($visitedCities);
            }
        }
    }

    /**
     * Attemps to get a route as short as possible at specified runtime
     * Generates random paths and returns the best one
     * @useless
     */
    public function getShortestRandPath($citiesList, \DateInterval $maxExecutionTime)
    {
        $endDate = (new \Datetime())->add($maxExecutionTime);
        $generation = 0;
        while (new \Datetime() < $endDate) {
            $generation++;
            $path = $this->generatePath($citiesList);
            if(($dist = $this->getTravelDistance($path)) < $this->minDistance) {
                $this->minDistance = $dist;
                $this->shortestRoute = $path;
            }
        }
        return $this->shortestRoute;
    }

    /**
     * Generates a random path
     * @useless
     */
    public function generatePath($citiesList)
    {
        $startCity = array_shift($citiesList); // preserve beginning city
        shuffle($citiesList);
        array_unshift($citiesList, $startCity);
        return new Path($citiesList);
    }

    /*****************************************************************************************
     * DEBUG FUNCTIONS
     ****************************************************************************************/
    public function printPaths($pathList)
    {
        foreach ($pathList as $key => $path) {
            $txt = "(".intval($this->getTravelDistance($path)/1000)."km)";
            var_dump($txt);
        }
        print("----------");
    }
    private function printBest($pathList) {
        $best = PHP_FLOAT_MAX;
        foreach ($pathList as $key => $a) {
            if($this->getTravelDistance($a) < $best) $best = $this->getTravelDistance($a);
        }
        //print($separator."-----------");
        var_dump($best);
    }
}