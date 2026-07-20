<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Services\RiskIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $countries = Country::all();
        
        // Trigger fetching news for all countries to populate cache
        foreach ($countries as $c) {
            $this->intelligenceService->getNews($c);
        }

        // Fetch news from DB
        $newsList = NewsCache::with('country')->orderBy('published_at', 'desc')->get();

        // Aggregate statistics
        $stats = NewsCache::select('sentiment', DB::raw('count(*) as total'))
            ->groupBy('sentiment')
            ->pluck('total', 'sentiment')
            ->toArray();

        $positive = $stats['Positive'] ?? 0;
        $neutral = $stats['Neutral'] ?? 0;
        $negative = $stats['Negative'] ?? 0;
        $total = $positive + $neutral + $negative;

        return view('news.index', compact('newsList', 'positive', 'neutral', 'negative', 'total'));
    }
}
