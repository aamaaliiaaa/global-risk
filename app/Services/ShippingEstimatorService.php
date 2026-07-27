<?php

namespace App\Services;

use App\Models\Port;
use Carbon\Carbon;

class ShippingEstimatorService
{
    protected $intelligenceService;

    public function __construct(RiskIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    /**
     * Calculate shipping estimation between two ports with detailed factor breakdown.
     *
     * @param Port $originPort
     * @param Port $destinationPort
     * @param float $vesselSpeedKnots
     * @param string|null $departureDateStr
     * @return array
     */
    public function calculate(Port $originPort, Port $destinationPort, float $vesselSpeedKnots = 18.0, ?string $departureDateStr = null): array
    {
        $departureDate = $departureDateStr ? Carbon::parse($departureDateStr) : Carbon::now();

        // 1. Calculate Great-Circle Distance in Nautical Miles (NM)
        $distanceNm = $this->calculateHaversineDistance(
            $originPort->latitude,
            $originPort->longitude,
            $destinationPort->latitude,
            $destinationPort->longitude
        );

        // Convert distance to Kilometers (1 NM = 1.852 km)
        $distanceKm = $distanceNm * 1.852;

        // 2. Base Voyage Time (pure sailing time without delays)
        // 1 knot = 1 nautical mile per hour
        $baseHours = $vesselSpeedKnots > 0 ? ($distanceNm / $vesselSpeedKnots) : 0;
        $baseDays = $baseHours / 24.0;

        // 3. Evaluate Risk Factors & Delays
        $delayFactors = [];
        $totalDelayDays = 0.0;

        // A. Origin Port Status Delay
        $originPortDelay = match ($originPort->status) {
            'Congested' => 2.5,
            'Delay'     => 1.5,
            'Busy'      => 0.8,
            default     => 0.0,
        };
        if ($originPortDelay > 0) {
            $totalDelayDays += $originPortDelay;
            $delayFactors[] = [
                'category' => 'Pelabuhan Asal',
                'title' => 'Kemacetan Operasional Pelabuhan Asal',
                'impact_days' => $originPortDelay,
                'type' => 'port_origin',
                'severity' => $originPort->status === 'Congested' ? 'danger' : 'warning',
                'explanation' => "Pelabuhan asal ({$originPort->name}) dalam kondisi '{$originPort->status}'. Memerlukan waktu ekstra untuk antrean pelepasan dokumen dan pemuatan kontainer (tambah +" . number_format($originPortDelay, 1) . " hari)."
            ];
        } else {
            $delayFactors[] = [
                'category' => 'Pelabuhan Asal',
                'title' => 'Operasional Pelabuhan Asal Normal',
                'impact_days' => 0.0,
                'type' => 'port_origin',
                'severity' => 'success',
                'explanation' => "Pelabuhan asal ({$originPort->name}) beroperasi normal. Kapal dapat berangkat sesuai jadwal tanpa antrean signifikan."
            ];
        }

        // B. Destination Port Status Delay
        $destPortDelay = match ($destinationPort->status) {
            'Congested' => 3.0,
            'Delay'     => 2.0,
            'Busy'      => 1.0,
            default     => 0.0,
        };
        if ($destPortDelay > 0) {
            $totalDelayDays += $destPortDelay;
            $delayFactors[] = [
                'category' => 'Pelabuhan Tujuan',
                'title' => 'Kepadatan Antrean Pelabuhan Tujuan',
                'impact_days' => $destPortDelay,
                'type' => 'port_dest',
                'severity' => $destinationPort->status === 'Congested' ? 'danger' : 'warning',
                'explanation' => "Pelabuhan tujuan ({$destinationPort->name}) mengalami kondisi '{$destinationPort->status}'. Kapal akan mengalami penundaan bersandar (berthing delay) dan antrean pembongkaran kargo (tambah +" . number_format($destPortDelay, 1) . " hari)."
            ];
        } else {
            $delayFactors[] = [
                'category' => 'Pelabuhan Tujuan',
                'title' => 'Ketersediaan Dermaga Pelabuhan Tujuan Baik',
                'impact_days' => 0.0,
                'type' => 'port_dest',
                'severity' => 'success',
                'explanation' => "Pelabuhan tujuan ({$destinationPort->name}) beroperasi lancar. Dermaga siap menerima kapal untuk proses pembongkaran tepat waktu."
            ];
        }

        // C. Weather Conditions at Origin & Destination
        $originWeather = $this->intelligenceService->getWeather($originPort);
        $destWeather   = $this->intelligenceService->getWeather($destinationPort);

        $originWeatherCode = $originWeather['weather_code'] ?? 0;
        $destWeatherCode   = $destWeather['weather_code'] ?? 0;

        $weatherDelay = 0.0;
        $weatherDetails = [];

        // Severe weather codes: 61, 63, 65 (Rain), 80, 81, 82 (Showers), 71-75 (Snow), 95, 96, 99 (Thunderstorm)
        if (in_array($originWeatherCode, [95, 96, 99, 71, 73, 75, 65, 82])) {
            $weatherDelay += 1.5;
            $weatherDetails[] = "Badai/cuaca ekstrem di kawasan asal ({$originWeather['condition']})";
        } elseif (in_array($originWeatherCode, [61, 63, 80, 81, 51, 53, 55, 45, 48])) {
            $weatherDelay += 0.5;
            $weatherDetails[] = "Hujan/kabut di pelabuhan asal ({$originWeather['condition']})";
        }

        if (in_array($destWeatherCode, [95, 96, 99, 71, 73, 75, 65, 82])) {
            $weatherDelay += 1.5;
            $weatherDetails[] = "Kondisi laut buruk di pelabuhan tujuan ({$destWeather['condition']})";
        } elseif (in_array($destWeatherCode, [61, 63, 80, 81, 51, 53, 55, 45, 48])) {
            $weatherDelay += 0.5;
            $weatherDetails[] = "Cuaca kurang bersahabat di pelabuhan tujuan ({$destWeather['condition']})";
        }

        if ($weatherDelay > 0) {
            $totalDelayDays += $weatherDelay;
            $delayFactors[] = [
                'category' => 'Cuaca & Navigasi',
                'title' => 'Faktor Pengaruh Cuaca Maritim',
                'impact_days' => $weatherDelay,
                'type' => 'weather',
                'severity' => $weatherDelay >= 1.5 ? 'danger' : 'warning',
                'explanation' => "Ditemukan kendala cuaca: " . implode(', ', $weatherDetails) . ". Kecepatan pelayaran akan disesuaikan untuk menjaga keselamatan kargo (tambah +" . number_format($weatherDelay, 1) . " hari)."
            ];
        } else {
            $delayFactors[] = [
                'category' => 'Cuaca & Navigasi',
                'title' => 'Kondisi Cuaca Maritim Cerah',
                'impact_days' => 0.0,
                'type' => 'weather',
                'severity' => 'success',
                'explanation' => "Cuaca di pelabuhan asal ({$originWeather['condition']}) dan tujuan ({$destWeather['condition']}) dalam batas aman untuk pelayaran."
            ];
        }

        // D. Country Risk & Maritime Clearance
        $originCountryRisk = $originPort->country->risk_score ?? 30;
        $destCountryRisk   = $destinationPort->country->risk_score ?? 30;

        $maxRisk = max($originCountryRisk, $destCountryRisk);
        if ($maxRisk >= 65) {
            $riskDelay = 2.0;
            $totalDelayDays += $riskDelay;
            $delayFactors[] = [
                'category' => 'Risiko & Inspeksi',
                'title' => 'Pemeriksaan Keamanan & Risiko Negara Tinggi',
                'impact_days' => $riskDelay,
                'type' => 'risk',
                'severity' => 'danger',
                'explanation' => "Tingkat risiko geopolitik/keamanan tinggi pada kawasan transit (Skor Risiko: {$maxRisk}/100). Diperlukan pemeriksaan manifes ketat dan sertifikasi clearance khusus (tambah +" . number_format($riskDelay, 1) . " hari)."
            ];
        } elseif ($maxRisk >= 45) {
            $riskDelay = 0.8;
            $totalDelayDays += $riskDelay;
            $delayFactors[] = [
                'category' => 'Risiko & Inspeksi',
                'title' => 'Pemeriksaan Kepabeanan Moderat',
                'impact_days' => $riskDelay,
                'type' => 'risk',
                'severity' => 'warning',
                'explanation' => "Skor risiko kawasan moderat ({$maxRisk}/100). Waktu inspeksi dokumen maritim standar diberlakukan (tambah +" . number_format($riskDelay, 1) . " hari)."
            ];
        } else {
            $delayFactors[] = [
                'category' => 'Risiko & Inspeksi',
                'title' => 'Jalur Reguler & Inspeksi Lancar',
                'impact_days' => 0.0,
                'type' => 'risk',
                'severity' => 'success',
                'explanation' => "Tingkat risiko wilayah sangat rendah ({$maxRisk}/100). Proses kepabeanan dan clearance berjalan cepat tanpa kendala."
            ];
        }

        // E. Long Route / Navigation Lane Buffer (> 2,500 NM)
        if ($distanceNm > 2500) {
            $routeBufferDays = round(($distanceNm / 2500) * 0.5, 1);
            $totalDelayDays += $routeBufferDays;
            $delayFactors[] = [
                'category' => 'Rute Jarak Jauh',
                'title' => 'Penyesuaian Jalur Maritim Internasional',
                'impact_days' => $routeBufferDays,
                'type' => 'route',
                'severity' => 'info',
                'explanation' => "Pelayaran rute jarak jauh (" . number_format($distanceNm, 0) . " NM) melewati selat/jalur pelayaran internasional yang memerlukan penyesuaian kecepatan navigasi (tambah +" . number_format($routeBufferDays, 1) . " hari)."
            ];
        }

        // 4. Calculate Final Total Days & ETA Date
        $totalDays = $baseDays + $totalDelayDays;
        
        $totalHoursRounded = (int) round($totalDays * 24);
        $daysPart = floor($totalHoursRounded / 24);
        $hoursPart = $totalHoursRounded % 24;

        $etaDate = (clone $departureDate)->addHours($totalHoursRounded);

        // Classify overall delay risk level
        $overallDelayRisk = 'Low';
        if ($totalDelayDays >= 4.0) {
            $overallDelayRisk = 'High';
        } elseif ($totalDelayDays >= 1.5) {
            $overallDelayRisk = 'Medium';
        }

        return [
            'origin_port'        => $originPort,
            'destination_port'   => $destinationPort,
            'departure_date'     => $departureDate,
            'eta_date'           => $etaDate,
            'vessel_speed_knots' => $vesselSpeedKnots,
            'distance_nm'        => round($distanceNm, 1),
            'distance_km'        => round($distanceKm, 1),
            'base_days'          => round($baseDays, 1),
            'base_hours'         => round($baseHours, 1),
            'total_delay_days'   => round($totalDelayDays, 1),
            'total_days'         => round($totalDays, 1),
            'formatted_duration'=> "{$daysPart} Hari {$hoursPart} Jam",
            'delay_factors'      => $delayFactors,
            'overall_delay_risk' => $overallDelayRisk,
            'origin_weather'     => $originWeather,
            'dest_weather'       => $destWeather,
        ];
    }

    /**
     * Calculate Great Circle distance between two points in Nautical Miles (Haversine formula).
     */
    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusNm = 3440.065; // Earth radius in Nautical Miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusNm * $c;
    }
}
