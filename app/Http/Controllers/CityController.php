<?php

namespace App\Http\Controllers;

use App\City;
use App\External\OpenWeather\OpenWeather;
use App\Repositories\CityRepository;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * @var OpenWeather
     */
    protected $apiWeather;

    protected $cities;

    /**
     * CityController constructor.
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->middleware('auth');
        $this->apiWeather = new OpenWeather();
        $this->cities = new CityRepository($city);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request){
        if(!$request->ajax()){
            return response()->json('invalid call', 503);
        }
        $jsonResponse = $this->cities->searchDataTable(array('id', 'api_id', 'name', 'country_code'), $request);
        return response()->json($jsonResponse, 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function compareWeather(Request $request){
        $citiesId = $request->get('city-id');
        $data = $this->apiWeather->getWeatherCities($citiesId, true);
        return view('compare', array('data' => $data));
    }
}
