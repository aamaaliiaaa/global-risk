<?php
// Inspect marchah sea-ports structure
$json = file_get_contents('https://raw.githubusercontent.com/marchah/sea-ports/master/lib/ports.json');
$data = json_decode($json, true);
echo "Total ports: " . count($data) . "\n\n";

// Show 3 sample entries
$i = 0;
foreach ($data as $code => $port) {
    echo "Code: $code\n";
    echo json_encode($port, JSON_PRETTY_PRINT) . "\n\n";
    if (++$i >= 3) break;
}

// Count by country
$countries = [];
foreach ($data as $port) {
    $c = $port['country'] ?? 'Unknown';
    $countries[$c] = ($countries[$c] ?? 0) + 1;
}
arsort($countries);
echo "Top 10 countries by port count:\n";
foreach (array_slice($countries, 0, 10, true) as $c => $cnt) {
    echo "  $c: $cnt ports\n";
}
