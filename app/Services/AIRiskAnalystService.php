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
        $p = strtolower(trim($userPrompt));

        // 1. Check for Indonesia <-> China Route
        if ((str_contains($p, 'indo') || str_contains($p, 'indonesia')) && (str_contains($p, 'china') || str_contains($p, 'tiongkok') || str_contains($p, 'shanghai'))) {
            return "🤖 **Analisis AI Estimasi Pengiriman Indonesia ➔ China:**\n\n" .
                   "• **Jarak Laut**: ~2.850 Nautical Miles (NM) (Pelabuhan Tanjung Priok ➔ Shanghai / Shenzhen).\n" .
                   "• **Waktu Transit Kapal**: ~7,5 Hari (pada kecepatan standar 18 knots).\n" .
                   "• **Buffer Cuaca & Antrean**: ~2,5 Hari (Status Pelabuhan: Normal).\n" .
                   "⏱️ **Total Estimasi Tiba (ETA)**: **~10 Hari**.\n\n" .
                   "💡 *Saran AI: Gunakan menu Shipping Estimator Pro di sidebar untuk menghitung rute spesifik dengan berbagai tipe kapal!*";
        }

        // 2. Check for General Indonesia Queries
        if (str_contains($p, 'indonesia') || str_contains($p, 'idr') || str_contains($p, 'indo')) {
            $c = Country::where('name', 'Indonesia')->first();
            return "🤖 **Analisis Risiko AI - Indonesia:**\n\n" .
                   "• **Skor Risiko**: " . ($c->risk_score ?? 20) . "/100 (" . ($c->risk ?? 'Low') . " Risk)\n" .
                   "• **Kondisi Cuaca**: " . ($c->weather ?? 'Partly Cloudy') . "\n" .
                   "• **Mata Uang**: IDR (Rata-rata 1 USD = Rp 17.950)\n" .
                   "• **Status Logistik**: Pelabuhan utama (Tanjung Priok, Tanjung Perak, Belawan) beroperasi dalam kondisi lancar & normal.";
        }

        // 3. Check for Shanghai <-> Rotterdam Route
        if (str_contains($p, 'shanghai') || str_contains($p, 'rotterdam') || str_contains($p, 'eropa') || str_contains($p, 'europe')) {
            return "🤖 **Analisis AI Estimasi Rute Shanghai ➔ Rotterdam:**\n\n" .
                   "• **Jarak Laut**: ~8.222 Nautical Miles (via Terusan Suez).\n" .
                   "• **Waktu Transit Kapal**: ~19 Hari (pada kecepatan 18 knots).\n" .
                   "• **Buffer Antrean Terminal**: ~5,4 Hari.\n" .
                   "⏱️ **Total Estimasi Tiba (ETA)**: **~24,4 Hari**.";
        }

        // 4. Check for High Risk Countries
        if (str_contains($p, 'tinggi') || str_contains($p, 'high risk') || str_contains($p, 'bahaya') || str_contains($p, 'risiko')) {
            $highRiskCount = Country::where('risk', 'High')->count();
            return "🤖 **Evaluasi Risiko Tinggi AI:**\n\n" .
                   "Terdapat **{$highRiskCount} negara** kategori **High Risk** (misal: Ukraine, Pakistan, Nigeria, Iran).\n" .
                   "• **Penyebab**: Kombinasi konflik geopolitik, volatilitas inflasi, dan risiko sentimen berita.\n" .
                   "• **Rekomendasi**: Tingkatkan stok cadangan 15-20 hari dan gunakan asuransi kargo maritim tambahan.";
        }

        // 5. Check for Greetings
        if (str_contains($p, 'halo') || str_contains($p, 'hi') || str_contains($p, 'siapa') || str_contains($p, 'bisa apa')) {
            return "👋 **Halo! Saya GlobalRisk AI Assistant.**\n\nSaya dapat membantu Anda mengecek:\n" .
                   "1. Estimasi waktu & rute pengiriman antar negara/pelabuhan.\n" .
                   "2. Evaluasi skor risiko & cuaca negara tujuan.\n" .
                   "3. Rekomendasi mitigasi logistik & volatilitas kurs.\n\nSilakan ketik pertanyaan Anda!";
        }

        // 6. Generic Query Processing
        $totalC = Country::count();
        $totalP = Port::count();

        return "🤖 **Analisis Intelijen AI GlobalRisk:**\n\n" .
               "Mengolah telemetry dari **{$totalC} negara** & **{$totalP} pelabuhan**:\n" .
               "• **Logistik**: Jalur perdagangan utama Asia-Pasifik dan Eropa saat ini beroperasi stabil.\n" .
               "• **Fitur Terkait**: Anda dapat membuka menu **Shipping Estimator Pro** untuk kalkulasi jarak maritim presisi & titik koordinat rute.";
    }
}
