# DataFabric SDK - Main Project Update

## Update Instructions for Main Project

The PHP SDK has been extracted into a standalone Composer package. Update the main project's SDK documentation to reference the new package.

### File to Update

**Location**: `/Users/rahmanazhar/Documents/Laravel/datafabric/sdk/README.md`

### Recommended Changes

Replace the PHP section with:

```markdown
### PHP

**Composer Package (Recommended)**

Install via Composer:

```bash
composer require hiroshiaki/datafabric-sdk-php
```

```php
<?php
require 'vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

$client = new KycClient('dfb_test_yourApiKey');

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
    echo "Result: " . $response->getResult() . "\n";
    
    if ($response->isApproved()) {
        echo "✅ Approved!\n";
    }
    
} catch (KycException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

**Documentation**: https://packagist.org/packages/hiroshiaki/datafabric-sdk-php

**Repository**: https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php

---

**Standalone Version** (without Composer)

If you can't use Composer, copy the SDK files:

```php
<?php
require_once 'sdk/php/KycClient.php';
// ... (keep existing standalone instructions)
```
```

### Additional Updates

1. **Main Project README** (`/Users/rahmanazhar/Documents/Laravel/datafabric/README.md`)
   
   Add a section about the PHP SDK package:
   
   ```markdown
   ## Official SDKs
   
   ### PHP SDK
   
   Install via Composer:
   ```bash
   composer require hiroshiaki/datafabric-sdk-php
   ```
   
   Documentation: https://packagist.org/packages/hiroshiaki/datafabric-sdk-php
   
   ### Other SDKs
   
   - JavaScript/Node.js: See `sdk/javascript/`
   - Python: See `sdk/python/`
   ```

2. **SDK Directory Structure**
   
   Keep the standalone PHP files in `sdk/php/` for backward compatibility, but add a note:
   
   ```markdown
   # sdk/php/README.md
   
   **Note**: This is the standalone version. For new projects, we recommend using the Composer package:
   
   ```bash
   composer require hiroshiaki/datafabric-sdk-php
   ```
   
   See: https://packagist.org/packages/hiroshiaki/datafabric-sdk-php
   ```

### Benefits of the Composer Package

Mention these benefits in the documentation:

- ✅ **Easy Installation** - One command via Composer
- ✅ **Automatic Updates** - `composer update` to get latest version
- ✅ **Dependency Management** - Guzzle automatically installed
- ✅ **PSR-4 Autoloading** - No manual require statements
- ✅ **Versioning** - Lock to specific versions
- ✅ **Professional** - Published on Packagist
- ✅ **CI/CD Integration** - Easy to include in deployment pipelines

## Migration Guide

For existing users of the standalone SDK:

```markdown
### Migrating from Standalone to Composer Package

**Before (Standalone)**:
```php
require_once 'sdk/php/KycClient.php';
use DataFabric\SDK\KycClient;
```

**After (Composer)**:
```php
require 'vendor/autoload.php';
use DataFabric\SDK\KycClient;
```

**Steps**:
1. Install package: `composer require hiroshiaki/datafabric-sdk-php`
2. Remove manual SDK files
3. Update require statement to use Composer autoload
4. Code remains the same - API is identical!
```

## Documentation Links to Update

Update all references to the PHP SDK:

- Main project README
- SDK README
- API documentation
- Getting started guides
- Example files
- Integration tutorials

Replace references to `sdk/php/KycClient.php` with instructions to install via Composer.
