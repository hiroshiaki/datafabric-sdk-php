# DataFabric SDK PHP - Package Structure

This document describes the structure of the `hiroshiaki/datafabric-sdk-php` package.

## Directory Structure

```
datafabric-sdk-php/
├── src/                          # Source files (PSR-4 autoloaded)
│   ├── KycClient.php            # Main KYC client
│   ├── KycCheckResponse.php     # Single check response object
│   ├── KycCheckListResponse.php # List response object
│   └── KycException.php         # Custom exception class
│
├── tests/                        # PHPUnit tests
│   ├── BaseTestCase.php         # Base test class
│   └── KycClientTest.php        # Client tests
│
├── examples/                     # Usage examples
│   ├── basic.php                # Basic usage
│   ├── create-check.php         # Creating checks
│   ├── list-checks.php          # Listing/filtering checks
│   └── error-handling.php       # Error handling patterns
│
├── .github/                      # GitHub workflows (optional)
├── .gitattributes               # Git attributes for exports
├── .gitignore                   # Git ignore rules
├── CHANGELOG.md                 # Version history
├── composer.json                # Package definition
├── CONTRIBUTING.md              # Contribution guidelines
├── LICENSE                      # MIT License
├── phpcs.xml                    # PHP CodeSniffer config
├── phpstan.neon                 # PHPStan config
├── phpunit.xml.dist             # PHPUnit config
└── README.md                    # Main documentation
```

## Package Information

- **Name**: `hiroshiaki/datafabric-sdk-php`
- **Type**: `library`
- **License**: MIT
- **PHP Version**: 8.0+
- **Autoloading**: PSR-4

## Namespace

All classes use the `DataFabric\SDK` namespace:

```php
use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycCheckResponse;
use DataFabric\SDK\KycCheckListResponse;
use DataFabric\SDK\KycException;
```

## Key Files

### composer.json
Defines package metadata, dependencies, and autoloading rules. Follow Packagist naming conventions.

### src/
Contains all source code following PSR-4 autoloading standard. Each class in its own file.

### tests/
PHPUnit tests for the SDK. Follows same namespace structure as `src/` but under `DataFabric\SDK\Tests`.

### examples/
Practical examples demonstrating SDK usage. Can be run directly with PHP CLI.

## Installation for Users

```bash
composer require hiroshiaki/datafabric-sdk-php
```

## Development Setup

```bash
# Clone repository
git clone https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php.git
cd datafabric-sdk-php

# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer cs-check

# Fix code style
composer cs-fix

# Run static analysis
composer phpstan
```

## Publishing to Packagist

1. **Submit Package**: Go to https://packagist.org/packages/submit
2. **Enter Repository URL**: `https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php.git`
3. **Set Up Webhook**: Configure GitLab webhook for auto-updates
4. **Create Tags**: Tag releases following semantic versioning (v1.0.0, v1.1.0, etc.)

## Versioning

Follow Semantic Versioning (https://semver.org/):

- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

Example:
```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

## Best Practices

1. **PSR Standards**: Follow PSR-4 (autoloading) and PSR-12 (coding style)
2. **Type Declarations**: Use strict types and full type hints
3. **Documentation**: PHPDoc for all public methods
4. **Testing**: Maintain high test coverage
5. **Semantic Versioning**: Version releases properly
6. **Changelog**: Keep CHANGELOG.md updated
7. **Security**: Never commit API keys or secrets

## Support Resources

- **Packagist Guidelines**: https://packagist.org/about
- **Composer Documentation**: https://getcomposer.org/doc/
- **PHP-FIG PSR Standards**: https://www.php-fig.org/psr/
- **Semantic Versioning**: https://semver.org/
