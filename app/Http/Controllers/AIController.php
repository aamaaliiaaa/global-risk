<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\AIRiskAnalystService;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;
    protected $riskIntelligence;

    public function __construct(AIRiskAnalystService $aiService, RiskIntelligenceService $riskIntelligence)
    {
        $this->aiService = $aiService;
        $this->riskIntelligence = $riskIntelligence;
    }

    public function generateReport(Request $request)
    {
        $countryId = $request->input('country_id');
        $country = Country::find($countryId) ?? Country::first();

        if (!$country) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        try {
            $weather = $this->riskIntelligence->getWeather($country);
        } catch (\Exception $e) {
            $weather = [
                'temperature' => 24.5,
                'wind_speed' => 12.0,
                'condition' => 'Partly Cloudy'
            ];
        }

        $report = $this->aiService->generateCountryReport($country, $weather, []);

        return response()->json([
            'status' => 'success',
            'report' => $report
        ]);
    }

    public function chat(Request $request)
    {
        $prompt = $request->input('message', '');
        if (empty($prompt)) {
            return response()->json(['response' => 'Ketik pertanyaan untuk memulai analisis AI.']);
        }

        $response = $this->aiService->processChatQuery($prompt);

        return response()->json([
            'status' => 'success',
            'response' => $response
        ]);
    }
}
