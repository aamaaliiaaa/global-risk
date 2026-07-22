<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $refresh = $request->has('refresh');

        $allCountries = Country::whereNotNull('latitude')->whereNotNull('longitude')->get();

        $weatherByCountryId = $this->intelligenceService->getBulkWeatherData($allCountries, $refresh);

        // Summary Statistics
        $totalMonitored = $allCountries->count();
        $clearCount = 0;
        $adverseCount = 0;
        $windyCount = 0;
        $totalTemp = 0;
        $tempCount = 0;

        $mapWeatherData = [];
        foreach ($allCountries as $c) {
            $w = $weatherByCountryId[$c->id] ?? null;
            $code = $w ? $w['code'] : 0;
            $temp = $w ? $w['temp'] : 24.5;
            $wind = $w ? $w['wind'] : 10.0;
            $humidity = $w ? ($w['humidity'] ?? 60) : 60;
            $conditionInfo = $w ? [
                'condition' => $w['condition'],
                'icon' => $w['icon'],
                'risk' => $w['risk'],
                'badge_class' => $w['badge_class']
            ] : $this->intelligenceService->parseWmoCode($code);

            if (in_array($code, [0, 1, 2])) {
                $clearCount++;
            }
            if (in_array($code, [51, 53, 55, 61, 63, 65, 80, 81, 82, 95, 96, 99])) {
                $adverseCount++;
            }
            if ($wind >= 20) {
                $windyCount++;
            }

            $totalTemp += $temp;
            $tempCount++;

            $mapWeatherData[] = [
                'id' => $c->id,
                'name' => $c->name,
                'flag' => $c->flag,
                'latitude' => $c->latitude,
                'longitude' => $c->longitude,
                'temperature' => $temp,
                'wind_speed' => $wind,
                'humidity' => $humidity,
                'condition' => $conditionInfo['condition'],
                'icon' => $conditionInfo['icon'],
                'risk' => $conditionInfo['risk'],
                'badge_class' => $conditionInfo['badge_class']
            ];
        }

        $avgTemp = $tempCount > 0 ? round($totalTemp / $tempCount, 1) : 24.0;

        // Query paginated list for sidebar / search
        $query = Country::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        $countries = $query->orderBy('name')->paginate(20)->withQueryString();

        $weatherData = [];
        foreach ($countries as $c) {
            $w = $weatherByCountryId[$c->id] ?? null;
            $code = $w ? $w['code'] : 0;
            $temp = $w ? $w['temp'] : 24.5;
            $wind = $w ? $w['wind'] : 10.0;
            $humidity = $w ? ($w['humidity'] ?? 60) : 60;
            $conditionInfo = $w ? [
                'condition' => $w['condition'],
                'icon' => $w['icon'],
                'risk' => $w['risk'],
                'badge_class' => $w['badge_class']
            ] : $this->intelligenceService->parseWmoCode($code);

            $weatherData[] = [
                'id' => $c->id,
                'name' => $c->name,
                'flag' => $c->flag,
                'latitude' => $c->latitude,
                'longitude' => $c->longitude,
                'temperature' => $temp,
                'wind_speed' => $wind,
                'humidity' => $humidity,
                'condition' => $conditionInfo['condition'],
                'icon' => $conditionInfo['icon'],
                'risk' => $conditionInfo['risk'],
                'badge_class' => $conditionInfo['badge_class']
            ];
        }

        $stats = [
            'total' => $totalMonitored,
            'clear' => $clearCount,
            'adverse' => $adverseCount,
            'windy' => $windyCount,
            'avg_temp' => $avgTemp,
            'last_updated' => now()->format('H:i T')
        ];

        $featuredWeather = $weatherData[0] ?? null;

        return view('weather.index', compact('weatherData', 'countries', 'mapWeatherData', 'stats', 'featuredWeather'));
    }
}

