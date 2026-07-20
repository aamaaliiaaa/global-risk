<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $countries = Country::all();
        $weatherData = [];

        foreach ($countries as $c) {
            $weather = $this->intelligenceService->getWeather($c);
            $weatherData[] = [
                'name' => $c->name,
                'flag' => $c->flag,
                'latitude' => $c->latitude,
                'longitude' => $c->longitude,
                'temperature' => $weather['temperature'],
                'wind_speed' => $weather['wind_speed'],
                'condition' => $weather['condition']
            ];
        }

        return view('weather.index', compact('weatherData'));
    }
}
