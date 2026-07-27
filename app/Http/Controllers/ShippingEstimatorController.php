<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Port;
use App\Services\ShippingEstimatorService;

class ShippingEstimatorController extends Controller
{
    protected $estimatorService;

    public function __construct(ShippingEstimatorService $estimatorService)
    {
        $this->estimatorService = $estimatorService;
    }

    public function index(Request $request)
    {
        // Load all available ports with countries for dropdown
        $ports = Port::with('country:id,name,flag')
            ->orderBy('name')
            ->get();

        $originId = $request->query('origin_id') ?? $request->input('origin_id');
        $destinationId = $request->query('destination_id') ?? $request->input('destination_id');
        $vesselSpeed = (float) ($request->input('vessel_speed') ?? $request->query('vessel_speed') ?? 18.0);
        $departureDate = $request->input('departure_date') ?? $request->query('departure_date') ?? now()->format('Y-m-d\TH:i');

        // Set intelligent default ports on initial view if no ports are selected
        if (!$originId || !$destinationId) {
            $shanghai = $ports->firstWhere('name', 'Shanghai') ?? $ports->first();
            $rotterdam = $ports->firstWhere('name', 'Rotterdam') ?? $ports->skip(1)->first();

            $originId = $originId ?? ($shanghai ? $shanghai->id : null);
            $destinationId = $destinationId ?? ($rotterdam ? $rotterdam->id : null);
        }

        $originPort = null;
        $destinationPort = null;
        $estimation = null;
        $errorMsg = null;

        if ($originId && $destinationId) {
            if ($originId == $destinationId) {
                $errorMsg = 'Pelabuhan asal dan pelabuhan tujuan tidak boleh sama. Harap pilih pelabuhan yang berbeda.';
            } else {
                $originPort = Port::with('country')->find($originId);
                $destinationPort = Port::with('country')->find($destinationId);

                if ($originPort && $destinationPort) {
                    $estimation = $this->estimatorService->calculate(
                        $originPort,
                        $destinationPort,
                        $vesselSpeed > 0 ? $vesselSpeed : 18.0,
                        $departureDate
                    );
                } else {
                    $errorMsg = 'Data pelabuhan yang dipilih tidak ditemukan.';
                }
            }
        }

        // Fetch popular preset ports for quick route selection buttons
        $popularPresets = [
            [
                'name' => 'Shanghai ➔ Rotterdam',
                'badge' => 'Asia-Europe Main',
                'origin_id' => $ports->firstWhere('name', 'Shanghai')?->id,
                'dest_id'   => $ports->firstWhere('name', 'Rotterdam')?->id,
            ],
            [
                'name' => 'Tanjung Priok ➔ Singapore',
                'badge' => 'ASEAN Route',
                'origin_id' => $ports->firstWhere('name', 'Tanjung Priok')?->id ?? $ports->firstWhere('name', 'Jakarta')?->id,
                'dest_id'   => $ports->firstWhere('name', 'Singapore')?->id,
            ],
            [
                'name' => 'Los Angeles ➔ Tokyo',
                'badge' => 'Trans-Pacific',
                'origin_id' => $ports->firstWhere('name', 'Los Angeles')?->id,
                'dest_id'   => $ports->firstWhere('name', 'Tokyo')?->id,
            ],
            [
                'name' => 'Ningbo ➔ Hamburg',
                'badge' => 'Eurasia Express',
                'origin_id' => $ports->firstWhere('name', 'Ningbo-Zhoushan')?->id ?? $ports->firstWhere('name', 'Ningbo')?->id,
                'dest_id'   => $ports->firstWhere('name', 'Hamburg')?->id,
            ],
        ];

        return view('shipping.index', compact(
            'ports',
            'originPort',
            'destinationPort',
            'originId',
            'destinationId',
            'vesselSpeed',
            'departureDate',
            'estimation',
            'errorMsg',
            'popularPresets'
        ));
    }
}
