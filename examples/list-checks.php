<?php

/**
 * Example: Listing and filtering KYC checks
 * 
 * Demonstrates different ways to query and filter checks
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

$apiKey = $_ENV['DATAFABRIC_API_KEY'] ?? 'dfb_test_your_key_here';
$client = new KycClient($apiKey);

echo "=== Listing KYC Checks ===\n\n";

try {
    // Example 1: List all checks (default pagination)
    echo "1. Listing all checks (first page)...\n";
    $response = $client->listChecks();
    
    echo "   Total: {$response->getTotal()}\n";
    echo "   Page: {$response->getCurrentPage()} of {$response->getLastPage()}\n";
    echo "   Per page: {$response->getPerPage()}\n";
    echo "   Has more: " . ($response->hasMorePages() ? 'Yes' : 'No') . "\n\n";
    
    // Example 2: List with custom pagination
    echo "2. Custom pagination (10 per page)...\n";
    $response = $client->listChecks(['per_page' => 10]);
    
    foreach ($response->getChecks() as $check) {
        echo "   - {$check->getCheckId()}\n";
        echo "     Status: {$check->getStatus()}";
        if ($check->getResult()) {
            echo " | Result: {$check->getResult()}";
        }
        if ($check->getRiskScore()) {
            echo " | Risk: {$check->getRiskScore()}";
        }
        echo "\n";
    }
    echo "\n";
    
    // Example 3: Filter by status
    echo "3. Filtering by status (completed)...\n";
    $response = $client->listChecks(['status' => 'completed']);
    echo "   Found {$response->getTotal()} completed checks\n\n";
    
    // Example 4: Filter by result
    echo "4. Filtering by result (approved)...\n";
    $response = $client->listChecks(['result' => 'approved']);
    echo "   Found {$response->getTotal()} approved checks\n";
    
    if ($response->getTotal() > 0) {
        echo "   Recent approved checks:\n";
        foreach (array_slice($response->getChecks(), 0, 3) as $check) {
            echo "   - {$check->getCheckId()} (Risk: {$check->getRiskScore()})\n";
        }
    }
    echo "\n";
    
    // Example 5: Filter by user reference
    echo "5. Filtering by user reference...\n";
    $response = $client->listChecks(['user_reference' => 'user_123']);
    echo "   Found {$response->getTotal()} checks for user_123\n\n";
    
    // Example 6: Combined filters
    echo "6. Combined filters (completed + approved)...\n";
    $response = $client->listChecks([
        'status' => 'completed',
        'result' => 'approved',
        'per_page' => 5
    ]);
    echo "   Found {$response->getTotal()} completed & approved checks\n\n";
    
    // Example 7: Pagination walkthrough
    echo "7. Walking through pages...\n";
    $currentPage = 1;
    $totalProcessed = 0;
    
    do {
        $response = $client->listChecks([
            'per_page' => 5,
            'page' => $currentPage
        ]);
        
        echo "   Page {$currentPage}: " . count($response->getChecks()) . " checks\n";
        $totalProcessed += count($response->getChecks());
        
        $currentPage++;
        
        // Safety limit for example
        if ($currentPage > 3) {
            echo "   (Stopping at page 3 for example)\n";
            break;
        }
        
    } while ($response->hasMorePages());
    
    echo "   Total processed: {$totalProcessed}\n\n";
    
    // Example 8: Processing all checks
    echo "8. Processing checks with helper methods...\n";
    $response = $client->listChecks(['per_page' => 10]);
    
    $stats = [
        'approved' => 0,
        'rejected' => 0,
        'review' => 0,
        'pending' => 0,
    ];
    
    foreach ($response->getChecks() as $check) {
        if ($check->isApproved()) {
            $stats['approved']++;
        } elseif ($check->isRejected()) {
            $stats['rejected']++;
        } elseif ($check->requiresReview()) {
            $stats['review']++;
        } elseif ($check->isPending()) {
            $stats['pending']++;
        }
    }
    
    echo "   Statistics:\n";
    echo "   - Approved: {$stats['approved']}\n";
    echo "   - Rejected: {$stats['rejected']}\n";
    echo "   - Needs Review: {$stats['review']}\n";
    echo "   - Pending: {$stats['pending']}\n";
    
    echo "\n✅ Examples completed!\n";
    
} catch (KycException $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}
