<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryIndicator;
use App\Models\RiskScore;
use Carbon\Carbon;

class RiskScoringService
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    /**
     * Compute and save country risk score dynamically.
     *
     * @param Country $country
     * @return array
     */
    public function calculateCountryRisk(Country $country): array
    {
        // 1. Weather Risk (0-100)
        $weatherData = $this->intelligenceService->getWeather($country);
        $code = $weatherData['weather_code'] ?? 0;
        
        $weatherRisk = match ($code) {
            0 => 10,              // Clear
            1, 2, 3 => 20,         // Partly Cloudy
            45, 48 => 35,         // Fog
            51, 53, 55 => 45,     // Drizzle
            61, 63, 65 => 65,     // Rain
            80, 81, 82 => 70,     // Showers
            71, 73, 75 => 80,     // Snow
            95, 96, 99 => 100,    // Thunderstorm
            default => 25         // Cloudy / Other
        };

        // 2. Inflation Risk (0-100)
        $this->intelligenceService->getWorldBankData($country);
        
        // Get the latest inflation indicator
        $latestIndicator = CountryIndicator::where('country_id', $country->id)
            ->whereNotNull('inflation')
            ->orderBy('year', 'desc')
            ->first();

        $inflationVal = $latestIndicator ? $latestIndicator->inflation : 2.5;

        if ($inflationVal < 0) {
            $inflationRisk = 40; // Deflation
        } elseif ($inflationVal <= 3.5) {
            $inflationRisk = 15; // Safe range
        } elseif ($inflationVal <= 6.0) {
            $inflationRisk = 45; // Moderate
        } elseif ($inflationVal <= 10.0) {
            $inflationRisk = 75; // High
        } else {
            $inflationRisk = 95; // Hyperinflation
        }

        // 3. Currency Risk (0-100)
        $currencyCode = strtoupper($country->currency);
        $currencyRisk = match ($currencyCode) {
            'USD' => 10,
            'EUR', 'SGD', 'GBP', 'AUD' => 25,
            'CAD', 'JPY' => 35,
            'CNY', 'IDR' => 50,
            default => 60
        };

        // Add a slight pseudo-fluctuation based on today's rate
        $rates = $this->intelligenceService->getExchangeRates();
        $rate = $rates[$currencyCode] ?? 1.0;
        
        // Adjust risk slightly based on rate variation (just dynamic effect)
        $factor = (sin($rate) * 5); 
        $currencyRisk = max(5, min(95, round($currencyRisk + $factor)));

        // 4. News Sentiment Risk (0-100)
        $newsList = $this->intelligenceService->getNews($country);
        $totalNews = count($newsList);
        $negativeCount = 0;
        $positiveCount = 0;

        foreach ($newsList as $news) {
            if ($news['sentiment'] === 'Negative') {
                $negativeCount++;
            } elseif ($news['sentiment'] === 'Positive') {
                $positiveCount++;
            }
        }

        if ($totalNews > 0) {
            // Formula: ratio of negative news to total evaluated sentiment news
            $sentimentRisk = ($negativeCount / ($positiveCount + $negativeCount + 1)) * 100;
        } else {
            $sentimentRisk = 50; // Neutral fallback
        }
        $newsSentimentRisk = round($sentimentRisk);

        // 5. Total Weighted Risk Calculation
        // Weights: Weather (20%), Inflation (30%), Currency (20%), News Sentiment (30%)
        $totalRisk = ($weatherRisk * 0.20) + ($inflationRisk * 0.30) + ($currencyRisk * 0.20) + ($newsSentimentRisk * 0.30);
        $totalRisk = round(max(0, min(100, $totalRisk)));

        // Classify risk level
        $riskLevel = 'Low';
        if ($totalRisk > 65) {
            $riskLevel = 'High';
        } elseif ($totalRisk >= 35) {
            $riskLevel = 'Medium';
        }

        // Save risk score to history table for charting
        $today = Carbon::today()->format('Y-m-d');
        RiskScore::updateOrCreate(
            [
                'country_id' => $country->id,
                'date' => $today
            ],
            [
                'weather_risk' => $weatherRisk,
                'inflation_risk' => $inflationRisk,
                'currency_risk' => $currencyRisk,
                'news_sentiment_risk' => $newsSentimentRisk,
                'total_risk' => $totalRisk
            ]
        );

        // Update the country's risk and weather cached columns in the database as well
        $country->update([
            'risk' => $riskLevel,
            'weather' => $weatherData['condition']
        ]);

        return [
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'news_sentiment_risk' => $newsSentimentRisk,
            'total_risk' => $totalRisk,
            'risk_level' => $riskLevel,
            'weather_condition' => $weatherData['condition'],
            'inflation_rate' => $inflationVal
        ];
    }
}
