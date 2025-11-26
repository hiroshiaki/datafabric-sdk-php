# Changelog

All notable changes to `hiroshiaki/datafabric-sdk-php` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-11-26

### Added
- Initial release of DataFabric PHP SDK
- KYC client for identity verification
- Support for creating KYC checks
- Support for retrieving check status
- Support for listing checks with filters
- Support for reprocessing failed checks
- PSR-4 autoloading
- Comprehensive error handling with `KycException`
- Response objects: `KycCheckResponse` and `KycCheckListResponse`
- Test and live mode support (API key prefixes)
- Configurable base URL and HTTP options
- Full type declarations for PHP 8.0+
- GuzzleHTTP integration for HTTP requests
- Detailed documentation and examples

### Requirements
- PHP 8.0 or higher
- GuzzleHTTP 7.0 or higher
- ext-json

[Unreleased]: https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/compare/v1.0.0...HEAD
[1.0.0]: https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/releases/v1.0.0
