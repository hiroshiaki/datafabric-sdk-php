# Publishing to Packagist

This guide will help you publish the `hiroshiaki/datafabric-sdk-php` package to Packagist.

## Prerequisites

- GitLab repository with the package code
- Packagist account (https://packagist.org/register/)
- Repository must be publicly accessible or have proper access configured

## Step 1: Prepare the Repository

Ensure all required files are in place:

- ✅ `composer.json` with proper package name
- ✅ `README.md` with installation and usage instructions
- ✅ `LICENSE` file
- ✅ `CHANGELOG.md` for version history
- ✅ Source code in `src/` following PSR-4
- ✅ `.gitignore` excluding vendor and other generated files

## Step 2: Create a Git Tag

Create an initial release tag:

```bash
cd /Users/rahmanazhar/Documents/Laravel/datafabric-sdk-php
git add .
git commit -m "Initial release v1.0.0"
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin main
git push origin v1.0.0
```

## Step 3: Submit to Packagist

1. Go to https://packagist.org/packages/submit
2. Log in or create an account
3. Enter your repository URL:
   ```
   https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php.git
   ```
4. Click "Check"
5. If validation passes, click "Submit"

## Step 4: Configure Auto-Update Webhook

### For GitLab (IMPORTANT - Required for Auto-Updates)

#### Option A: Built-in Packagist Integration (Recommended)

1. Go to your GitLab project: **Settings → Integrations**
2. Look for "Packagist" in the integrations list
3. If available, configure:
   - **Username**: Your Packagist username
   - **API Token**: Get from https://packagist.org/profile/
   - Enable triggers: ✅ Push events, ✅ Tag push events
4. Click "Save changes"
5. Test the integration using the "Test" button

#### Option B: Manual Webhook Setup

If the built-in integration isn't available:

1. Go to: **Settings → Webhooks** (or Settings → Integrations → Add new webhook)
2. Configure:
   - **URL**: `https://packagist.org/api/update-package?username=YOUR_PACKAGIST_USERNAME&apiToken=YOUR_API_TOKEN`
   - **Trigger**: ✅ Push events, ✅ Tag push events
   - **Enable SSL verification**: ✅ Enabled
3. Click "Add webhook"
4. Test by clicking "Test" → "Push events"

**Get your API token**: https://packagist.org/profile/ → "Show API Token"

**Example URL**:
```
https://packagist.org/api/update-package?username=johndoe&apiToken=abc123def456
```

### Verify Webhook Works

After setup, test it:

```bash
# Make a small change
echo "# Test update" >> README.md
git add README.md
git commit -m "test: webhook trigger"
git push origin main
```

Then check:
1. GitLab: Settings → Webhooks → Recent Deliveries (should show 200 OK)
2. Packagist: Your package page should update within 1-2 minutes

**Without this webhook, you must manually update on Packagist after each push!**

## Step 5: Verify Installation

Test that your package can be installed:

```bash
# In a new directory
composer require hiroshiaki/datafabric-sdk-php

# Or with specific version
composer require hiroshiaki/datafabric-sdk-php:^1.0
```

## Step 6: Update Package Description (Optional)

On Packagist:
1. Go to your package page
2. Click "Edit" 
3. Update description, keywords, and links
4. Save changes

## Releasing New Versions

When you have new features or fixes:

```bash
# Update CHANGELOG.md with changes

# Update version in composer.json (optional, can use git tags)

# Commit changes
git add .
git commit -m "Release v1.1.0 - Add new features"

# Create new tag
git tag -a v1.1.0 -m "Release version 1.1.0"

# Push to GitLab
git push origin main
git push origin v1.1.0

# Packagist will auto-update via webhook
# Or manually trigger update on packagist.org
```

## Version Naming Convention

Follow Semantic Versioning (https://semver.org/):

- **1.0.0** - Initial release
- **1.0.1** - Bug fixes (patch)
- **1.1.0** - New features, backward compatible (minor)
- **2.0.0** - Breaking changes (major)

Tag examples:
```bash
git tag v1.0.0  # Initial release
git tag v1.0.1  # Bug fix
git tag v1.1.0  # New feature
git tag v2.0.0  # Breaking changes
```

## Maintaining the Package

### Regular Tasks

1. **Monitor Issues**: Respond to user issues on GitLab
2. **Review PRs**: Review and merge pull requests
3. **Update Dependencies**: Keep GuzzleHTTP and dev dependencies updated
4. **Security**: Monitor and fix security vulnerabilities
5. **Documentation**: Keep README and docs up-to-date

### Updating Dependencies

```bash
# Check for outdated packages
composer outdated

# Update dependencies
composer update

# Test after updates
composer test
composer phpstan
composer cs-check
```

### Deprecation Process

When deprecating features:

1. Mark as deprecated in code and docs
2. Announce in CHANGELOG
3. Keep for at least one major version
4. Remove in next major version

Example:
```php
/**
 * @deprecated Will be removed in v2.0. Use newMethod() instead.
 */
public function oldMethod() { }
```

## Troubleshooting

### Package Not Found

- Check repository URL is correct
- Ensure repository is public or accessible
- Verify composer.json is valid: `composer validate`
- Wait a few minutes for Packagist to index

### Webhook Not Working

**Check webhook configuration:**
- Verify webhook URL includes your correct Packagist username and API token
- Ensure webhook is enabled in GitLab (Settings → Webhooks)
- Check webhook Recent Deliveries in GitLab for error messages
- Verify triggers are enabled: Push events and Tag push events
- Confirm SSL verification is enabled

**Test the webhook:**
```bash
# In GitLab: Settings → Webhooks → Click "Test" → "Push events"
# Should return 200 OK
```

**Check Packagist status:**
- Go to your package page on Packagist
- Look for last update timestamp
- Manually trigger: Click "Update" button if webhook failed

**Common issues:**
- API token expired: Generate new token on Packagist
- Wrong username: Must match your Packagist account exactly
- Network/firewall blocking GitLab → Packagist communication
- Repository not public: Ensure GitLab repo is accessible

### Invalid composer.json

```bash
# Validate locally
composer validate --strict

# Fix common issues:
# - Package name must be lowercase
# - Package name format: vendor/package
# - Valid license identifier
# - Proper PSR-4 autoloading
```

## Support

- **Packagist Docs**: https://packagist.org/about
- **Composer Docs**: https://getcomposer.org/doc/
- **Issues**: https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/issues

## Checklist

Before publishing:

- [ ] Repository is clean and organized
- [ ] All tests pass (`composer test`)
- [ ] Code style is correct (`composer cs-check`)
- [ ] Static analysis passes (`composer phpstan`)
- [ ] README is comprehensive
- [ ] CHANGELOG is updated
- [ ] Version is tagged
- [ ] composer.json is valid
- [ ] LICENSE file exists
- [ ] Examples are working

After publishing:

- [ ] Package appears on Packagist
- [ ] Can be installed via Composer
- [ ] Webhook is configured
- [ ] Documentation is clear
- [ ] Version badge works
