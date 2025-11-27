# DataFabric PHP SDK

[![Pipeline Status](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/badges/main/pipeline.svg)](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/commits/main)
[![Latest Version](https://img.shields.io/packagist/v/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](https://packagist.org/packages/hiroshiaki/datafabric-sdk-php)
[![License](https://img.shields.io/packagist/l/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](https://packagist.org/packages/hiroshiaki/datafabric-sdk-php)

Official PHP SDK for the DataFabric API. Integrate KYC verification, maps, routing, and more into your PHP applications.

## Features

- **✨ AI-Powered OCR** - Automatic document data extraction using GPT-4o Vision, Gemini 2.0 Flash, and Claude 3 Opus
- **KYC Verification** - Identity verification and document checks with risk scoring
- **Document Upload** - Upload ID cards, passports, driver's licenses with image validation
- **Easy Integration** - Simple, intuitive API with strongly-typed responses
- **PSR-4 Autoloading** - Modern PHP standards
- **Fully Typed** - PHP 8.0+ with strict type declarations
- **Test & Live Modes** - Separate environments for development and production
- **Comprehensive Error Handling** - Custom exceptions with detailed messages
- **Webhook Support** - Real-time notifications for async processing

## Requirements

- PHP 8.0 or higher
- Composer
- ext-json (typically included with PHP)
- GuzzleHTTP 7.0 or higher

## Installation

Install via Composer:

```bash
composer require hiroshiaki/datafabric-sdk-php
```

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

// Initialize the client with your API key
$client = new KycClient('dfb_test_your_api_key_here');

try {
    // Create a KYC check
    $checkResponse = $client->createCheck([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-05-15',
        'document_type' => 'passport',
        'document_number' => 'AB123456',
        'email' => 'john@example.com'
    ]);
    
    echo "Check ID: " . $checkResponse->getCheckId() . "\n";
    echo "Status: " . $checkResponse->getStatus() . "\n";
    
    // Upload document with AI OCR
    $documentResponse = $client->uploadDocument(
        $checkResponse->getCheckId(),
        '/path/to/passport.jpg',
        'front',
        true  // Enable AI OCR
    );
    
    if ($documentResponse->hasOcrData()) {
        echo "Extracted Name: " . $documentResponse->getExtractedFullName() . "\n";
        echo "ID Number: " . $documentResponse->getExtractedIdNumber() . "\n";
        echo "Confidence: " . $documentResponse->getConfidence() . "%\n";
    }
    
    // Check verification result
    $finalCheck = $client->getCheck($checkResponse->getCheckId());
    if ($finalCheck->isApproved()) {
        echo "✅ Verification approved!\n";
        echo "Risk Score: " . $finalCheck->getRiskScore() . "\n";
    }
    
} catch (KycException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Configuration

### API Keys

Get your API keys from the [DataFabric Dashboard](https://datafabric.hiroshiaki.com):

- **Test keys**: `dfb_test_*` - Use for development and testing (no charges)
- **Live keys**: `dfb_live_*` - Use for production

```php
// Test mode
$client = new KycClient('dfb_test_abc123...');

// Production mode
$client = new KycClient('dfb_live_xyz789...');
```

### Custom Configuration

```php
// Custom base URL (e.g., for local development)
$client = new KycClient(
    'dfb_test_abc123',
    'http://localhost:8000'
);

// Custom Guzzle options
$client = new KycClient(
    'dfb_test_abc123',
    'https://datafabric.hiroshiaki.com',
    [
        'timeout' => 60,
        'connect_timeout' => 10,
        'verify' => true
    ]
);
```

## Usage

### Create KYC Check

Create a new identity verification check:

```php
$response = $client->createCheck([
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'date_of_birth' => '1985-08-20',
    'document_type' => 'drivers_license',
    'document_number' => 'DL9876543',
    
    // Optional fields
    'user_reference' => 'user_12345',
    'country' => 'US',
    'email' => 'jane@example.com',
    'phone' => '+1234567890',
    'address' => [
        'street' => '456 Oak Ave',
        'city' => 'San Francisco',
        'state' => 'CA',
        'postal_code' => '94102',
        'country' => 'US'
    ],
    'webhook_url' => 'https://your-app.com/webhooks/kyc',
    'metadata' => ['order_id' => 'ORD-123']
]);

echo $response->getCheckId();
```

#### Required Fields

- `first_name` - First name
- `last_name` - Last name
- `date_of_birth` - Date of birth (YYYY-MM-DD format)
- `document_type` - One of: `passport`, `drivers_license`, `national_id`, `residence_permit`
- `document_number` - Document number

### Get Check Status

Retrieve the status of an existing check:

```php
$response = $client->getCheck('chk_abc123...');

echo "Status: " . $response->getStatus() . "\n";
echo "Result: " . $response->getResult() . "\n";
echo "Risk Score: " . $response->getRiskScore() . "\n";

// Check status helpers
if ($response->isCompleted()) {
    if ($response->isApproved()) {
        echo "✅ Approved\n";
    } elseif ($response->isRejected()) {
        echo "❌ Rejected\n";
    } elseif ($response->requiresReview()) {
        echo "⚠️ Requires manual review\n";
    }
}
```

#### Status Values

- `pending` - Check is queued
- `in_progress` - Currently processing
- `completed` - Processing finished
- `failed` - Processing failed (can be reprocessed)

#### Result Values (when completed)

- `approved` - Identity verified, low risk
- `rejected` - Failed verification
- `review_required` - Manual review needed

#### Risk Scores

- `low` - All checks passed
- `medium` - Minor issues detected
- `high` - Significant risk factors

### List Checks

List checks with optional filtering:

```php
$response = $client->listChecks([
    'status' => 'completed',
    'result' => 'approved',
    'per_page' => 50
]);

foreach ($response->getChecks() as $check) {
    echo $check->getCheckId() . ": " . $check->getResult() . "\n";
}

echo "Total: " . $response->getTotal() . "\n";
echo "Page: " . $response->getCurrentPage() . " of " . $response->getLastPage() . "\n";

if ($response->hasMorePages()) {
    echo "More pages available\n";
}
```

### Reprocess Failed Check

Retry a failed check:

```php
$response = $client->reprocessCheck('chk_abc123...');
echo "New status: " . $response->getStatus() . "\n";
```

### Upload Documents with AI OCR

Upload ID cards, passports, or driver's licenses with automatic data extraction:

```php
// Upload front of ID card with AI OCR enabled
$documentResponse = $client->uploadDocument(
    'chk_abc123...',           // Check ID
    '/path/to/id-front.jpg',   // Image file path
    'front',                   // Image type: front|back|selfie|proof_of_address
    true                       // Enable AI OCR (default: true)
);

echo "Document ID: " . $documentResponse->getId() . "\n";

// Access AI-extracted data
if ($documentResponse->hasOcrData()) {
    echo "Extracted Name: " . $documentResponse->getExtractedFullName() . "\n";
    echo "ID Number: " . $documentResponse->getExtractedIdNumber() . "\n";
    echo "Date of Birth: " . $documentResponse->getExtractedDateOfBirth() . "\n";
    echo "Nationality: " . $documentResponse->getExtractedNationality() . "\n";
    echo "OCR Confidence: " . $documentResponse->getConfidence() . "%\n";
}
```

#### Supported Image Types

- `front` - Front of document (ID card, passport, driver's license)
- `back` - Back of document
- `selfie` - Selfie photo for facial verification
- `proof_of_address` - Utility bill, bank statement, etc.

#### Image Requirements

- **Formats**: JPEG, PNG, WebP
- **Max Size**: 10MB
- **Recommended**: Minimum 1000px width for best OCR accuracy
- **Quality**: Clear, well-lit, no glare or shadows

#### AI-Extracted Fields

The OCR system can extract:
- Document type (passport, national_id, drivers_license)
- Full name, first name, last name
- ID/Document number
- Date of birth
- Gender
- Nationality
- Address
- Confidence score (0-100)

#### Supported Documents

- Malaysian IC (MyKad)
- Passports
- Driver's Licenses
- National IDs
- Residence Permits

#### AI Models Used

- Google Gemini 2.0 Flash
- GPT-4o Vision
- Claude 3 Opus

Processing time: 2-5 seconds per document

### Get All Documents

Retrieve all uploaded documents for a check:

```php
$documentsResponse = $client->getDocuments('chk_abc123...');

echo "Total Documents: " . $documentsResponse->getCount() . "\n";

foreach ($documentsResponse->getDocuments() as $doc) {
    echo "- Document #{$doc['id']} ({$doc['image_type']})";
    if ($doc['has_ocr_data'] ?? false) {
        echo " ✨ Has OCR data";
    }
    echo "\n";
}

// Filter by type
$frontDocs = $documentsResponse->getDocumentsByType('front');
```

## Response Objects

### KycCheckResponse

Single check response with helper methods:

```php
$response->getCheckId()              // string - Check ID
$response->getStatus()               // string - pending|in_progress|completed|failed
$response->getResult()               // string|null - approved|rejected|review_required
$response->getRiskScore()            // string|null - low|medium|high
$response->getVerificationDetails()  // array - Detailed verification results
$response->getRequestId()            // string|null - Request ID for support
$response->getExpiresAt()            // string|null - Expiration datetime (ISO 8601)

// Helper methods
$response->isApproved()              // bool
$response->isRejected()              // bool
$response->requiresReview()          // bool
$response->isPending()               // bool
$response->isCompleted()             // bool
$response->isExpired()               // bool

// Data export
$response->toArray()                 // array
$response->toJson()                  // string
$response->getRawData()              // array
```

### KycCheckListResponse

Paginated list of checks:

```php
$response->getChecks()               // KycCheckResponse[] - Array of checks
$response->getTotal()                // int - Total number of checks
$response->getPerPage()              // int - Checks per page
$response->getCurrentPage()          // int - Current page number
$response->getLastPage()             // int - Last page number
$response->hasMorePages()            // bool - More pages available
$response->getRawData()              // array - Raw response data
```

### KycDocumentResponse

Document upload response with AI OCR data:

```php
$response->getId()                      // int|null - Document ID
$response->getImageType()               // string|null - front|back|selfie|proof_of_address
$response->hasOcrData()                 // bool - Has AI-extracted data
$response->getOcrData()                 // array|null - Raw OCR data
$response->isSuccessful()               // bool - Upload successful

// AI-Extracted Fields
$response->getExtractedDocumentType()   // string|null - passport|national_id|drivers_license
$response->getExtractedFullName()       // string|null
$response->getExtractedFirstName()      // string|null
$response->getExtractedLastName()       // string|null
$response->getExtractedIdNumber()       // string|null
$response->getExtractedDateOfBirth()    // string|null - YYYY-MM-DD
$response->getExtractedGender()         // string|null
$response->getExtractedNationality()    // string|null
$response->getExtractedAddress()        // string|null
$response->getConfidence()              // int|null - OCR confidence (0-100)
$response->getProvider()                // string|null - AI provider used

// Data export
$response->toArray()                    // array
$response->toJson()                     // string
$response->getRawData()                 // array
```

### KycDocumentListResponse

List of documents for a check:

```php
$response->getDocuments()               // array - Array of document data
$response->getCount()                   // int - Total documents
$response->hasOcrData()                 // bool - Any documents have OCR data
$response->getDocumentsByType($type)    // array - Filter by image type
$response->getRawData()                 // array - Raw response data
```

## Error Handling

All SDK methods throw `KycException` on errors:

```php
use DataFabric\SDK\KycException;

try {
    $response = $client->createCheck($data);
} catch (KycException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    
    // Log for debugging
    error_log('KYC Error: ' . $e->getMessage());
}
```

### Common Error Scenarios

- Invalid API key → Authentication error
- Missing required fields → Validation error
- Rate limit exceeded → Rate limit error
- Network issues → Connection error

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

Run static analysis:

```bash
composer phpstan
```

Check code style:

```bash
composer cs-check
```

Fix code style:

```bash
composer cs-fix
```

## Examples

See the `examples/` directory for complete examples:

```bash
php examples/basic.php              # Basic SDK usage
php examples/create-check.php       # Create KYC checks
php examples/upload-document.php    # Upload documents with AI OCR
php examples/list-checks.php        # List and filter checks
php examples/error-handling.php     # Error handling patterns
```

## Best Practices

1. **Use Test Keys in Development**
   - Always use `dfb_test_` keys during development
   - Switch to `dfb_live_` keys only in production

2. **Store API Keys Securely**
   - Never commit API keys to version control
   - Use environment variables or secure vaults
   - Rotate keys periodically

3. **Handle Errors Gracefully**
   - Always wrap API calls in try-catch blocks
   - Log errors for debugging
   - Provide user-friendly error messages

4. **Use Webhooks**
   - Don't poll for status updates
   - Set up webhook endpoints for async notifications
   - Verify webhook signatures (HMAC-SHA256)

5. **Validate Before Sending**
   - Validate data client-side to reduce API calls
   - Check required fields before submission
   - Format dates correctly (YYYY-MM-DD)

6. **Document Upload Best Practices**
   - Upload high-quality images (min 1000px width)
   - Ensure documents are clearly visible and well-lit
   - Avoid glare, shadows, or obstructions
   - Use appropriate image types (front, back, selfie, proof_of_address)
   - Keep file sizes under 10MB
   - Use JPEG, PNG, or WebP formats

7. **OCR Data Usage**
   - Always check `hasOcrData()` before accessing extracted fields
   - Verify OCR confidence scores (aim for >90%)
   - Manually review cases with low confidence
   - Compare extracted data with user-submitted data
   - Handle null values gracefully

8. **Monitor API Usage**
   - Track request IDs for debugging
   - Monitor rate limits (10-120 req/min depending on plan)
   - Log all API interactions
   - Watch for expiration dates on checks (30 days)

9. **Keep SDK Updated**
   - Regularly update to the latest version
   - Review changelogs for breaking changes
   - Test updates in a staging environment

## Environment Variables

Store your API key in environment variables:

```php
// .env file
DATAFABRIC_API_KEY=dfb_test_your_key_here
DATAFABRIC_BASE_URL=https://datafabric.hiroshiaki.com
```

```php
// Load from environment
$client = new KycClient(
    $_ENV['DATAFABRIC_API_KEY'],
    $_ENV['DATAFABRIC_BASE_URL'] ?? 'https://datafabric.hiroshiaki.com'
);
```

## CI/CD & Automated Releases

This package uses GitLab CI/CD for automated testing and releases. See [docs/GITLAB_CI.md](docs/GITLAB_CI.md) for complete setup instructions.

**Automatic versioning based on commit messages:**

```bash
# Patch bump (v1.0.0 → v1.0.1)
git commit -m "fix: handle null responses"

# Minor bump (v1.0.1 → v1.1.0)
git commit -m "feat: add new validation method"

# Major bump (v1.1.0 → v2.0.0)
git commit -m "breaking: change method signatures"
```

The pipeline automatically:
- Tests all code (PHPStan, PSR-12, PHPUnit)
- Creates version tags
- Publishes to Packagist

## Framework Integration

### Laravel

Create a service provider:

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
                config('services.datafabric.api_key'),
                config('services.datafabric.base_url')
            );
        });
    }
}
```

```php
// config/services.php
'datafabric' => [
    'api_key' => env('DATAFABRIC_API_KEY'),
    'base_url' => env('DATAFABRIC_BASE_URL', 'https://datafabric.hiroshiaki.com'),
],
```

### Symfony

Define as a service:

```yaml
# config/services.yaml
services:
    DataFabric\SDK\KycClient:
        arguments:
            $apiKey: '%env(DATAFABRIC_API_KEY)%'
            $baseUrl: '%env(DATAFABRIC_BASE_URL)%'
```

## Documentation

- **[Getting Started Guide](docs/GETTING_STARTED.md)** - Comprehensive beginner's guide with framework integration
- **[API Documentation](https://datafabric.hiroshiaki.com/docs)** - Official DataFabric API documentation
- **[Examples Directory](examples/)** - Working code examples for all features

## Support

- **Documentation**: [https://datafabric.hiroshiaki.com/docs](https://datafabric.hiroshiaki.com/docs)
- **Issues**: [GitLab Issues](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/issues)
- **Email**: support@hiroshiaki.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.

## License

This SDK is licensed under the MIT License. See [LICENSE](LICENSE) for details.

## Credits

Developed and maintained by [Hiroshi Aki](https://hiroshiaki.com).

---

**DataFabric** - API-first data services for modern applications.

## Suggestions for a good README

Every project is different, so consider which of these sections apply to yours. The sections used in the template are suggestions for most open source projects. Also keep in mind that while a README can be too long and detailed, too long is better than too short. If you think your README is too long, consider utilizing another form of documentation rather than cutting out information.

## Contributing

Contributions are welcome! This project uses automated testing and semantic versioning:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes and ensure tests pass: `composer test`
4. Use semantic commit messages:
   - `fix:` for bug fixes (patch version)
   - `feat:` for new features (minor version)
   - `breaking:` for breaking changes (major version)
5. Push to your fork and submit a pull request

### Development Setup

```bash
git clone https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php.git
cd datafabric-sdk-php
composer install
composer test
```

See [docs/GITLAB_CI.md](docs/GITLAB_CI.md) for CI/CD details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- **Documentation**: [docs/](docs/)
- **Issues**: [GitLab Issues](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/issues)
- **API Documentation**: https://datafabric.hiroshiaki.com/docs

## Project Status

Active development. Automated releases via GitLab CI/CD.
