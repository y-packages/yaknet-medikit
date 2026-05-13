<?php

/**
 * YakNet \ Medikit Demo - Broken Script
 * 
 * To run this:
 * 1. composer install
 * 2. php broken.php YOUR_API_KEY
 */

require_once __DIR__ . '/vendor/autoload.php';

use YakNet\Medikit\Medikit;

$apiKey = $argv[1] ?? 'YOUR_API_KEY_HERE';

if ($apiKey === 'YOUR_API_KEY_HERE') {
    echo "\e[33m[TIP] Provide your Gemini API Key as an argument: php broken.php <key>\e[0m\n";
}

// Register the immunity system
Medikit::register($apiKey);

echo "Starting a risky operation...\n";

// --- THE BUGGY CODE ---

function calculateTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        // BUG: Trying to access property of a non-object (e.g., if item is an array or null)
        $total += $item->price; 
    }
    return $total;
}

$cart = [
    (object)['name' => 'Apple', 'price' => 10],
    ['name' => 'Orange', 'price' => 5], // This is an array, will trigger error in calculateTotal
];

echo calculateTotal($cart);

echo "Operation finished successfully (Wait, this shouldn't happen!)\n";
