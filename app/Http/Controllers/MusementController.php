<?php

namespace App\Http\Controllers;

use App\City;
use App\External\Musement\MusementApi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MusementController extends Controller
{
    /**
     * @var MusementApi
     */
    private $musementApi;

    /**
     * MusementController constructor.
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->musementApi = new MusementApi();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCitiesWeather(){
        // $response = [];
        foreach ($this->musementApi->getCities() as $city){
            // array_push($response, [
            //     'Processed city '.$city['name'].' - '.$city['country'].' | '.implode(' - ', $city['weather'])
            // ]);
            echo 'Processed city '.$city['name'].' - '.$city['country'].' | '.implode(' - ', $city['weather']).'<br>';
        }
        // return response()->json($response, Response::HTTP_OK);
    }
}
