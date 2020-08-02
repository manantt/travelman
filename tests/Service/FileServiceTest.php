<?php

namespace App\tests\Service;

use App\Service\FileService;
use App\Entity\City;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\Exception;

class FileServiceTest extends TestCase
{
    public function testGetCitiesList1()
    {
        $testFileName = "tests\\testGetCitiesList.txt";
        // if a test failed, the file may already exist
        if(file_exists($testFileName)) { 
        	unlink($testFileName);
        }
        $fileService = new FileService();

        $this->expectException('Exception');
        $this->expectExceptionMessage('File not found in ' . $testFileName);
        $citiesList = $fileService->getCitiesList($testFileName);
    }

    public function testGetCitiesList2()
    {
        $testFileName = "tests\\testGetCitiesList.txt";
        $fileService = new FileService();
        $testText1 = "City1\t1\nCity2\t2\t2\nCity3\t3\t3";

        file_put_contents($testFileName, $testText1);
        $this->expectException('Exception');
        $this->expectExceptionMessage('Unsupported file format: ' . $testFileName);
        $citiesList = $fileService->getCitiesList($testFileName);
    }

    public function testGetCitiesList3()
    {
        $testFileName = "tests\\testGetCitiesList.txt";
        $fileService = new FileService();
        $testText2 = "City1\t1\t1\nCity2\t2\t2\nCity3\t3\t3";

        file_put_contents($testFileName, $testText2);
        $this->expectException('Exception');
        $this->expectExceptionMessage('Route must beggin in Beijing: ' . $testFileName);
        $citiesList = $fileService->getCitiesList($testFileName);
    }

    public function testGetCitiesList4()
    {
        $testFileName = "tests\\testGetCitiesList.txt";
        $fileService = new FileService();
        $testText3 = "Beijing\t1\t1\nCity2\t2\t2\nCity3\t3\t3";

        file_put_contents($testFileName, $testText3);
        $citiesList = $fileService->getCitiesList($testFileName);
        $this->assertEquals(count($citiesList), 3);
        $this->assertEquals($citiesList[0], new City(1, "Beijing", 1, 1));
        $this->assertEquals($citiesList[1], new City(2, "City2", 2, 2));
        $this->assertEquals($citiesList[2], new City(3, "City3", 3, 3));
        unlink($testFileName);
    }
}