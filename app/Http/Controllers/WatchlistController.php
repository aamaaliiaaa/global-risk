<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use App\Services\RiskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    protected $scoringService;

    public function __construct(RiskScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    public function index()
    {
        $userId = Auth::id();
        $watchlistItems = Watchlist::where('user_id', $userId)->with('country')->get();
        
        $watchlistData = [];
        foreach ($watchlistItems as $item) {
            $country = $item->country;
            $riskData = $this->scoringService->calculateCountryRisk($country);
            
            $watchlistData[] = [
                'id' => $item->id,
                'country_id' => $country->id,
                'name' => $country->name,
                'flag' => $country->flag,
                'risk_score' => $riskData['total_risk'],
                'risk_level' => $riskData['risk_level'],
                'weather' => $riskData['weather_condition'],
                'currency' => strtoupper($country->currency),
            ];
        }

        $allCountries = Country::whereNotIn('id', $watchlistItems->pluck('country_id'))->get();

        return view('watchlist.index', compact('watchlistData', 'allCountries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id'
        ]);

        Watchlist::updateOrCreate([
            'user_id' => Auth::id(),
            'country_id' => $request->country_id
        ]);

        return redirect()->route('watchlist.index')->with('success', 'Country added to watchlist.');
    }

    public function destroy($id)
    {
        $item = Watchlist::where('id', $id)->where('user_id', Auth::id())->first();
        if ($item) {
            $item->delete();
            return redirect()->route('watchlist.index')->with('success', 'Country removed from watchlist.');
        }

        return redirect()->route('watchlist.index')->with('error', 'Item not found.');
    }
}
