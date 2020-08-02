<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Path;
use App\Service\FileService;
use App\Service\PathFinderService;

class TravelManController extends AbstractController
{
	const MAX_EXECUTION_TIME = 'PT15M';

    /**
     * @Route("/", name="index")
     */
    public function index(
    	Request $request,
    	FileService $fileService, 
    	PathFinderService $pathfinder
    )
    {
        $fileName = $this->getParameter('kernel.project_dir') . "/resources/cities.txt";
        $citiesList = $fileService->getCitiesList($fileName);

        $algorithm = $request->get('algorithm');
        switch ($algorithm) {
        	case 'Exact Algorithm':
        		$pathfinder->getBestPossibleTravel($citiesList);
        		$route = $pathfinder->shortestRoute;
        		break;
        	case 'Random Route':
        		$route = $pathfinder->getShortestRandPath($citiesList, new \DateInterval(self::MAX_EXECUTION_TIME));
        		break;
        	case 'Nearest Neighbour Algorithm':
        		$route = $pathfinder->getNNPath($citiesList);
        		break;
        	case 'None':
        		$route = new Path($citiesList);
        		break;
        	default:
        	case 'Genetic Algorithm':
        		$route = $pathfinder->getShortestPath($citiesList, new \DateInterval(self::MAX_EXECUTION_TIME));
        		break;
        }

        return $this->render('travelman.html.twig', [
        	'algorithm' => $algorithm,
        	'route' => $route,
        	'distance' => intval($pathfinder->getTravelDistance($route)/1000)
        ]);
    }
}