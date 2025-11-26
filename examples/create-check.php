<?php

/**
 * Example: Creating KYC checks with various configurations
 * 
 * Demonstrates different ways to create KYC checks
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

$apiKey = $_ENV['DATAFABRIC_API_KEY'] ?? 'dfb_test_your_key_here';
$client = new KycClient($apiKey);

echo "=== Creating KYC Checks ===\n\n";

// Example 1: Minimal check
echo "1. Creating minimal check (required fields only)...\n";
try {
    $response = $client->createCheck([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'date_of_birth' => '1985-03-20',
        'document_type' => 'drivers_license',
        'document_number' => 'DL123456',
    ]);
    echo "✅ Created: {$response->getCheckId()}\n\n";
} catch (KycException $e) {
    echo "❌ Error: {$e->getMessage()}\n\n";
}

// Example 2: Full check with all optional fields
echo "2. Creating comprehensive check (all fields)...\n";
try {
    $response = $client->createCheck([
        // Required fields
        'first_name' => 'Michael',
        'last_name' => 'Johnson',
        'date_of_birth' => '1992-07-10',
        'document_type' => 'passport',
        'document_number' => 'P9876543',
        
        // Optional fields
        'user_reference' => 'user_' . uniqid(),
        'country' => 'US',
        'email' => 'michael.johnson@example.com',
        'phone' => '+1234567890',
        'address' => [
            'street' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US'
        ],
        'webhook_url' => 'https://your-app.com/webhooks/kyc',
        'metadata' => [
            'order_id' => 'ORD-' . time(),
            'source' => 'web_signup',
            'campaign' => 'summer_2024'
        ]
    ]);
    
    echo "✅ Created: {$response->getCheckId()}\n";
    echo "   User Reference: user_" . uniqid() . "\n";
    echo "   Request ID: {$response->getRequestId()}\n\n";
    
} catch (KycException $e) {
    echo "❌ Error: {$e->getMessage()}\n\n";
}

// Example 3: Batch creation
echo "3. Creating multiple checks (batch)...\n";
$userData = [
    ['first_name' => 'Alice', 'last_name' => 'Williams', 'document_type' => 'national_id', 'document_number' => 'NI111111'],
    ['first_name' => 'Bob', 'last_name' => 'Brown', 'document_type' => 'passport', 'document_number' => 'P222222'],
    ['first_name' => 'Carol', 'last_name' => 'Davis', 'document_type' => 'drivers_license', 'document_number' => 'DL333333'],
];

$checkIds = [];
foreach ($userData as $index => $user) {
    try {
        $response = $client->createCheck([
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'date_of_birth' => '1990-01-01',
            'document_type' => $user['document_type'],
            'document_number' => $user['document_number'],
            'user_reference' => 'batch_' . time() . '_' . $index,
        ]);
        
        $checkIds[] = $response->getCheckId();
        echo "   ✅ {$user['first_name']} {$user['last_name']}: {$response->getCheckId()}\n";
        
    } catch (KycException $e) {
        echo "   ❌ {$user['first_name']} {$user['last_name']}: {$e->getMessage()}\n";
    }
}

echo "\n📊 Created " . count($checkIds) . " checks\n";

// Example 4: Validation error handling
echo "\n4. Handling validation errors...\n";
try {
    $response = $client->createCheck([
        'first_name' => 'Invalid',
        'last_name' => 'Check',
        // Missing required fields: date_of_birth, document_type, document_number
    ]);
} catch (KycException $e) {
    echo "❌ Expected validation error: {$e->getMessage()}\n";
}

echo "\n✅ Examples completed!\n";
