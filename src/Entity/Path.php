<?php

namespace App\Entity;

/**
 * Class whose instances represent a route through all cities
 * A possible solution to the problem
 * (The Chromosome in evolutionary computation)
 */
class Path
{
    /**
     * @var array
     */
    private $cities;

    public function __construct($cityList)
    {
        $this->cities = $cityList;
    }

    /**
     * Creates a copy of the path
     *
     * @return Path
     */
    public function clone() {
        return new Path($this->cities);
    }

    /**
     * Get cities.
     *
     * @return array
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Applies a mutation to the path
     * It must never change the first element (route beginning)
     */
    public function mutate()
    {
        if(rand(0, 100) > 50) {
            // select a city in the path and changes its position
            $keyFrom = random_int(1, count($this->cities) - 1);
            $keyTo = random_int(1, count($this->cities) - 2);
            $city = array_splice($this->cities, $keyFrom, 1, []);
            array_splice($this->cities, $keyTo, 0, $city);
        } else {
            // Selects a random piece in the middle of the array and flips it
            $half = intval(count($this->cities) / 2);
            $startPos = random_int(1, $half);
            $endPos = random_int($half, count($this->cities) - 1);

            $start = array_slice($this->cities, 0, $startPos);
            $middle = array_slice($this->cities, $startPos, $endPos - $startPos);
            $end = array_slice($this->cities, $endPos, count($this->cities) - $endPos);

            $this->cities = array_merge($start, array_reverse($middle), $end);
        }
    }

    /**
     * Recombinates two routes to generate two new ones
     * Cycle crossover
     * https://es.wikipedia.org/wiki/Recombinaci%C3%B3n_(computaci%C3%B3n_evolutiva)
     * @param Route $anotherRoute: the other parent route
     * @return array: two children [Route $children1, Route $children2]
     */
    public function crossover($anotherRoute)
    {
        $citiesList1 = $this->cities;
        $citiesList2 = $anotherRoute->cities;
        // removes the first city from both routes so as not to change its position
        $startCity = array_shift($citiesList1); 
        $startCity2 = array_shift($citiesList2);
        // applies the crossover
        $child1 = $citiesList2;
        $child2 = $citiesList1;
        $idx = [0];
        $i = 0;
        $continue = true;
        while ($continue) {
            $city = $citiesList2[$i];
            $i = array_search($city, $citiesList1);
            $idx[] = $i;
            $continue = $citiesList1[0] != $citiesList2[$i];
        }
        foreach ($idx as $k => $key) {
            $temp = $child1[$key];
            $child1[$key] = $child2[$key];
            $child2[$key] = $temp;
        }
        // re-insert beggining city
        array_unshift($child1, $startCity);
        array_unshift($child2, $startCity2);

        return [new Path($child1), new Path($child2)];
    }
}
