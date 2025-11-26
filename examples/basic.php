<?php

/**
 * Basic example of using the DataFabric PHP SDK
 * 
 * This example demonstrates the most common usage patterns
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

// Get API key from environment or use test key
$apiKey = $_ENV['DATAFABRIC_API_KEY'] ?? 'dfb_test_your_key_here';

// Initialize the client
$client = new KycClient($apiKey);

echo "=== DataFabric PHP SDK - Basic Example ===\n\n";

try {
    // 1. Create a KYC check
    echo "1. Creating a KYC check...\n";
    $response = $client->createCheck([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-05-15',
        'document_type' => 'passport',
        'document_number' => 'AB123456',
        'user_reference' => 'user_' . time(),
    ]);
    
    $checkId = $response->getCheckId();
    echo "✅ Check created: {$checkId}\n";
    echo "   Status: {$response->getStatus()}\n\n";
    
    // 2. Get check status
    echo "2. Retrieving check status...\n";
    $response = $client->getCheck($checkId);
    echo "   Status: {$response->getStatus()}\n";
    echo "   Result: " . ($response->getResult() ?? 'pending') . "\n";
    echo "   Risk Score: " . ($response->getRiskScore() ?? 'N/A') . "\n\n";
    
    // 3. List recent checks
    echo "3. Listing recent checks...\n";
    $listResponse = $client->listChecks(['per_page' => 5]);
    echo "   Total checks: {$listResponse->getTotal()}\n";
    echo "   Recent checks:\n";
    
    foreach ($listResponse->getChecks() as $check) {
        echo "   - {$check->getCheckId()}: {$check->getStatus()}";
        if ($check->getResult()) {
            echo " ({$check->getResult()})";
        }
        echo "\n";
    }
    
    echo "\n✅ Example completed successfully!\n";
    
} catch (KycException $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "   Code: {$e->getCode()}\n";
    exit(1);
}
