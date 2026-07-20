<?php

use Illuminate\Support\Facades\Route;
use App\Models\Country;
use App\Models\Port;
use App\Models\NewsCache;
use App\Models\CurrencyCache;
use App\Services\RiskScoringService;
use App\Services\RiskIntelligenceService;

Route::get('/countries', function () {
    $countries = Country::all();
    $riskScoring = app(RiskScoringService::class);
    
    $result = [];
    foreach ($countries as $country) {
        $calc = $riskScoring->calculateCountryRisk($country);
        $result[] = [
            'id' => $country->id,
            'name' => $country->name,
            'flag' => $country->flag,
            'risk_level' => $calc['risk_level'],
            'total_risk_score' => $calc['total_risk_score'] ?? $calc['total_risk'],
            'weather' => $calc['weather_condition']
        ];
    }
    return response()->json($result);
});

Route::get('/risk', function () {
    $countryId = request('country_id');
    $country = Country::find($countryId);
    if (!$country) {
        return response()->json(['error' => 'Country not found'], 404);
    }
    
    $riskScoring = app(RiskScoringService::class);
    $calc = $riskScoring->calculateCountryRisk($country);
    
    $history = $country->riskScores()->orderBy('date', 'asc')->get();
    
    return response()->json([
        'country' => $country->name,
        'current_calculation' => $calc,
        'history' => $history
    ]);
});

Route::get('/ports', function () {
    $ports = Port::with('country')->get();
    $intelligence = app(RiskIntelligenceService::class);
    
    $result = [];
    foreach ($ports as $port) {
        $weather = $intelligence->getWeather($port);
        $result[] = [
            'id' => $port->id,
            'name' => $port->name,
            'city' => $port->city,
            'country' => $port->country->name,
            'latitude' => $port->latitude,
            'longitude' => $port->longitude,
            'status' => $port->status,
            'risk_score' => $port->risk_score,
            'weather' => $weather
        ];
    }
    return response()->json($result);
});

Route::get('/news', function () {
    $countryId = request('country_id');
    $country = Country::find($countryId);
    
    $intelligence = app(RiskIntelligenceService::class);
    
    if ($country) {
        $news = $intelligence->getNews($country);
        return response()->json($news);
    }
    
    // Return news from all countries
    $news = NewsCache::with('country')->orderBy('created_at', 'desc')->take(15)->get();
    return response()->json($news);
});

Route::get('/currency', function () {
    $intelligence = app(RiskIntelligenceService::class);
    $rates = $intelligence->getExchangeRates();
    return response()->json($rates);
});

Route::get('/test-apis', function () {
    $results = [];
    
    // Test Open-Meteo
    try {
        $res = Http::get('https://api.open-meteo.com/v1/forecast?latitude=-6.2000&longitude=106.8167&current=temperature_2m');
        $results['open_meteo'] = $res->successful() ? 'SUCCESS' : 'FAILED: ' . $res->status();
    } catch (\Exception $e) {
        $results['open_meteo'] = 'ERROR: ' . $e->getMessage();
    }

    // Test ExchangeRate
    try {
        $res = Http::get('https://open.er-api.com/v6/latest/USD');
        $results['exchangerate'] = $res->successful() ? 'SUCCESS' : 'FAILED: ' . $res->status();
    } catch (\Exception $e) {
        $results['exchangerate'] = 'ERROR: ' . $e->getMessage();
    }

    // Test World Bank
    try {
        $res = Http::get('http://api.worldbank.org/v2/country/IDN/indicator/FP.CPI.TOTL.ZG?format=json&date=2023:2023');
        $results['world_bank'] = $res->successful() ? 'SUCCESS' : 'FAILED: ' . $res->status();
    } catch (\Exception $e) {
        $results['world_bank'] = 'ERROR: ' . $e->getMessage();
    }

    // Test GNews
    try {
        $apiKey = env('GNEWS_API_KEY') ?: '43ad4068a0ffd6d0115e341fe80a8432';
        $res = Http::get('https://gnews.io/api/v4/search', [
            'q' => 'indonesia',
            'lang' => 'en',
            'max' => 1,
            'apikey' => $apiKey
        ]);
        $results['gnews'] = $res->successful() ? 'SUCCESS' : 'FAILED: ' . $res->status() . ' - ' . json_encode($res->json());
    } catch (\Exception $e) {
        $results['gnews'] = 'ERROR: ' . $e->getMessage();
    }

    return response()->json($results);
});
