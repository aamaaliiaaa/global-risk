<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $rates = $this->intelligenceService->getExchangeRates();
        $countries = Country::all();

        // Prepare charts data (fluctuation of currencies over the last few months)
        $chartData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        foreach ($countries as $c) {
            $currency = strtoupper($c->currency);
            $rate = $rates[$currency] ?? 1.0;
            
            // Mock currency trends based on current rate
            $trend = [];
            for ($i = 5; $i >= 0; $i--) {
                $fluctuation = (sin($i + $rate) * ($rate * 0.02)); 
                $trend[] = round($rate - $fluctuation, 4);
            }

            $chartData[$c->name] = [
                'currency' => $currency,
                'rate' => $rate,
                'trend' => $trend
            ];
        }

        return view('currency.index', compact('rates', 'chartData', 'months'));
    }
}
