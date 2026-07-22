<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Port;
use App\Models\Country;
use App\Services\RiskIntelligenceService;

class PortController extends Controller
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index(Request $request)
    {
        $query = Port::with('country');

        // Search by port name, city, or country name
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhereHas('country', function ($cq) use ($request) {
                      $cq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by country dropdown
        if ($request->country_id) {
            $query->where('country_id', $request->country_id);
        }

        // Paginated list for the table (25 per page)
        $ports = $query->orderBy('name')->paginate(25)->withQueryString();

        // Separate: all ports with coordinates for the map (no pagination, all points)
        $mapQuery = Port::with('country')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0);

        if ($request->search) {
            $mapQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhereHas('country', function ($cq) use ($request) {
                      $cq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->status) {
            $mapQuery->where('status', $request->status);
        }

        if ($request->country_id) {
            $mapQuery->where('country_id', $request->country_id);
        }

        $mapPorts = $mapQuery->select('id', 'name', 'city', 'latitude', 'longitude', 'status', 'risk_score', 'country_id')
            ->with('country:id,name,flag')
            ->get();

        $totalPorts = Port::count();

        $stats = [
            'total' => $totalPorts,
            'congested' => Port::whereIn('status', ['Delay', 'Congested'])->count(),
            'busy' => Port::where('status', 'Busy')->count(),
            'normal' => Port::where('status', 'Normal')->count(),
        ];

        // Countries that actually have ports, for the dropdown
        $countries = Country::whereHas('ports')
            ->orderBy('name')
            ->get(['id', 'name', 'flag']);

        return view('ports.index', compact('ports', 'mapPorts', 'totalPorts', 'countries', 'stats'));
    }

    public function show(Port $port)
    {
        // Get weather at port coordinates
        $weather = $this->intelligenceService->getWeather($port);
        $condition = $weather['condition'] ?? 'Unknown';

        return view('ports.show', compact('port', 'weather', 'condition'));
    }
}