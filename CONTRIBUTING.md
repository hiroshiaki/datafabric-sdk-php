# Contributing to DataFabric PHP SDK

Thank you for your interest in contributing to the DataFabric PHP SDK! We welcome contributions from the community.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to support@hiroshiaki.com.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples**
- **Describe the behavior you observed and what you expected**
- **Include PHP version, SDK version, and OS details**
- **Include error messages and stack traces**

### Suggesting Enhancements

Enhancement suggestions are tracked as GitLab issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description of the suggested enhancement**
- **Explain why this enhancement would be useful**
- **List any alternative solutions you've considered**

### Pull Requests

- Fill in the pull request template
- Follow the coding standards
- Include tests for new features
- Update documentation as needed
- Ensure all tests pass

## Development Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://gitlab.hiroshiaki.com:8886/your-username/datafabric-sdk-php.git
   cd datafabric-sdk-php
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Create a branch for your changes:
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Coding Standards

This project follows PSR-12 coding standards. Please ensure your code adheres to these standards.

### Code Style

- Use 4 spaces for indentation (no tabs)
- Follow PSR-12 naming conventions
- Add PHPDoc blocks for all classes, methods, and properties
- Keep lines under 120 characters when possible
- Use type declarations for all parameters and return types

### Check Code Style

```bash
composer cs-check
```

### Fix Code Style

```bash
composer cs-fix
```

### Static Analysis

Run PHPStan to check for potential issues:

```bash
composer phpstan
```

## Testing

### Running Tests

Run the full test suite:

```bash
composer test
```

### Code Coverage

Generate code coverage report:

```bash
composer test-coverage
```

View the report in the `coverage/` directory.

### Writing Tests

- Write tests for all new features
- Maintain or improve code coverage
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)
- Mock external dependencies

Example test structure:

```php
public function testCreateCheckWithValidData(): void
{
    // Arrange
    $client = new KycClient('dfb_test_key');
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        // ...
    ];
    
    // Act
    $response = $client->createCheck($data);
    
    // Assert
    $this->assertInstanceOf(KycCheckResponse::class, $response);
    $this->assertNotEmpty($response->getCheckId());
}
```

## Pull Request Process

1. **Update Documentation**: Update README.md, CHANGELOG.md, and inline documentation as needed

2. **Add Tests**: Ensure your changes are covered by tests

3. **Run Quality Checks**:
   ```bash
   composer cs-check
   composer phpstan
   composer test
   ```

4. **Commit Your Changes**: Use clear and descriptive commit messages
   ```bash
   git commit -m "Add feature: description of feature"
   ```

5. **Push to Your Fork**:
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create a Merge Request**: 
   - Go to the repository on GitLab
   - Click "New merge request"
   - Select your branch
   - Fill in the merge request template
   - Submit for review

7. **Address Review Comments**: Be responsive to feedback and make requested changes

8. **Merge**: Once approved, a maintainer will merge your PR

## Commit Message Guidelines

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests after the first line

Examples:
```
Add KYC check validation

- Add validation for required fields
- Add validation for document types
- Add validation for date format

Fixes #123
```

## Branch Naming Convention

- `feature/` - New features
- `bugfix/` - Bug fixes
- `hotfix/` - Urgent production fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring

Examples:
- `feature/add-map-client`
- `bugfix/fix-date-validation`
- `docs/update-readme`

## Release Process

Maintainers follow this process for releases:

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Create a git tag
4. Push to GitLab
5. Submit to Packagist (auto-updates via webhook)

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Email us at support@hiroshiaki.com
- Check existing documentation

## Thank You!

Your contributions make this project better for everyone. We appreciate your time and effort! 🎉
