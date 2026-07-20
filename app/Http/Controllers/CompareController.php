<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\RiskScoringService;
use App\Services\RiskIntelligenceService;
use App\Models\CountryIndicator;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    protected $scoringService;
    protected $intelligenceService;

    public function __construct(RiskScoringService $scoringService, RiskIntelligenceService $intelligenceService)
    {
        $this->scoringService = $scoringService;
        $this->intelligenceService = $intelligenceService;
    }

    public function index(Request $request)
    {
        $countries = Country::all();
        $countryA = null;
        $countryB = null;
        $comparison = null;

        if ($request->country_a && $request->country_b) {
            $countryA = Country::find($request->country_a);
            $countryB = Country::find($request->country_b);

            if ($countryA && $countryB) {
                // Ensure risk scores and data are updated
                $riskA = $this->scoringService->calculateCountryRisk($countryA);
                $riskB = $this->scoringService->calculateCountryRisk($countryB);

                // Fetch World Bank indicators
                $indA = $this->intelligenceService->getWorldBankData($countryA);
                $indB = $this->intelligenceService->getWorldBankData($countryB);

                $latestIndA = collect($indA)->sortByDesc('year')->first();
                $latestIndB = collect($indB)->sortByDesc('year')->first();

                // Exchange rates
                $rates = $this->intelligenceService->getExchangeRates();
                $rateA = $rates[strtoupper($countryA->currency)] ?? 1.0;
                $rateB = $rates[strtoupper($countryB->currency)] ?? 1.0;

                // Weather
                $weatherA = $this->intelligenceService->getWeather($countryA);
                $weatherB = $this->intelligenceService->getWeather($countryB);

                $comparison = [
                    'a' => [
                        'name' => $countryA->name,
                        'flag' => $countryA->flag,
                        'risk_score' => $riskA['total_risk'],
                        'risk_level' => $riskA['risk_level'],
                        'gdp' => $latestIndA ? $latestIndA['gdp'] : null,
                        'inflation' => $latestIndA ? $latestIndA['inflation'] : null,
                        'population' => $latestIndA ? $latestIndA['population'] : null,
                        'currency' => strtoupper($countryA->currency),
                        'exchange_rate' => $rateA,
                        'weather' => $weatherA
                    ],
                    'b' => [
                        'name' => $countryB->name,
                        'flag' => $countryB->flag,
                        'risk_score' => $riskB['total_risk'],
                        'risk_level' => $riskB['risk_level'],
                        'gdp' => $latestIndB ? $latestIndB['gdp'] : null,
                        'inflation' => $latestIndB ? $latestIndB['inflation'] : null,
                        'population' => $latestIndB ? $latestIndB['population'] : null,
                        'currency' => strtoupper($countryB->currency),
                        'exchange_rate' => $rateB,
                        'weather' => $weatherB
                    ]
                ];
            }
        }

        return view('compare.index', compact('countries', 'countryA', 'countryB', 'comparison'));
    }
}
