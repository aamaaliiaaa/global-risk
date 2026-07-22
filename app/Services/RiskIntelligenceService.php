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
        'united states of america' => 'USA',
        'usa' => 'USA',
        'australia' => 'AUS',
        'japan' => 'JPN',
        'germany' => 'DEU',
        'united kingdom' => 'GBR',
        'uk' => 'GBR',
        'malaysia' => 'MYS',
        'india' => 'IND',
        'france' => 'FRA',
        'brazil' => 'BRA',
        'vietnam' => 'VNM',
        'thailand' => 'THA',
        'philippines' => 'PHL',
        'south korea' => 'KOR',
        'canada' => 'CAN',
        'russia' => 'RUS',
        'netherlands' => 'NLD',
        'saudi arabia' => 'SAU',
        'south africa' => 'ZAF',
        'switzerland' => 'CHE',
        'turkey' => 'TUR',
        'türkiye' => 'TUR',
        'united arab emirates' => 'ARE',
        'uae' => 'ARE',
        'argentina' => 'ARG',
        'egypt' => 'EGY',
        'italy' => 'ITA',
        'spain' => 'ESP',
        'mexico' => 'MEX',
    ];

    /**
     * Fetch weather data with database caching.
     */
    /**
     * Unified WMO Weather Code Parser
     */
    public function parseWmoCode(int $code): array
    {
        return match (true) {
            $code === 0 => [
                'condition' => 'Clear / Sunny',
                'icon' => '☀️',
                'risk' => 'Low Risk',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle'
            ],
            in_array($code, [1, 2, 3]) => [
                'condition' => 'Partly Cloudy',
                'icon' => '🌤️',
                'risk' => 'Low Risk',
                'badge_class' => 'bg-info-subtle text-info border border-info-subtle'
            ],
            in_array($code, [45, 48]) => [
                'condition' => 'Fog / Haze',
                'icon' => '🌫️',
                'risk' => 'Moderate Risk',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle'
            ],
            in_array($code, [51, 53, 55, 56, 57]) => [
                'condition' => 'Drizzle',
                'icon' => '🌦️',
                'risk' => 'Moderate Risk',
                'badge_class' => 'bg-warning-subtle text-warning border border-warning-subtle'
            ],
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82]) => [
                'condition' => 'Rain / Showers',
                'icon' => '🌧️',
                'risk' => 'Moderate Risk',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle'
            ],
            in_array($code, [71, 73, 75, 77, 85, 86]) => [
                'condition' => 'Snow',
                'icon' => '❄️',
                'risk' => 'High Risk',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle'
            ],
            in_array($code, [95, 96, 99]) => [
                'condition' => 'Thunderstorm',
                'icon' => '⛈️',
                'risk' => 'Severe Risk',
                'badge_class' => 'bg-danger text-white'
            ],
            default => [
                'condition' => 'Cloudy',
                'icon' => '☁️',
                'risk' => 'Low Risk',
                'badge_class' => 'bg-secondary-subtle text-secondary'
            ]
        };
    }

    /**
     * Fetch weather data for a single model (Country or Port) with DB caching.
     */
    public function getWeather($model)
    {
        $type = get_class($model);
        $id = $model->id;

        $cache = WeatherCache::where('weatherable_type', $type)
            ->where('weatherable_id', $id)
            ->first();

        if ($cache && $cache->expires_at && $cache->expires_at->isFuture()) {
            $wmoInfo = $this->parseWmoCode($cache->weather_code);
            return [
                'temperature' => $cache->temperature,
                'wind_speed' => $cache->wind_speed,
                'weather_code' => $cache->weather_code,
                'humidity' => 60,
                'condition' => $wmoInfo['condition'],
                'icon' => $wmoInfo['icon'],
                'risk' => $wmoInfo['risk'],
                'badge_class' => $wmoInfo['badge_class']
            ];
        }

        try {
            $lat = $model->latitude;
            $lng = $model->longitude;

            $response = Http::timeout(5)->get('http://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => 'true'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cw = $data['current_weather'] ?? [];
                $temp = $cw['temperature'] ?? 25;
                $wind = $cw['windspeed'] ?? 10;
                $code = $cw['weathercode'] ?? 0;

                $wmoInfo = $this->parseWmoCode($code);

                WeatherCache::updateOrCreate(
                    [
                        'weatherable_type' => $type,
                        'weatherable_id' => $id
                    ],
                    [
                        'temperature' => $temp,
                        'wind_speed' => $wind,
                        'weather_code' => $code,
                        'condition' => $wmoInfo['condition'],
                        'expires_at' => Carbon::now()->addMinutes(15)
                    ]
                );

                return [
                    'temperature' => $temp,
                    'wind_speed' => $wind,
                    'weather_code' => $code,
                    'humidity' => 60,
                    'condition' => $wmoInfo['condition'],
                    'icon' => $wmoInfo['icon'],
                    'risk' => $wmoInfo['risk'],
                    'badge_class' => $wmoInfo['badge_class']
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error fetching weather for {$type} ID {$id}: " . $e->getMessage());
        }

        if ($cache) {
            $wmoInfo = $this->parseWmoCode($cache->weather_code);
            return [
                'temperature' => $cache->temperature,
                'wind_speed' => $cache->wind_speed,
                'weather_code' => $cache->weather_code,
                'humidity' => 60,
                'condition' => $wmoInfo['condition'],
                'icon' => $wmoInfo['icon'],
                'risk' => $wmoInfo['risk'],
                'badge_class' => $wmoInfo['badge_class']
            ];
        }

        $wmoInfo = $this->parseWmoCode(0);
        return [
            'temperature' => 24.5,
            'wind_speed' => 12.0,
            'weather_code' => 0,
            'humidity' => 60,
            'condition' => $wmoInfo['condition'],
            'icon' => $wmoInfo['icon'],
            'risk' => $wmoInfo['risk'],
            'badge_class' => $wmoInfo['badge_class']
        ];
    }

    /**
     * Bulk fetch weather for all countries, updating WeatherCache table in MySQL.
     */
    public function getBulkWeatherData($allCountries, $forceRefresh = false)
    {
        $cacheKey = 'bulk_weather_map_data_v5';
        if ($forceRefresh) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($allCountries) {
            $result = [];
            $chunks = $allCountries->chunk(50);

            foreach ($chunks as $chunk) {
                $lats = $chunk->pluck('latitude')->implode(',');
                $lngs = $chunk->pluck('longitude')->implode(',');
                $url = "http://api.open-meteo.com/v1/forecast?latitude={$lats}&longitude={$lngs}&current_weather=true";

                try {
                    $response = Http::timeout(6)->get($url);
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['current_weather'])) {
                            $data = [$data];
                        }

                        if (is_array($data)) {
                            foreach ($data as $index => $res) {
                                if (isset($res['current_weather'])) {
                                    $cw = $res['current_weather'];
                                    $countryItem = $chunk->values()->get($index);

                                    if ($countryItem) {
                                        $temp = $cw['temperature'] ?? 24.5;
                                        $wind = $cw['windspeed'] ?? 10.0;
                                        $code = $cw['weathercode'] ?? 0;
                                        $wmoInfo = $this->parseWmoCode($code);

                                        // Store in DB WeatherCache so country detail uses exact same data
                                        WeatherCache::updateOrCreate(
                                            [
                                                'weatherable_type' => get_class($countryItem),
                                                'weatherable_id' => $countryItem->id
                                            ],
                                            [
                                                'temperature' => $temp,
                                                'wind_speed' => $wind,
                                                'weather_code' => $code,
                                                'condition' => $wmoInfo['condition'],
                                                'expires_at' => Carbon::now()->addMinutes(15)
                                            ]
                                        );

                                        $result[$countryItem->id] = [
                                            'temp' => $temp,
                                            'wind' => $wind,
                                            'humidity' => 60,
                                            'code' => $code,
                                            'condition' => $wmoInfo['condition'],
                                            'icon' => $wmoInfo['icon'],
                                            'risk' => $wmoInfo['risk'],
                                            'badge_class' => $wmoInfo['badge_class']
                                        ];
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Bulk weather fetch error: " . $e->getMessage());
                }
            }

            return $result;
        });
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
                            'expires_at' => Carbon::now()->addMinutes(10)
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
     * Fetch real-time live news for a country using Google News RSS and GNews fallback.
     */
    public function getNews(Country $country, bool $forceRefresh = false)
    {
        // Check cache
        $cachedNews = NewsCache::where('country_id', $country->id)->get();
        $fresh = !$forceRefresh && $cachedNews->isNotEmpty() && $cachedNews->first()->expires_at->isFuture();

        if ($fresh) {
            return $cachedNews->toArray();
        }

        // 1. Primary Engine: Google News RSS (Real-time, authentic publisher links, 100% free)
        try {
            $query = urlencode('"' . $country->name . '" AND (shipping OR logistics OR trade OR economy OR port OR business)');
            $rssUrl = "https://news.google.com/rss/search?q={$query}&hl=en-US&gl=US&ceid=US:en";

            $response = Http::timeout(6)->get($rssUrl);
            if ($response->successful()) {
                $xml = @simplexml_load_string($response->body());
                if ($xml && isset($xml->channel->item) && count($xml->channel->item) > 0) {
                    NewsCache::where('country_id', $country->id)->delete();
                    $cachedNews = [];
                    $count = 0;

                    foreach ($xml->channel->item as $item) {
                        if ($count >= 6) break;

                        $rawTitle = (string)$item->title;
                        $link = (string)$item->link;
                        $pubDateStr = (string)$item->pubDate;
                        $pubDate = $pubDateStr ? Carbon::parse($pubDateStr) : Carbon::now();

                        $source = 'Google News';
                        if (isset($item->source) && (string)$item->source !== '') {
                            $source = (string)$item->source;
                        } elseif (str_contains($rawTitle, ' - ')) {
                            $parts = explode(' - ', $rawTitle);
                            $source = array_pop($parts);
                            $rawTitle = implode(' - ', $parts);
                        }

                        $description = strip_tags((string)$item->description);
                        $description = \Illuminate\Support\Str::limit($description, 250);

                        $analysisText = $rawTitle . ' ' . $description;
                        $sentiment = $this->sentimentService->analyze($analysisText);

                        $newRecord = NewsCache::create([
                            'country_id' => $country->id,
                            'title' => \Illuminate\Support\Str::limit($rawTitle, 250),
                            'source' => $source,
                            'description' => $description ?: $rawTitle,
                            'content' => $description ?: $rawTitle,
                            'url' => $link,
                            'published_at' => $pubDate,
                            'sentiment' => $sentiment['sentiment'],
                            'positive_count' => $sentiment['positive_count'],
                            'negative_count' => $sentiment['negative_count'],
                            'expires_at' => Carbon::now()->addHours(3)
                        ]);

                        $cachedNews[] = $newRecord->toArray();
                        $count++;
                    }

                    if (!empty($cachedNews)) {
                        return $cachedNews;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error fetching Google News RSS for country {$country->name}: " . $e->getMessage());
        }

        // 2. Secondary Engine: GNews API
        $apiKey = env('GNEWS_API_KEY') ?: '43ad4068a0ffd6d0115e341fe80a8432';
        try {
            $response = Http::timeout(5)->get('https://gnews.io/api/v4/search', [
                'q' => $country->name . ' AND (shipping OR logistics OR trade OR economy)',
                'lang' => 'en',
                'max' => 5,
                'apikey' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $articles = $data['articles'] ?? [];

                if (!empty($articles)) {
                    NewsCache::where('country_id', $country->id)->delete();
                    $cachedNews = [];
                    foreach ($articles as $art) {
                        $title = $art['title'] ?? 'No Title';
                        $desc = $art['description'] ?? '';
                        $source = $art['source']['name'] ?? 'GNews';
                        $url = $art['url'] ?? '#';
                        $pubDate = isset($art['publishedAt']) ? Carbon::parse($art['publishedAt']) : Carbon::now();

                        $analysis = $this->sentimentService->analyze($title . ' ' . $desc);

                        $newRecord = NewsCache::create([
                            'country_id' => $country->id,
                            'title' => substr($title, 0, 255),
                            'source' => $source,
                            'description' => $desc,
                            'content' => $desc,
                            'url' => $url,
                            'published_at' => $pubDate,
                            'sentiment' => $analysis['sentiment'],
                            'positive_count' => $analysis['positive_count'],
                            'negative_count' => $analysis['negative_count'],
                            'expires_at' => Carbon::now()->addHours(6)
                        ]);

                        $cachedNews[] = $newRecord->toArray();
                    }

                    return $cachedNews;
                }
            }
        } catch (\Exception $e) {
            Log::error("Error fetching GNews API for country {$country->name}: " . $e->getMessage());
        }

        // Return cached news if exists
        if ($cachedNews->isNotEmpty()) {
            return $cachedNews->toArray();
        }

        return [];
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

        // Check if we already have genuine World Bank data (>100M pop for large countries or realistic GDP)
        $indicators = CountryIndicator::where('country_id', $country->id)
            ->orderBy('year', 'asc')
            ->get();

        // If indicators exist and have real World Bank scale (> 100M pop for Indonesia, etc.), use them
        $firstInd = $indicators->first();
        $isMock = $firstInd && ($country->name === 'Indonesia' && ($firstInd->population ?? 0) < 100000000);

        if ($indicators->isNotEmpty() && !$isMock) {
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
