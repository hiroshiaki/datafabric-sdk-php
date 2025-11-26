<?php

/**
 * Example: Error handling patterns
 * 
 * Demonstrates best practices for error handling
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

echo "=== Error Handling Examples ===\n\n";

// Example 1: Basic try-catch
echo "1. Basic error handling...\n";
try {
    $client = new KycClient('dfb_test_invalid_key');
    $response = $client->createCheck([
        'first_name' => 'Test',
        'last_name' => 'User',
        'date_of_birth' => '1990-01-01',
        'document_type' => 'passport',
        'document_number' => 'TEST123',
    ]);
} catch (KycException $e) {
    echo "   ❌ Caught error: {$e->getMessage()}\n";
    echo "   Code: {$e->getCode()}\n\n";
}

// Example 2: Validation errors
echo "2. Handling validation errors...\n";
$client = new KycClient($_ENV['DATAFABRIC_API_KEY'] ?? 'dfb_test_key');

$invalidData = [
    ['first_name' => '', 'last_name' => 'Test'],  // Empty first name
    ['first_name' => 'Test', 'document_type' => 'invalid_type'],  // Invalid document type
    ['date_of_birth' => 'invalid-date'],  // Invalid date format
];

foreach ($invalidData as $index => $data) {
    try {
        $data = array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '1990-01-01',
            'document_type' => 'passport',
            'document_number' => 'TEST' . $index,
        ], $data);
        
        $response = $client->createCheck($data);
    } catch (KycException $e) {
        echo "   ❌ Validation error: {$e->getMessage()}\n";
    }
}
echo "\n";

// Example 3: Network errors with retry logic
echo "3. Retry logic for network errors...\n";
function createCheckWithRetry($client, $data, $maxRetries = 3)
{
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            $attempt++;
            echo "   Attempt {$attempt}...\n";
            return $client->createCheck($data);
        } catch (KycException $e) {
            echo "   ❌ Attempt {$attempt} failed: {$e->getMessage()}\n";
            
            if ($attempt >= $maxRetries) {
                throw new KycException("Max retries exceeded: " . $e->getMessage());
            }
            
            // Wait before retry (exponential backoff)
            $waitTime = pow(2, $attempt);
            echo "   Waiting {$waitTime} seconds before retry...\n";
            sleep($waitTime);
        }
    }
}

try {
    // This might fail due to network issues
    $response = createCheckWithRetry($client, [
        'first_name' => 'Retry',
        'last_name' => 'Test',
        'date_of_birth' => '1990-01-01',
        'document_type' => 'passport',
        'document_number' => 'RETRY123',
    ]);
    echo "   ✅ Check created after retries: {$response->getCheckId()}\n\n";
} catch (KycException $e) {
    echo "   ❌ All retries failed\n\n";
}

// Example 4: Logging errors
echo "4. Logging errors for debugging...\n";
function logError(KycException $e, $context = [])
{
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'context' => $context,
    ];
    
    // In production, use a proper logging system
    error_log(json_encode($logEntry));
    
    echo "   📝 Error logged: {$e->getMessage()}\n";
}

try {
    $client->createCheck([
        'first_name' => 'Log',
        'last_name' => 'Test',
        // Missing required fields
    ]);
} catch (KycException $e) {
    logError($e, [
        'user_id' => 'user_123',
        'action' => 'create_check',
    ]);
}
echo "\n";

// Example 5: User-friendly error messages
echo "5. User-friendly error messages...\n";
function getUserFriendlyMessage(KycException $e): string
{
    $message = $e->getMessage();
    
    // Map technical errors to user-friendly messages
    if (str_contains($message, 'Missing required field')) {
        return "Please fill in all required fields.";
    }
    
    if (str_contains($message, 'Invalid document_type')) {
        return "Please select a valid document type.";
    }
    
    if (str_contains($message, 'Invalid date')) {
        return "Please enter a valid date of birth (YYYY-MM-DD).";
    }
    
    if (str_contains($message, 'rate limit')) {
        return "Too many requests. Please try again in a few minutes.";
    }
    
    if (str_contains($message, 'authentication') || str_contains($message, 'API key')) {
        return "Authentication failed. Please check your API key.";
    }
    
    // Generic fallback
    return "An error occurred. Please try again later.";
}

try {
    $client->createCheck(['first_name' => 'Test']);
} catch (KycException $e) {
    echo "   Technical: {$e->getMessage()}\n";
    echo "   User-friendly: " . getUserFriendlyMessage($e) . "\n";
}
echo "\n";

// Example 6: Graceful degradation
echo "6. Graceful degradation...\n";
function createCheckSafely($client, $data)
{
    try {
        return [
            'success' => true,
            'response' => $client->createCheck($data),
            'error' => null,
        ];
    } catch (KycException $e) {
        return [
            'success' => false,
            'response' => null,
            'error' => $e->getMessage(),
        ];
    }
}

$result = createCheckSafely($client, [
    'first_name' => 'Safe',
    'last_name' => 'Test',
    'date_of_birth' => '1990-01-01',
    'document_type' => 'passport',
    'document_number' => 'SAFE123',
]);

if ($result['success']) {
    echo "   ✅ Success: {$result['response']->getCheckId()}\n";
} else {
    echo "   ⚠️  Failed gracefully: {$result['error']}\n";
    echo "   Application continues running...\n";
}

echo "\n✅ Error handling examples completed!\n";
