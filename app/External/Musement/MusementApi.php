<?php

namespace App\External\Musement;

require_once app_path('External/Weatherapicom/vendor/autoload.php');

use App\City;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class MusementApi
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string 99ca6caa3f784572a6a112303211110
     */
    private $apiKey; 
    // My original API key which are already set into .env file

    /**
     * MusementApi constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.musement.com/'
        ]);
        $this->baseUri = env('OWN_BASE_URI');
        $this->apiKey = env('OWM_API_KEY');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCities(){
        $request = $this->client->get('api/v3/cities');
        $cities = \GuzzleHttp\json_decode($request->getBody()->getContents());
        $citiesDb = [];
        $count = 0;
        foreach ($cities as $city){
            $cityDb = DB::table('cities')->select(['api_id', 'coord_lat', 'coord_lon'])->where([
                'name' => $city->name,
                'country_code' => strtoupper($city->country->iso_code)
            ])->first();
            if(empty($cityDb)){
                continue;
            }
            $weatherMuseAPI = new \WeatherAPILib\WeatherAPIClient($this->apiKey);
            $aPIs = $weatherMuseAPI->getAPIs();

            $weatherData = $aPIs->getForecastWeather($cityDb->coord_lat.','.$cityDb->coord_lon, '2');

            $count++;
            /**
             * weather limit call 60 per minutes
             */
            if(($count % 60) === 0){
                sleep(60);
            }
            $citiesDb[$cityDb->api_id] = [
                'name' => $city->name,
                'country' => $city->country->name,
                'weather' => [
                    ucfirst($weatherData->forecast->forecastday[0]->day->condition->text),
                    ucfirst($weatherData->forecast->forecastday[1]->day->condition->text)
                ]
            ];
        }
        return $citiesDb;
    }
}
