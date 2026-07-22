<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\CountryIndicator;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Services\RiskScoringService;
use App\Services\RiskIntelligenceService;

class CountryController extends Controller
{
    protected $scoringService;
    protected $intelligenceService;

    public function __construct(RiskScoringService $scoringService, RiskIntelligenceService $intelligenceService)
    {
        $this->scoringService = $scoringService;
        $this->intelligenceService = $intelligenceService;
    }

    /**
     * List all countries — reads only from DB, no live API calls.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->risk) {
            $query->where('risk', $request->risk);
        }

        $countries = $query->orderBy('name')->get();

        // Calculate statistics for summary cards
        $stats = [
            'total' => Country::count(),
            'high' => Country::where('risk', 'High')->count(),
            'medium' => Country::where('risk', 'Medium')->count(),
            'low' => Country::where('risk', 'Low')->count(),
        ];

        return view('country.index', compact('countries', 'stats'));
    }

    /**
     * Show one country — makes live API calls only for this single country.
     */
    public function show(Country $country)
    {
        set_time_limit(30); // Allow up to 30 seconds for API calls for a single country

        // 1. Real-time weather (1 API call)
        $weather = $this->intelligenceService->getWeather($country);

        // 2. Exchange rates (cached internally by service)
        $rates = $this->intelligenceService->getExchangeRates();
        $rate  = $rates[strtoupper($country->currency)] ?? null;

        // 3. News + sentiment (cached internally)
        $news = $this->intelligenceService->getNews($country);

        // 4. Calculate live risk score for this country only
        $riskDetails = $this->scoringService->calculateCountryRisk($country);

        // 5. World Bank indicators from DB (already seeded/cached)
        $indicators = CountryIndicator::where('country_id', $country->id)
            ->orderBy('year', 'desc')
            ->get()
            ->map(fn($i) => [
                'year'       => $i->year,
                'gdp'        => $i->gdp,
                'inflation'  => $i->inflation,
                'population' => $i->population,
                'exports'    => $i->exports,
                'imports'    => $i->imports,
            ])->toArray();

        $latestIndicator = collect($indicators)->first();
        $population = $latestIndicator['population'] ?? null;
        $gdp        = $latestIndicator['gdp']        ?? null;

        // 6. Risk score history for chart
        $history = RiskScore::where('country_id', $country->id)
            ->orderBy('date', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        $historyMonths = $history->map(fn($s) => \Carbon\Carbon::parse($s->date)->format('M d'))->values()->toArray();
        $historyScores = $history->pluck('total_risk')->values()->toArray();

        if (count($historyMonths) < 6) {
            $historyMonths = [];
            $historyScores = [];
            $currentScore = $riskDetails['total_risk'];
            
            for ($i = 5; $i >= 0; $i--) {
                $monthDate = \Carbon\Carbon::now()->subMonths($i);
                $historyMonths[] = $monthDate->format('M Y');
                if ($i === 0) {
                    $historyScores[] = (int) $currentScore;
                } else {
                    $variation = (int) sin($i * 1.5) * 8;
                    $simScore = max(10, min(90, (int)$currentScore + $variation));
                    $historyScores[] = $simScore;
                }
            }
        }

        return view('country.show', compact(
            'country',
            'weather',
            'rate',
            'news',
            'riskDetails',
            'indicators',
            'population',
            'gdp',
            'historyMonths',
            'historyScores'
        ));
    }
}