# DataFabric PHP SDK - AI Coding Agent Instructions

## Project Overview

This is a PHP SDK library for the DataFabric API (KYC verification service). The SDK follows modern PHP best practices with PSR-4 autoloading, strict typing (PHP 8.0+), and comprehensive validation.

**Architecture**: Simple client-response pattern with a single `KycClient` class that handles all API interactions via GuzzleHTTP, returning strongly-typed response objects.

## Core Components

- **`KycClient`** (`src/KycClient.php`): Main SDK entry point. Handles HTTP requests, authentication via `X-Api-Key` header, and environment detection (test vs live keys)
- **`KycCheckResponse`** (`src/KycCheckResponse.php`): Wraps single check API responses with typed accessors
- **`KycCheckListResponse`** (`src/KycCheckListResponse.php`): Wraps list/pagination responses
- **`KycException`** (`src/KycException.php`): Custom exception for all SDK errors (wraps Guzzle exceptions)

## Key Patterns & Conventions

### API Key Validation
API keys follow a strict format: `dfb_test_*` for test mode, `dfb_live_*` for production. The SDK auto-detects mode using `str_starts_with()`:
```php
$this->testMode = str_starts_with($apiKey, 'dfb_test_');
```

### Required Field Validation
All `createCheck()` calls validate required fields in `validateCheckData()`:
- Required: `first_name`, `last_name`, `date_of_birth`, `document_type`, `document_number`
- `document_type` must be one of: `passport`, `drivers_license`, `national_id`, `residence_permit`
- `date_of_birth` must be `YYYY-MM-DD` format (validated with `DateTime::createFromFormat()`)

### Error Handling Pattern
Always catch `GuzzleException` and re-throw as `KycException` with context:
```php
try {
    $response = $this->httpClient->post('/api/v1/kyc/checks', ['json' => $data]);
    // ... handle response
} catch (GuzzleException $e) {
    throw new KycException("Failed to create KYC check: " . $e->getMessage(), $e->getCode(), $e);
}
```

### Response Object Pattern
Response classes wrap raw API arrays and provide typed accessors with null-safety:
```php
public function getStatus(): string
{
    $status = $this->data['kyc_status'] ?? $this->data['status'] ?? '';
    return is_string($status) ? $status : '';
}
```
Always provide fallback values and type checks to handle API inconsistencies.

## Development Workflow

### Running Tests
```bash
composer test              # Run PHPUnit without coverage
composer test-coverage     # Generate HTML coverage report
```

### Code Quality Checks
```bash
composer phpstan           # Static analysis (level: max)
composer cs-check          # PSR-12 code style check
composer cs-fix            # Auto-fix code style issues
```

**Critical**: All four commands must pass before committing. The CI pipeline runs these in sequence.

### CI/CD Pipeline
GitLab CI (`.gitlab-ci.yml`) has three stages:

1. **test**: Runs on all branches/MRs. Executes `validate`, `phpstan`, `cs-check`, `test`
2. **auto-tag**: Creates version tags based on commit message prefixes (main/master only):
   - `breaking:` or `major:` → major version bump (e.g., 1.0.0 → 2.0.0)
   - `feat:` or `feature:` or `minor:` → minor bump (e.g., 1.0.0 → 1.1.0)
   - `fix:`, `patch:`, `chore:`, `docs:` → patch bump (e.g., 1.0.0 → 1.0.1)
3. **notify-packagist**: Triggers Packagist update after tagging (requires `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` env vars)

### Adding New API Methods
Follow this pattern for consistency:
1. Add method to `KycClient` with PHPDoc types
2. Validate inputs if applicable (see `validateCheckData()`)
3. Make HTTP call with `$this->httpClient->{get|post}()`
4. Wrap `GuzzleException` in `KycException`
5. Return appropriate response object
6. Add unit tests in `tests/KycClientTest.php`

### Response Object Guidelines
When creating new response classes:
- Accept `array $data` in constructor
- Store raw data in `protected array $data`
- Provide typed accessor methods with null-safety checks
- Use `??` operators for fallback values
- Handle API field variations (e.g., `kyc_status` vs `status`)

## Testing Conventions

- All tests extend `BaseTestCase` (extends PHPUnit's `TestCase`)
- Use descriptive test method names: `testClientInstantiation()`, `testCustomBaseUrl()`
- Test key constants: `$testApiKey = 'dfb_test_example_key'`
- Verify both test and live key modes
- Test validation errors with expected exceptions

## Package Distribution

- **Namespace**: `DataFabric\SDK`
- **Packagist**: `hiroshiaki/datafabric-sdk-php`
- **License**: MIT
- **PHP Version**: ^8.0
- **Primary Dependency**: `guzzlehttp/guzzle ^7.0`

## Important Files

- `examples/`: Runnable usage examples (basic, create-check, list-checks, error-handling)
- `docs/PACKAGE_STRUCTURE.md`: Complete package layout documentation
- `phpcs.xml`: PSR-12 ruleset targeting `src/` and `tests/`
- `phpstan.neon`: Max-level static analysis config
- `CONTRIBUTING.md`: Development setup, coding standards, PR process

## Common Gotchas

- Always use `rtrim($baseUrl, '/')` when setting base URL to avoid double-slashes in API paths
- The SDK uses `/api/v1/kyc/checks` endpoint pattern - maintain consistent versioning
- User-Agent header is set to `DataFabric-KYC-SDK/1.0.0` - update VERSION constant when bumping versions
- Default timeouts: 30s request, 10s connection
- JSON responses must be validated with `is_array($body)` before constructing response objects
