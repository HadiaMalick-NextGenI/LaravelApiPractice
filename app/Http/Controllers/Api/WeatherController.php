<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService){
        $this->weatherService = $weatherService;
    }

    public function getCurrentWeather(Request $request){
        $location = $request->query('location', 'London');
        $data = $this->weatherService->getCurrentWeather($location);

        return response()->json($data);
    }

    public function getMultipleWeather(Request $request)
    {
        $locations = $request->query('locations', ['London', 'New York', 'Tokyo']); // Default to a few locations
        $data = $this->weatherService->getMultipleWeatherData($locations);

        return response()->json($data);
    }

}
