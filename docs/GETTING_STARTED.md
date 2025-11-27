# Getting Started with DataFabric PHP SDK

This guide will walk you through everything you need to know to start using the DataFabric PHP SDK in your project.

## Prerequisites

Before you begin, ensure you have:
- PHP 8.0 or higher
- Composer installed
- A DataFabric API key (get one at https://datafabric.hiroshiaki.com)

## Installation

### Using Composer (Recommended)

```bash
composer require hiroshiaki/datafabric-sdk-php
```

### Manual Installation

If you can't use Composer, download the source and include it:

```php
require_once 'path/to/src/KycClient.php';
require_once 'path/to/src/KycCheckResponse.php';
require_once 'path/to/src/KycCheckListResponse.php';
require_once 'path/to/src/KycException.php';
```

**Note**: Manual installation requires manually installing Guzzle HTTP client as well.

## Getting Your API Key

1. Sign up at https://datafabric.hiroshiaki.com
2. Navigate to Dashboard → API Keys
3. Create a new API key
4. Use `dfb_test_*` keys for development
5. Use `dfb_live_*` keys for production

## First Steps

### 1. Create Your First Client

```php
<?php

require 'vendor/autoload.php';

use DataFabric\SDK\KycClient;

// Initialize with your API key
$client = new KycClient('dfb_test_your_key_here');

// Check if in test mode
echo $client->isTestMode() ? 'Test mode' : 'Live mode';
```

### 2. Create Your First KYC Check

```php
use DataFabric\SDK\KycException;

try {
    $response = $client->createCheck([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-05-15',
        'document_type' => 'passport',
        'document_number' => 'AB123456'
    ]);
    
    echo "✅ Check created: " . $response->getCheckId() . "\n";
    echo "Status: " . $response->getStatus() . "\n";
    
} catch (KycException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

### 3. Check Status

```php
// Get check by ID
$checkId = 'chk_abc123...';
$response = $client->getCheck($checkId);

// Check status
if ($response->isCompleted()) {
    if ($response->isApproved()) {
        echo "✅ Approved\n";
    } elseif ($response->isRejected()) {
        echo "❌ Rejected\n";
    } elseif ($response->requiresReview()) {
        echo "⚠️ Needs review\n";
    }
}
```

### 4. Upload Documents with AI OCR

```php
// Upload document with AI-powered data extraction
$documentResponse = $client->uploadDocument(
    $checkId,
    '/path/to/id-card-front.jpg',
    'front',  // Image type: front|back|selfie|proof_of_address
    true      // Enable AI OCR (default: true)
);

// Access AI-extracted data
if ($documentResponse->hasOcrData()) {
    echo "✨ AI Extracted Data:\n";
    echo "Full Name: " . $documentResponse->getExtractedFullName() . "\n";
    echo "ID Number: " . $documentResponse->getExtractedIdNumber() . "\n";
    echo "Date of Birth: " . $documentResponse->getExtractedDateOfBirth() . "\n";
    echo "Nationality: " . $documentResponse->getExtractedNationality() . "\n";
    echo "Confidence: " . $documentResponse->getConfidence() . "%\n";
}
```

**Supported Documents:**
- Malaysian IC (MyKad)
- Passports
- Driver's Licenses
- National IDs
- Residence Permits

**Image Requirements:**
- Formats: JPEG, PNG, WebP
- Max size: 10MB
- Recommended: Min 1000px width for best OCR accuracy

### 5. List Your Checks

```php
// Get all checks
$response = $client->listChecks();

echo "Total checks: " . $response->getTotal() . "\n";

foreach ($response->getChecks() as $check) {
    echo "- {$check->getCheckId()}: {$check->getStatus()}\n";
}
```

## Common Patterns

### Environment Variables

Store your API key securely:

```php
// .env file
DATAFABRIC_API_KEY=dfb_test_your_key

// Load in your code
$client = new KycClient($_ENV['DATAFABRIC_API_KEY']);
```

### Error Handling

Always wrap API calls in try-catch:

```php
try {
    $response = $client->createCheck($data);
    // Success
} catch (KycException $e) {
    // Log error
    error_log('KYC Error: ' . $e->getMessage());
    
    // Show user-friendly message
    echo "Sorry, we couldn't process your verification. Please try again.";
}
```

### Validation

Validate data before sending:

```php
function validateCheckData($data) {
    $errors = [];
    
    if (empty($data['first_name'])) {
        $errors[] = 'First name is required';
    }
    
    if (empty($data['date_of_birth'])) {
        $errors[] = 'Date of birth is required';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_of_birth'])) {
        $errors[] = 'Date of birth must be in YYYY-MM-DD format';
    }
    
    return $errors;
}

// Use it
$errors = validateCheckData($userData);
if (!empty($errors)) {
    // Show errors to user
    foreach ($errors as $error) {
        echo "❌ $error\n";
    }
} else {
    // Proceed with API call
    $response = $client->createCheck($userData);
}
```

## Framework Integration

### Laravel

**Step 1**: Create a service provider

```php
// app/Providers/DataFabricServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DataFabric\SDK\KycClient;

class DataFabricServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(KycClient::class, function ($app) {
            return new KycClient(
                config('services.datafabric.api_key')
            );
        });
    }
}
```

**Step 2**: Add to config

```php
// config/services.php
'datafabric' => [
    'api_key' => env('DATAFABRIC_API_KEY'),
],
```

**Step 3**: Register provider

```php
// config/app.php
'providers' => [
    // ...
    App\Providers\DataFabricServiceProvider::class,
],
```

**Step 4**: Use in controllers

```php
use DataFabric\SDK\KycClient;

