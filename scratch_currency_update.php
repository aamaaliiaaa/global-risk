<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\CurrencyCache;
use Carbon\Carbon;

$response = Http::timeout(15)->get('https://open.er-api.com/v6/latest/USD');
echo "Status: " . $response->status() . "\n";
if ($response->successful()) {
    $data = $response->json();
    $rates = $data['rates'] ?? [];
    echo "Rates count: " . count($rates) . "\n";
    foreach ($rates as $code => $rate) {
        CurrencyCache::updateOrCreate(
            ['currency_code' => $code],
            [
                'rate' => $rate,
                'expires_at' => Carbon::now()->addHours(12)
            ]
        );
    }
    echo "Saved to cache.\n";
} else {
    echo "Failed.\n";
}
