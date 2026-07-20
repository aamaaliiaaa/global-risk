<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use App\Models\NewsCache;
use App\Models\WeatherCache;
use App\Models\CurrencyCache;
use App\Models\CountryIndicator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RiskIntelligenceService
{
    protected $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public static $countryCodes = [
        'indonesia' => 'IDN',
        'singapore' => 'SGP',
        'china' => 'CHN',
        'united states' => 'USA',
        'usa' => 'USA',
        'australia' => 'AUS'
    ];

    /**
     * Fetch weather data with database caching.
     */
    public function getWeather($model)
    {
        $type = get_class($model);
        $id = $model->id;

        // Check cache
        $cache = WeatherCache::where('weatherable_type', $type)
            ->where('weatherable_id', $id)
            ->first();

        if ($cache && $cache->expires_at->isFuture()) {
            return [
                'temperature' => $cache->temperature,
                'wind_speed' => $cache->wind_speed,
                'weather_code' => $cache->weather_code,
                'condition' => $cache->condition,
            ];
        }

        // Fetch from Open-Meteo
        try {
            $lat = $model->latitude;
            $lng = $model->longitude;

            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current' => 'temperature_2m,wind_speed_10m,weather_code'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $temp = $data['current']['temperature_2m'] ?? 25;
                $wind = $data['current']['wind_speed_10m'] ?? 10;
                $code = $data['current']['weather_code'] ?? 0;
                $condition = $this->parseWeatherCode($code);

                WeatherCache::updateOrCreate(
                    [
                        'weatherable_type' => $type,
                        'weatherable_id' => $id
                    ],
                    [
                        'temperature' => $temp,
                        'wind_speed' => $wind,
                        'weather_code' => $code,
                        'condition' => $condition,
                        'expires_at' => Carbon::now()->addHours(6)
                    ]
                );

                return [
                    'temperature' => $temp,
                    'wind_speed' => $wind,
                    'weather_code' => $code,
                    'condition' => $condition
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error fetching weather for {$type} ID {$id}: " . $e->getMessage());
        }

        // Fallback to cache if exists (even if expired)
        if ($cache) {
            return [
                'temperature' => $cache->temperature,
                'wind_speed' => $cache->wind_speed,
                'weather_code' => $cache->weather_code,
                'condition' => $cache->condition,
            ];
        }

        // Hardcoded defaults based on model
        $defaultCond = '☀️ Clear';
        if (str_contains(strtolower($model->weather ?? ''), 'rain')) {
            $defaultCond = '🌧️ Rain';
        } elseif (str_contains(strtolower($model->weather ?? ''), 'cloud')) {
            $defaultCond = '☁️ Cloudy';
        }

        return [
            'temperature' => 24.5,
            'wind_speed' => 12.0,
            'weather_code' => 0,
            'condition' => $defaultCond
        ];
    }

    /**
     * Fetch exchange rates against USD with database caching.
     */
    public function getExchangeRates()
    {
        // Check cache
        $caches = CurrencyCache::all();
        $fresh = $caches->isNotEmpty() && $caches->first()->expires_at->isFuture();

        if ($fresh) {
            return $caches->pluck('rate', 'currency_code')->toArray();
        }

        // Fetch from API
        try {
            $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');
            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];

                foreach ($rates as $code => $rate) {
                    CurrencyCache::updateOrCreate(
                        ['currency_code' => $code],
                        [
                            'rate' => $rate,
                            'expires_at' => Carbon::now()->addHours(12)
                        ]
                    );
                }

                return $rates;
            }
        } catch (\Exception $e) {
            Log::error("Error fetching exchange rates: " . $e->getMessage());
        }

        if ($caches->isNotEmpty()) {
            return $caches->pluck('rate', 'currency_code')->toArray();
        }

        // Static fallback rates if everything fails
        return [
            'USD' => 1.0,
            'IDR' => 16200.0,
            'SGD' => 1.34,
            'CNY' => 7.25,
            'AUD' => 1.50,
            'EUR' => 0.92
        ];
    }

    /**
     * Fetch news for a country with caching and sentiment analysis.
     */
    public function getNews(Country $country)
    {
        // Check cache
        $cachedNews = NewsCache::where('country_id', $country->id)->get();
        $fresh = $cachedNews->isNotEmpty() && $cachedNews->first()->expires_at->isFuture();

        if ($fresh) {
            return $cachedNews->toArray();
        }

        // Fetch from GNews API
        $apiKey = env('GNEWS_API_KEY') ?: '43ad4068a0ffd6d0115e341fe80a8432';
        
        try {
            $response = Http::timeout(5)->get('https://gnews.io/api/v4/search', [
                'q' => $country->name . ' AND (shipping OR logistics OR trade OR economy OR port)',
                'lang' => 'en',
                'max' => 5,
                'apikey' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $articles = $data['articles'] ?? [];

                if (empty($articles)) {
                    // Fallback to simpler query
                    $response = Http::timeout(5)->get('https://gnews.io/api/v4/search', [
                        'q' => $country->name,
                        'lang' => 'en',
                        'max' => 5,
                        'apikey' => $apiKey
                    ]);
                    if ($response->successful()) {
                        $data = $response->json();
                        $articles = $data['articles'] ?? [];
                    }
                }

                if (!empty($articles)) {
                    // Delete old news cache for this country
                    NewsCache::where('country_id', $country->id)->delete();

                    $cachedNews = [];
                    foreach ($articles as $art) {
                        $title = $art['title'] ?? 'No Title';
                        $desc = $art['description'] ?? '';
                        $content = $art['content'] ?? '';
                        $source = $art['source']['name'] ?? 'GNews';
                        $url = $art['url'] ?? '#';
                        $pubDate = isset($art['publishedAt']) ? Carbon::parse($art['publishedAt']) : Carbon::now();

                        // Perform Sentiment Analysis on Title + Description
                        $analysisText = $title . ' ' . $desc;
                        $sentiment = $this->sentimentService->analyze($analysisText);

                        $newRecord = NewsCache::create([
                            'country_id' => $country->id,
                            'title' => substr($title, 0, 255),
                            'source' => $source,
                            'description' => $desc,
                            'content' => $content,
                            'url' => $url,
                            'published_at' => $pubDate,
                            'sentiment' => $sentiment['sentiment'],
                            'positive_count' => $sentiment['positive_count'],
                            'negative_count' => $sentiment['negative_count'],
                            'expires_at' => Carbon::now()->addHours(24)
                        ]);

                        $cachedNews[] = $newRecord->toArray();
                    }

                    return $cachedNews;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error fetching news for country {$country->name}: " . $e->getMessage());
        }

        // Return expired cache if available
        if ($cachedNews->isNotEmpty()) {
            return $cachedNews->toArray();
        }

        // Mock news fallback if API fails and cache is empty
        $mockArticles = [
            [
                'title' => "Supply chain adjustments in {$country->name} show positive progress",
                'description' => "Recent logistics indicators suggest strong domestic growth and improvement in export activities.",
                'source' => "Global Trade News",
                'sentiment' => "Positive"
            ],
            [
                'title' => "Concerns arise over transport congestion and potential delay",
                'description' => "Local industrial reports highlight issues of port congestion and increasing inflation risks.",
                'source' => "Supply Chain Journal",
                'sentiment' => "Negative"
            ],
            [
                'title' => "Market updates and currency stability in {$country->name}",
                'description' => "Central bank maintains stable policies despite minor fluctuations in the global currency rates.",
                'source' => "Financial Times",
                'sentiment' => "Neutral"
            ]
        ];

        NewsCache::where('country_id', $country->id)->delete();
        $cachedNews = [];
        foreach ($mockArticles as $mock) {
            $analysis = $this->sentimentService->analyze($mock['title'] . ' ' . $mock['description']);
            $newRecord = NewsCache::create([
                'country_id' => $country->id,
                'title' => $mock['title'],
                'source' => $mock['source'],
                'description' => $mock['description'],
                'content' => $mock['description'],
                'url' => '#',
                'published_at' => Carbon::now(),
                'sentiment' => $analysis['sentiment'],
                'positive_count' => $analysis['positive_count'],
                'negative_count' => $analysis['negative_count'],
                'expires_at' => Carbon::now()->addHours(2) // Short expire for mock news
            ]);
            $cachedNews[] = $newRecord->toArray();
        }

        return $cachedNews;
    }

    /**
     * Fetch macroeconomic indicator data from World Bank with caching.
     */
    public function getWorldBankData(Country $country)
    {
        $code = self::$countryCodes[strtolower($country->name)] ?? null;
        if (!$code) {
            // Default fallback if country is not Indonesian/etc.
            $code = strtoupper(substr($country->name, 0, 3));
        }

        // Check if we have indicators for the past few years in database
        $indicators = CountryIndicator::where('country_id', $country->id)
            ->orderBy('year', 'asc')
            ->get();

        // If we have indicators in DB and they were updated recently, use them
        if ($indicators->isNotEmpty()) {
            return $indicators->toArray();
        }

        // Fetch from World Bank API
        $indicatorsMap = [
            'gdp' => 'NY.GDP.MKTP.CD',
            'inflation' => 'FP.CPI.TOTL.ZG',
            'population' => 'SP.POP.TOTL',
            'exports' => 'NE.EXP.GNFS.CD',
            'imports' => 'NE.IMP.GNFS.CD',
        ];

        $yearData = [];

        try {
            foreach ($indicatorsMap as $key => $wbCode) {
                $response = Http::timeout(3)->get("http://api.worldbank.org/v2/country/{$code}/indicator/{$wbCode}", [
                    'format' => 'json',
                    'date' => '2019:2024'
                ]);

                if ($response->successful()) {
                    $res = $response->json();
                    if (isset($res[1]) && is_array($res[1])) {
                        foreach ($res[1] as $item) {
                            $year = (int)$item['date'];
                            $val = $item['value'];
                            if ($val !== null) {
                                $yearData[$year][$key] = $val;
                            }
                        }
                    }
                }
            }

            // Save to DB indicators
            if (!empty($yearData)) {
                foreach ($yearData as $year => $metrics) {
                    CountryIndicator::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'year' => $year
                        ],
                        [
                            'gdp' => $metrics['gdp'] ?? null,
                            'inflation' => $metrics['inflation'] ?? null,
                            'population' => $metrics['population'] ?? null,
                            'exports' => $metrics['exports'] ?? null,
                            'imports' => $metrics['imports'] ?? null,
                        ]
                    );
                }

                return CountryIndicator::where('country_id', $country->id)
                    ->orderBy('year', 'asc')
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error("Error fetching World Bank data for country {$country->name}: " . $e->getMessage());
        }

        // Return empty or fallback
        return [];
    }

    /**
     * Map weather codes from Open-Meteo to human-readable strings.
     */
    protected function parseWeatherCode(int $code): string
    {
        return match ($code) {
            0 => '☀️ Clear',
            1, 2, 3 => '🌤️ Partly Cloudy',
            45, 48 => '🌫️ Fog',
            51, 53, 55 => '🌦️ Drizzle',
            56, 57 => '❄️ Freezing Drizzle',
            61, 63, 65 => '🌧️ Rain',
            66, 67 => '❄️ Freezing Rain',
            71, 73, 75 => '❄️ Snow',
            77 => '❄️ Snow Grains',
            80, 81, 82 => '🌧️ Showers',
            85, 86 => '❄️ Snow Showers',
            95, 96, 99 => '⛈️ Thunderstorm',
            default => '☁️ Cloudy',
        };
    }
}
