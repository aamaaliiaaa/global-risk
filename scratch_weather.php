<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Country;
use Illuminate\Support\Facades\Http;

$countries = Country::whereNotNull('latitude')->whereNotNull('longitude')->take(100)->get();
$lats = $countries->pluck('latitude')->implode(',');
$lngs = $countries->pluck('longitude')->implode(',');

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lats}&longitude={$lngs}&current_weather=true";
echo "URL length: " . strlen($url) . "\n";
$response = Http::get($url);
echo "Status: " . $response->status() . "\n";
if ($response->successful()) {
    $data = $response->json();
    echo "Is array? " . (is_array($data) ? 'Yes' : 'No') . "\n";
    if (is_array($data)) {
        echo "Count: " . count($data) . "\n";
    }
}
