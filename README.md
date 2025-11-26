# DataFabric PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](https://packagist.org/packages/hiroshiaki/datafabric-sdk-php)
[![License](https://img.shields.io/packagist/l/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/hiroshiaki/datafabric-sdk-php.svg?style=flat-square)](https://packagist.org/packages/hiroshiaki/datafabric-sdk-php)

Official PHP SDK for the DataFabric API. Integrate KYC verification, maps, routing, and more into your PHP applications.

## Features

- **KYC Verification** - Identity verification and document checks
- **Easy Integration** - Simple, intuitive API
- **PSR-4 Autoloading** - Modern PHP standards
- **Fully Typed** - PHP 8.0+ with type declarations
- **Test & Live Modes** - Separate environments for development and production
- **Comprehensive Error Handling** - Custom exceptions with detailed messages

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
    $response = $client->createCheck([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-05-15',
        'document_type' => 'passport',
        'document_number' => 'AB123456'
    ]);
    
    echo "Check ID: " . $response->getCheckId() . "\n";
    echo "Status: " . $response->getStatus() . "\n";
    
    if ($response->isApproved()) {
        echo "✅ Verification approved!\n";
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

// Helper methods
$response->isApproved()              // bool
$response->isRejected()              // bool
$response->requiresReview()          // bool
$response->isPending()               // bool
$response->isCompleted()             // bool

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
php examples/basic.php
php examples/create-check.php
php examples/list-checks.php
php examples/error-handling.php
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
   - Verify webhook signatures

5. **Validate Before Sending**
   - Validate data client-side to reduce API calls
   - Check required fields before submission
   - Format dates correctly (YYYY-MM-DD)

6. **Monitor API Usage**
   - Track request IDs for debugging
   - Monitor rate limits
   - Log all API interactions

7. **Keep SDK Updated**
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

- **[Getting Started Guide](docs/GETTING_STARTED.md)** - Complete beginner's guide
- **[Setup Instructions](docs/SETUP_SUMMARY.md)** - Developer setup guide
- **[Publishing Guide](docs/PUBLISHING.md)** - How to publish to Packagist
- **[Package Structure](docs/PACKAGE_STRUCTURE.md)** - Understanding the codebase
- **[Complete Documentation](docs/)** - Full documentation index

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

## Name
Choose a self-explaining name for your project.

## Description
Let people know what your project can do specifically. Provide context and add a link to any reference visitors might be unfamiliar with. A list of Features or a Background subsection can also be added here. If there are alternatives to your project, this is a good place to list differentiating factors.

## Badges
On some READMEs, you may see small images that convey metadata, such as whether or not all the tests are passing for the project. You can use Shields to add some to your README. Many services also have instructions for adding a badge.

## Visuals
Depending on what you are making, it can be a good idea to include screenshots or even a video (you'll frequently see GIFs rather than actual videos). Tools like ttygif can help, but check out Asciinema for a more sophisticated method.

## Installation
Within a particular ecosystem, there may be a common way of installing things, such as using Yarn, NuGet, or Homebrew. However, consider the possibility that whoever is reading your README is a novice and would like more guidance. Listing specific steps helps remove ambiguity and gets people to using your project as quickly as possible. If it only runs in a specific context like a particular programming language version or operating system or has dependencies that have to be installed manually, also add a Requirements subsection.

## Usage
Use examples liberally, and show the expected output if you can. It's helpful to have inline the smallest example of usage that you can demonstrate, while providing links to more sophisticated examples if they are too long to reasonably include in the README.

## Support
Tell people where they can go to for help. It can be any combination of an issue tracker, a chat room, an email address, etc.

## Roadmap
If you have ideas for releases in the future, it is a good idea to list them in the README.

## Contributing
State if you are open to contributions and what your requirements are for accepting them.

For people who want to make changes to your project, it's helpful to have some documentation on how to get started. Perhaps there is a script that they should run or some environment variables that they need to set. Make these steps explicit. These instructions could also be useful to your future self.

You can also document commands to lint the code or run tests. These steps help to ensure high code quality and reduce the likelihood that the changes inadvertently break something. Having instructions for running tests is especially helpful if it requires external setup, such as starting a Selenium server for testing in a browser.

## Authors and acknowledgment
Show your appreciation to those who have contributed to the project.

## License
For open source projects, say how it is licensed.

## Project status
If you have run out of energy or time for your project, put a note at the top of the README saying that development has slowed down or stopped completely. Someone may choose to fork your project or volunteer to step in as a maintainer or owner, allowing your project to keep going. You can also make an explicit request for maintainers.
