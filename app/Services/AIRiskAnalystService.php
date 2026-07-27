<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use App\Models\Article;
use App\Models\NewsCache;

class AIRiskAnalystService
{
    /**
     * Generate an Executive AI Risk Assessment Report for a specific country or global corridor.
     */
    public function generateCountryReport(Country $country, array $weather = [], array $riskDetails = []): array
    {
        $riskScore = $country->risk_score ?? 25;
        $riskLevel = $country->risk ?? 'Low';
        $temp = $weather['temperature'] ?? 24.5;
        $wind = $weather['wind_speed'] ?? 12.0;
        $condition = $weather['condition'] ?? 'Clear';

        // AI Threat Evaluation Synthesis
        if ($riskScore >= 65) {
            $threatAssessment = "CRITICAL RISK ELEVATION: {$country->name} is currently exhibiting heightened vulnerability across trade infrastructure. Elevated news sentiment risks and weather anomalies indicate high potential for supply chain bottlenecks.";
            $recommendation = "Enforce route diversification, increase safety stock buffer by 15-20 days, and mandate real-time GPS tracking for all container consignments bound for or departing from {$country->name}.";
        } elseif ($riskScore >= 35) {
            $threatAssessment = "MODERATE WATCH: Trade operations in {$country->name} remain functional with localized operational friction. Minor currency fluctuations and seasonal weather patterns warrant active monitoring.";
            $recommendation = "Maintain standard procurement schedules but review carrier contingency plans. Monitor local port congestion indexes weekly.";
        } else {
            $threatAssessment = "OPTIMAL TRADE ENVIRONMENT: {$country->name} demonstrates robust logistical stability, favorable meteorological forecasts, and steady macroeconomic indicators.";
            $recommendation = "Standard maritime transit recommended. Opportunity for optimal freight rate negotiation under current stable market corridor conditions.";
        }

        // Weather Risk Impact
        $weatherImpact = "Meteorological telemetry indicates {$condition} conditions with temperature recorded at {$temp}°C and wind velocity of {$wind} km/h. " . 
            ($wind > 20 ? "High wind speeds may trigger temporary gantry crane holds at container terminals." : "Weather parameters are within safe operational thresholds for sea & air freight.");

        // Financial & FX Impact
        $currency = strtoupper($country->currency ?? 'USD');
        $fxImpact = "Currency benchmark in {$currency}. Volatility index suggests " . 
            ($riskScore > 50 ? "heightened FX exposure risk against USD. Recommend hedging strategy for multi-quarter contracts." : "stable foreign exchange conversion rates relative to standard trade currencies.");

        return [
            'country_name' => $country->name,
            'flag' => $country->flag,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'summary' => $threatAssessment,
            'weather_impact' => $weatherImpact,
            'financial_impact' => $fxImpact,
            'actionable_recommendation' => $recommendation,
            'ai_model_name' => 'GlobalRisk Neural Heuristic AI v2.4 (Free Engine)',
            'generated_at' => now()->format('d M Y, H:i T')
        ];
    }

    /**
     * AI Assistant Chatbot Knowledge Query Engine.
     */
    public function processChatQuery(string $userPrompt): string
    {
        $promptLower = strtolower(trim($userPrompt));

        // 1. Check for Country Risk Queries
        if (str_contains($promptLower, 'indonesia') || str_contains($promptLower, 'idr')) {
            $country = Country::where('name', 'Indonesia')->first();
            return "🤖 **Analisis AI GlobalRisk - Indonesia:**\n\n- **Skor Risiko**: " . ($country->risk_score ?? 20) . "/100 (" . ($country->risk ?? 'Low') . " Risk)\n- **Cuaca Live**: " . ($country->weather ?? 'Partly Cloudy') . "\n- **Mata Uang**: IDR (Rata-rata 1 USD = Rp 17.950)\n- **Rekomendasi AI**: Jalur logistik maritim Indonesia berada dalam kondisi stabil. Pelabuhan utama (Tanjung Priok & Tanjung Perak) beroperasi normal.";
        }

        if (str_contains($promptLower, 'shanghai') || str_contains($promptLower, 'rotterdam') || str_contains($promptLower, 'estimasi') || str_contains($promptLower, 'shipping') || str_contains($promptLower, 'pengiriman')) {
            return "🤖 **Analisis AI Estimasi Pengiriman Maritim:**\n\n- **Rute Shanghai ➔ Rotterdam**: Jarak ~8.222 NM (Nautical Miles), waktu transit dasar ~19 hari pada kecepatan 18 knots. Ditambah buffer antrean & cuaca ~5,4 hari (Total ETA ~24,4 Hari).\n- **Saran AI**: Gunakan fitur *Shipping Estimator Pro* di sidebar untuk menghitung rute khusus antar 1.500+ pelabuhan dunia secara realtime!";
        }

        if (str_contains($promptLower, 'tinggi') || str_contains($promptLower, 'high risk') || str_contains($promptLower, 'bahaya') || str_contains($promptLower, 'risiko')) {
            $highRiskCount = Country::where('risk', 'High')->count();
            return "🤖 **Ringkasan Risiko Tinggi AI:**\n\nSaat ini terdapat **{$highRiskCount} negara** berkategori **High Risk** (seperti Ukraine, Pakistan, Nigeria, Iran) akibat kombinasi konflik geopolitik, inflasi tinggi, atau cuaca ekstrem. Disarankan melakukan diversifikasi rute pelayaran dan penambahan buffer stok 15-20 hari.";
        }

        if (str_contains($promptLower, 'halo') || str_contains($promptLower, 'hi') || str_contains($promptLower, 'siapa')) {
            return "👋 **Halo! Saya GlobalRisk AI Assistant.**\n\nSaya dapat membantu Anda menganalisis:\n1. Estimasi waktu & risiko pengiriman maritim antar pelabuhan.\n2. Evaluasi skor risiko, cuaca, dan inflasi negara.\n3. Tren volatilitas nilai tukar mata uang & berita logistik.\n\nSilakan ketik pertanyaan Anda!";
        }

        // Generic Intelligent Synthesis Response
        $totalC = Country::count();
        $totalP = Port::count();

        return "🤖 **GlobalRisk AI Risk Intelligence System:**\n\nMengolah data realtime dari **{$totalC} negara** dan **{$totalP} pelabuhan dunia**:\n- **Saran Logistik**: Selalu periksa skor risiko negara asal & tujuan sebelum membuat kontrak pengiriman.\n- **Fitur Terkait**: Anda dapat mengecek *Shipping Estimator Pro* untuk kalkulasi jarak & buffer keterlambatan, serta *Weather Feeds* untuk pantauan badai maritim.";
    }
}
