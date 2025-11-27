# Changelog

All notable changes to `hiroshiaki/datafabric-sdk-php` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **AI-Powered OCR**: Document upload with automatic data extraction using AI models (GPT-4o Vision, Gemini 2.0 Flash, Claude 3 Opus)
- `uploadDocument()` method for uploading ID cards, passports, and driver's licenses with AI OCR
- `getDocuments()` method to retrieve all uploaded documents for a KYC check
- New response classes: `KycDocumentResponse` and `KycDocumentListResponse`
- Comprehensive OCR data accessors for extracted fields (name, ID number, date of birth, nationality, etc.)
- Document validation (file type, size, format checks)
- Support for multiple image types: front, back, selfie, proof_of_address
- Image format validation (JPEG, PNG, WebP, max 10MB)
- `getExpiresAt()` and `isExpired()` methods in `KycCheckResponse`
- New example file: `examples/upload-document.php` demonstrating AI OCR features
- Enhanced README with AI OCR documentation and best practices

### Changed
- Updated README with comprehensive AI OCR documentation
- Expanded best practices section with document upload guidelines
- Enhanced Quick Start example to include document upload
- Updated response object documentation to include new OCR fields

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
