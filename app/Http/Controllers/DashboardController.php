<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsCache;
use App\Models\RiskScore;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Load directly from DB (no live API calls) ────────────────────────
        $countries = Country::select('id','name','flag','risk','risk_score','weather','latitude','longitude','currency')
            ->get();

        $totalCountries = $countries->count();
        $highRisk   = $countries->where('risk', 'High')->count();
        $mediumRisk = $countries->where('risk', 'Medium')->count();
        $lowRisk    = $countries->where('risk', 'Low')->count();

        // ── Build heatmap data straight from cached columns ───────────────────
        $heatmapData = $countries->map(function ($c) {
            $score = $c->risk_score ?? 25;
            return [
                'name'       => $c->name,
                'flag'       => $c->flag,
                'latitude'   => $c->latitude,
                'longitude'  => $c->longitude,
                'risk_score' => $score,
                'risk_level' => $c->risk,
                'weather'    => $c->weather,
            ];
        })->filter(fn($c) => $c['latitude'] != 0 || $c['longitude'] != 0)->values()->toArray();

        // ── Latest news from cache ────────────────────────────────────────────
        $newsList = NewsCache::with('country')
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $totalNewsToday = NewsCache::whereDate('published_at', Carbon::today())->count();
        if ($totalNewsToday === 0) {
            $totalNewsToday = NewsCache::count();
        }

        // ── Sentiment stats ───────────────────────────────────────────────────
        $sentimentStats = NewsCache::select('sentiment', DB::raw('count(*) as total'))
            ->groupBy('sentiment')
            ->pluck('total', 'sentiment')
            ->toArray();

        $positiveSentiment = $sentimentStats['Positive'] ?? 0;
        $neutralSentiment  = $sentimentStats['Neutral']  ?? 0;
        $negativeSentiment = $sentimentStats['Negative'] ?? 0;

        // Show some defaults when no news cached yet
        if ($positiveSentiment + $neutralSentiment + $negativeSentiment === 0) {
            $positiveSentiment = 40;
            $neutralSentiment  = 30;
            $negativeSentiment = 30;
        }

        // ── Global risk trend (6 months average) from DB ─────────────────────
        $trendData = RiskScore::select(
            DB::raw("DATE_FORMAT(date, '%b %Y') as month"),
            DB::raw("DATE_FORMAT(date, '%Y%m') as sort_key"),
            DB::raw('AVG(total_risk) as avg_risk')
        )
        ->where('date', '>=', Carbon::now()->subMonths(6)->startOfMonth())
        ->groupBy('month', 'sort_key')
        ->orderBy('sort_key')
        ->get();

        $months = [];
        $scores = [];
        foreach ($trendData as $trend) {
            $months[] = $trend->month;
            $scores[] = round($trend->avg_risk);
        }

        if (empty($months)) {
            $months = ['Feb 2026','Mar 2026','Apr 2026','May 2026','Jun 2026','Jul 2026'];
            $scores = [30, 35, 32, 40, 38, 42];
        }

        return view('dashboard.index', compact(
            'totalCountries',
            'highRisk',
            'mediumRisk',
            'lowRisk',
            'totalNewsToday',
            'heatmapData',
            'newsList',
            'positiveSentiment',
            'neutralSentiment',
            'negativeSentiment',
            'months',
            'scores'
        ));
    }
}
