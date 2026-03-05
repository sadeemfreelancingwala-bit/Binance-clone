<?php
// realtime.php - Simulated Live Price API for CryptoX Pro Exchange
header('Content-Type: application/json');
 
// Function to simulate price fluctuation
function fluctuate($price, $percentage = 0.05) {
    $change = (rand(-100, 100) / 10000) * $percentage * $price;
    return round($price + $change, 2);
}
 
// Initial/Base prices
$prices = [
    'BTC' => 64250.00,
    'ETH' => 3450.00,
    'BNB' => 580.00,
    'SOL' => 145.00,
    'XRP' => 0.62
];
 
$response = [];
 
foreach ($prices as $coin => $price) {
    // Basic randomization logic for demo
    $new_price = fluctuate($price);
    $change = ($new_price - $price) / $price * 100;
 
    $response[$coin] = [
        'price' => number_format($new_price, 2),
        'change' => number_format($change, 2),
        'raw_price' => $new_price
    ];
}
 
echo json_encode($response);
?>
 
