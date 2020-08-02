<?php

namespace App\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use App\Entity\City;

/**
 * File read and write functions
 */
class FileService
{
    /**
     * Reads a file to get a list of cities.
     * The input file will contain a listing of cities and coordinates in a tab-delimited file
     * The list will being in "Beijing".
     * 
     * @param string $fileName: path to the file
     * @return array: the list of the cities, format: "city_name" => [longitude, latitude]
     */
    public function getCitiesList($fileName)
    {
        if(!file_exists($fileName)) {
            throw new Exception('File not found in ' . $fileName);
        }
        $file = file_get_contents($fileName);
        $cities = [];
        foreach (explode("\n", $file) as $key => $city) {
            $cityData = explode("\t", $city);
            if(count($cityData) != 3) {
                throw new Exception('Unsupported file format: ' . $fileName);
            }
            $cityName = $cityData[0];
            if($key == 0 && $cityName != 'Beijing') {
                throw new Exception('Route must beggin in Beijing: ' . $fileName);
            }
            $latitude = floatval($cityData[1]);
            $longitude = floatval($cityData[2]);
            $cities[] = new City($key+1, $cityName, $latitude, $longitude);
        }
        return $cities;    
    }
}