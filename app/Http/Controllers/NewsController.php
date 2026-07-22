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

    public function index(Request $request)
    {
        $refresh = $request->has('refresh');

        // Populate live news for major countries if empty or refresh requested
        if ($refresh || NewsCache::count() === 0) {
            $majorCountries = Country::whereIn('name', [
                'Indonesia', 'United States', 'China', 'Singapore', 'Australia', 
                'Japan', 'Germany', 'United Kingdom', 'Malaysia', 'Vietnam', 'Thailand', 'India'
            ])->get();

            if ($majorCountries->isEmpty()) {
                $majorCountries = Country::take(10)->get();
            }

            foreach ($majorCountries as $c) {
                $this->intelligenceService->getNews($c, $refresh);
            }
        }

        $query = NewsCache::with('country')->orderBy('published_at', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('country', function($cq) use ($request) {
                      $cq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->sentiment) {
            $query->where('sentiment', $request->sentiment);
        }

        // Fetch paginated news from DB
        $newsList = $query->paginate(15)->withQueryString();

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

