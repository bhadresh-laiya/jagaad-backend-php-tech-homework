<?php

namespace App\External\OpenWeather;

use GuzzleHttp\Client;

class OpenWeather
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var string
     */
    private $units;
    /**
     * @var array
     */
    private $tempRange;
    /**
     * @var string
     */
    private $weatherClearCode;
    /**
     * @var array
     */
    private $othersWeatherCodes;

    private $othersWeatherCodesNotIn;
    /**
     * @var string
     */
    private $baseUri;

    /**
     * OpenWeather constructor.
     */
    public function __construct()
    {
        $this->baseUri = env('OWN_BASE_URI');
        $this->apiKey = env('OWM_API_KEY');
        $this->units = env('OWM_UNITS');
        $this->tempRange = explode(',',env('OWM_TEMP_RANGE'));
        $this->weatherClearCode = env('OWN_WEATHER_CLEAR');
        $this->othersWeatherCodes = explode(',', env('OWN_WEATHER_OTHER_OK'));
        $this->othersWeatherCodesNotIn = explode(',', env('OWN_WEATHER_OTHER_NOT'));
        $this->client = new Client();
        return $this;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $days
     * @return mixed
     */
    public function getWeatherByLatLonPerDay(float $latitude, float $longitude){
        $response = $this->client->get($this->baseUri.'onecall', [
            'query' => [
                'appid' => $this->apiKey,
                'units' => $this->units,
                'lat' => $latitude,
                'lon' => $longitude,
                'exclude' => 'current,minutely,hourly,alerts'
            ]
        ]);
        return \GuzzleHttp\json_decode($response->getBody()->getContents());
    }

    /**
     * @param array $ids
     * @param bool $compare
     * @return array
     */
    public function getWeatherCities(array $ids, bool $compare = false):array{
        $responseApi = $this->client->get($this->baseUri.'group', array(
            'query' => array(
                'appid' => $this->apiKey,
                'units' => $this->units,
                'id' => implode(',', $ids)
            )
        ));
        $response = array();
        $response['code'] = $responseApi->getStatusCode();
        $data = \GuzzleHttp\json_decode($responseApi->getBody()->getContents());
        if($compare){
            $response['data'] = $this->compareWeatherCities($data);
        } else {
            $response['data'] = $data;
        }
        return $response;
    }

    /**
     * @param \stdClass $data
     * @return \stdClass
     */
    private function compareWeatherCities(\stdClass $data):\stdClass{
        $tempData = $this->getTemperature($data);
        $weatherData = $this->getWeather($data);
        $data = $this->mergeData($tempData, $weatherData, $data);
        return $data;
    }

    /**
     * @param \stdClass $data
     * @return array
     */
    private function getTemperature(\stdClass $data):array{
        $rangeOk = array();
        $others = array();
        foreach ($data->list as $city){
            $mainTemp = $city->main->temp;
           if($mainTemp >= $this->tempRange[0] && $mainTemp <= $this->tempRange[1]){
               $rangeOk[$city->id] = $city->main->temp;
           } else {
               $others[$city->id] = $mainTemp;
           }
        }
        $response['range_ok'] = $rangeOk;
        $response['others'] = $others;
        return $response;
    }

    /**
     * @param \stdClass $data
     * @return array
     */
    private function getWeather(\stdClass $data):array{
        $rangeOk = array();
        $others = array();
        foreach ($data->list as $city){
            $weatherId = $city->weather[0]->id;
            if($weatherId == $this->weatherClearCode || in_array($weatherId, $this->othersWeatherCodes)){
                $rangeOk[$city->id] = $weatherId;
            } else if(true) {
                $others[$city->id] = $weatherId;
            }
        }
        $response['range_ok'] = $rangeOk;
        $response['others'] = $others;
        return $response;
    }

    /**
     * @param array $tempData
     * @param array $weatherData
     * @param \stdClass $data
     * @return \stdClass
     */
    private function mergeData(array $tempData, array $weatherData, \stdClass $data):\stdClass{
        $bestCities = array();
        /**
         * All range ok found
         */
        if(!empty($tempData['range_ok']) && !empty($weatherData['range_ok'])){
            foreach ($tempData['range_ok'] as $cityId => $cityTemp){
                if(key_exists($cityId, $weatherData['range_ok'])){
                    $bestCities[$cityId]['temperature'] = $cityTemp;
                    $bestCities[$cityId]['weather_code'] = $weatherData['range_ok'][$cityId];
                }
            }
        }
        /**
         * Not Temp but Weather
         */
        if(empty($bestCities) && !empty($tempData['others']) && !empty($weatherData['range_ok'])){
            foreach ($tempData['others'] as $cityId => $cityTemp){
                if(key_exists($cityId, $weatherData['range_ok'])){
                    $bestCities[$cityId]['temperature'] = $cityTemp;
                    $bestCities[$cityId]['weather_code'] = $weatherData['range_ok'][$cityId];
                }
            }
        }
        /**
         * Not Temp Not Weather
         */
        if(empty($bestCities) && !empty($tempData['others']) && !empty($weatherData['others'])){
            //TODO
        }
        /**
         *  Get the best of All
         */
        if(!empty($bestCities)){
            $maxTemp = max(array_column($bestCities, 'temperature'));
            $maxWeather = max(array_column($bestCities, 'weather_code'));
            foreach ($bestCities as $idCity => $bestCity){
                if($bestCity['temperature'] == $maxTemp && $bestCity['weather_code'] === $maxWeather){
                    $data->best_city_temp[$idCity] = $bestCity;
                    $data->best_city_weather[$idCity] = $bestCity;
                    break;
                }
                if(!isset($data->best_city_temp) && $bestCity['temperature'] == $maxTemp){
                    $data->best_city_temp[$idCity] = $bestCity;
                }
                if(!isset($data->best_city_weather) && $bestCity['weather_code'] == $maxWeather){
                    $data->best_city_temp[$idCity] = $bestCity;
                }
            }
        }
        return $data;
    }
}