class KycController extends Controller
{
    public function __construct(private KycClient $client) {}
    
    public function verify(Request $request)
    {
        $response = $this->client->createCheck($request->validated());
        return response()->json($response->toArray());
    }
}
```

### Symfony

**Step 1**: Define service

```yaml
# config/services.yaml
services:
    DataFabric\SDK\KycClient:
        arguments:
            $apiKey: '%env(DATAFABRIC_API_KEY)%'
```

**Step 2**: Use with dependency injection

```php
use DataFabric\SDK\KycClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class KycController
{
    public function __construct(private KycClient $client) {}
    
    public function verify(Request $request): JsonResponse
    {
        $response = $this->client->createCheck($request->toArray());
        return new JsonResponse($response->toArray());
    }
}
```

## Configuration Options

### Custom Base URL

For local development or custom endpoints:

```php
$client = new KycClient(
    'dfb_test_key',
    'http://localhost:8000'
);
```

### Custom HTTP Options

Pass Guzzle options for advanced configuration:

```php
$client = new KycClient(
    'dfb_test_key',
    'https://datafabric.hiroshiaki.com',
    [
        'timeout' => 60,
        'connect_timeout' => 10,
        'verify' => true, // SSL verification
        'proxy' => 'http://proxy.example.com:8080',
        'headers' => [
            'X-Custom-Header' => 'value'
        ]
    ]
);
```

## Testing Your Integration

### Unit Testing

```php
use PHPUnit\Framework\TestCase;
use DataFabric\SDK\KycClient;

class KycIntegrationTest extends TestCase
{
    private KycClient $client;
    
    protected function setUp(): void
    {
        $this->client = new KycClient($_ENV['DATAFABRIC_TEST_API_KEY']);
    }
    
    public function testCreateCheck(): void
    {
        $response = $this->client->createCheck([
            'first_name' => 'Test',
            'last_name' => 'User',
            'date_of_birth' => '1990-01-01',
            'document_type' => 'passport',
            'document_number' => 'TEST123'
        ]);
        
        $this->assertNotEmpty($response->getCheckId());
        $this->assertEquals('pending', $response->getStatus());
    }
}
```

### Integration Testing

```php
// Create test check
$response = $client->createCheck($testData);
$checkId = $response->getCheckId();

// Wait a bit (in production, use webhooks)
sleep(2);

// Check status
$status = $client->getCheck($checkId);
assert($status->isCompleted());
```

## Production Checklist

Before going live:

- [ ] Switch to live API key (`dfb_live_*`)
- [ ] Set up error logging
- [ ] Implement retry logic
- [ ] Set up webhook endpoints
- [ ] Test error scenarios
- [ ] Monitor API usage
- [ ] Set up alerts for failures
- [ ] Document your integration
- [ ] Train support team
- [ ] Plan for rate limits

## Next Steps

Now that you're set up:

1. **Explore Examples**: Check the [examples/](../examples/) directory
2. **Read API Reference**: See full method documentation in [README.md](../README.md)
3. **Learn Best Practices**: Review error handling and optimization tips
4. **Join Community**: Get help via GitLab Issues or email

## Troubleshooting

### "Invalid API key" Error
- Check your API key is correct
- Ensure no extra spaces in the key
- Verify you're using the right environment (test vs live)

### "Connection timeout" Error
- Check your internet connection
- Increase timeout in client options
- Check if API is accessible from your server

### "Invalid response from API" Error
- Check API status page
- Verify your request data format
- Enable debug mode to see raw responses

## Getting Help

- **Examples**: See [examples/](../examples/) for working code
- **API Reference**: Check [README.md](../README.md) for detailed API docs
- **Issues**: Report bugs at https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/issues
- **Email**: support@hiroshiaki.com

---

**Ready to build something awesome? Let's go! 🚀**
