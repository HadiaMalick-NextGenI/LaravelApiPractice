<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://api.weatherapi.com/v1/',
            'timeout'  => 5.0,
        ]);
        $this->apiKey = env('WEATHER_API_KEY');
    }

    public function getCurrentWeather($location)
    {
        try {
            $response = $this->client->request('GET', 'current.json', [
                'query' => [
                    'key' => $this->apiKey,
                    'q' => $location,
                ],
            ]);
            $weatherDetails = json_decode($response->getBody()->getContents(), true);;
            return ApiResponse::success(data: $weatherDetails, message:"Weather Details");
            //return json_decode($response->getBody(), true);

        } catch (RequestException $e) {
            return [
                'error' => 'Could not fetch weather data.',
                'details' => $e->getMessage(),
            ];
        }
    }

    public function getMultipleWeatherData($locations){
        try{
            $promises = [];
            foreach($locations as $location){
                $promises[$location] = $this->client->getAsync('current.json', [
                    'query' => [
                        'key' => $this->apiKey,
                        'q' => $location,
                    ],
                ]);
            }

            $responses = Promise\Utils::settle($promises)->wait();

            $weatherData = [];

            foreach($responses as $location => $response){
                if($response['state'] == 'fulfilled'){
                    $weatherData[$location] = json_decode($response['value']->getBody(), true);
                }else{
                    $weatherData[$location] = [
                        'error' => 'Could not fetch weather data.',
                        'details' => $response['reason']->getMessage(),
                    ];
                }
            }

            return $weatherData;

        }catch(RequestException $e){
            return[
                'error' => 'Could not fetch data for multiple locations.',
                'details' => $e->getMessage(),
            ];
        }
    }
}