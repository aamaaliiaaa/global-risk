<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$caches = \App\Models\CurrencyCache::all();
if ($caches->isEmpty()) {
    echo "No currency cache found.\n";
} else {
    echo "Cache Count: " . $caches->count() . "\n";
    echo "Cache sample IDR: " . $caches->where('currency_code', 'IDR')->first()?->rate . "\n";
    echo "Cache sample MYR: " . $caches->where('currency_code', 'MYR')->first()?->rate . "\n";
    
    $first = $caches->first();
    echo "Expires at: " . $first->expires_at . " (Is future: " . ($first->expires_at->isFuture() ? 'Yes' : 'No') . ")\n";
}
