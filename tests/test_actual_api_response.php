<?php

/**
 * Quick test to verify the actual API response structure works correctly
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycCheckResponse;

// Simulate the actual API response from your logs
$actualApiResponse = [
    "status" => "success",
    "check_id" => "db4e12df-4c4e-4aba-ad60-73116a2be8d7",
    "result" => null,
    "kyc_status" => "pending",
    "risk_score" => null,
    "message" => "KYC check created and processed.",
    "verification_details" => [],
    "expires_at" => "2025-12-27T15:56:40.000000Z",
    "request_id" => "test_request_123"
];

echo "Testing actual API response structure...\n\n";

$response = new KycCheckResponse($actualApiResponse);

echo "Check ID: " . $response->getCheckId() . "\n";
echo "Status: " . $response->getStatus() . "\n";
echo "Result: " . ($response->getResult() ?? 'null') . "\n";
echo "Risk Score: " . ($response->getRiskScore() ?? 'null') . "\n";
echo "Request ID: " . ($response->getRequestId() ?? 'null') . "\n";
echo "Expires At: " . ($response->getExpiresAt() ?? 'null') . "\n";
echo "Is Pending: " . ($response->isPending() ? 'true' : 'false') . "\n";
echo "\n";

// Verify check_id is not null
if ($response->getCheckId() === '') {
    echo "❌ FAILED: check_id is empty!\n";
    exit(1);
} elseif ($response->getCheckId() === 'db4e12df-4c4e-4aba-ad60-73116a2be8d7') {
    echo "✅ SUCCESS: check_id correctly extracted!\n";
    exit(0);
} else {
    echo "❌ FAILED: check_id has wrong value: " . $response->getCheckId() . "\n";
    exit(1);
}
