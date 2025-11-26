# GitLab CI/CD Setup Guide

This guide explains how the automated versioning and Packagist publishing works in this project.

## Overview

The GitLab CI/CD pipeline automatically:
1. **Tests** every commit (validation, PHPStan, code style, unit tests)
2. **Creates version tags** based on commit message prefixes
3. **Notifies Packagist** when new tags are created

## Pipeline Stages

### 1. Test Stage (Every Commit)

Runs on all branches and merge requests:
- `composer validate --strict` - Validates composer.json
- `composer phpstan` - Static analysis at max level
- `composer cs-check` - PSR-12 code style check
- `composer test` - PHPUnit tests

### 2. Tag Stage (Main Branch Only)

Automatically creates version tags when commits to `main`/`master` branch contain semantic prefixes:

| Commit Prefix | Version Bump | Example |
|---------------|--------------|---------|
| `breaking:` or `major:` | Major version (x.0.0) | `breaking: remove deprecated methods` |
| `feat:` or `feature:` or `minor:` | Minor version (0.x.0) | `feat: add new validation method` |
| `fix:` or `patch:` or `chore:` or `docs:` | Patch version (0.0.x) | `fix: correct response parsing` |

**Examples:**

```bash
# Patch version bump (v1.0.0 → v1.0.1)
git commit -m "fix: handle null API responses correctly"

# Minor version bump (v1.0.1 → v1.1.0)
git commit -m "feat: add webhook signature verification"

# Major version bump (v1.1.0 → v2.0.0)
git commit -m "breaking: change method signatures for better type safety"
```

### 3. Publish Stage (On Tags Only)

When a tag is created:
- Notifies Packagist to update the package
- Requires `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` variables

## Setup Instructions

### Step 1: Configure GitLab CI/CD Variables

Go to: **GitLab Project → Settings → CI/CD → Variables**

Add the following variables:

| Variable | Type | Value | Protected | Masked |
|----------|------|-------|-----------|--------|
| `PACKAGIST_USERNAME` | Variable | Your Packagist username | ✅ Yes | ❌ No |
| `PACKAGIST_API_TOKEN` | Variable | Your Packagist API token | ✅ Yes | ✅ Yes |

**Get your Packagist API token:**
1. Go to https://packagist.org/profile/
2. Click "Show API Token" or create a new one
3. Copy the token and add it to GitLab variables

### Step 2: Initial Setup

1. **Push the repository to GitLab:**
   ```bash
   git remote add origin https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php.git
   git push -u origin main
   ```

2. **Create the first tag manually:**
   ```bash
   git tag -a v1.0.0 -m "Release version 1.0.0"
   git push origin v1.0.0
   ```

3. **Submit to Packagist:**
   - Go to https://packagist.org/packages/submit
   - Enter repository URL: `https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php`
   - Click "Check" and then "Submit"

### Step 3: Verify Pipeline

After pushing, check:
1. **GitLab → CI/CD → Pipelines** - Should show passing pipeline
2. **GitLab → Repository → Tags** - Should show created tags
3. **Packagist → Your Package** - Should show updated version

## Usage Workflows

### Automatic Workflow (Recommended)

1. **Make changes and commit with semantic prefix:**
   ```bash
   git add .
   git commit -m "feat: add support for batch KYC checks"
   git push origin main
   ```

2. **CI automatically:**
   - Runs all tests
   - Creates new tag (e.g., v1.1.0)
   - Notifies Packagist
   - Package is available within minutes

### Manual Tag Creation

If you need to create a tag manually, use the `manual-tag` job:

1. Go to **GitLab → CI/CD → Pipelines**
2. Click on the latest pipeline
3. Find the `manual-tag` job and click ▶️ Play
4. Set variable `TAG_VERSION` (e.g., `v1.2.3`)
5. Click "Run job"

## Commit Message Best Practices

### Good Commit Messages

```bash
# Patch bump - bug fixes
fix: correct JSON parsing for empty responses
fix: handle timeout errors gracefully
patch: update dependencies to latest versions

# Minor bump - new features
feat: add webhook signature verification
feature: support custom HTTP headers
minor: add response caching

# Major bump - breaking changes
breaking: remove deprecated createCheck() method
major: change constructor parameters

# Documentation and maintenance (patch bump)
docs: update API documentation
chore: update dev dependencies
style: fix code formatting
refactor: simplify error handling
test: add integration tests
```

### Bad Commit Messages (Won't Trigger Auto-Tagging)

```bash
# No semantic prefix - no tag created
git commit -m "update code"
git commit -m "changes"
git commit -m "working on feature"
```

## Version Strategy

This package follows [Semantic Versioning 2.0.0](https://semver.org/):

- **MAJOR** (x.0.0) - Breaking changes, incompatible API changes
- **MINOR** (0.x.0) - New features, backward compatible
- **PATCH** (0.0.x) - Bug fixes, backward compatible

## Troubleshooting

### Pipeline Fails on Tag Creation

**Problem:** "Permission denied" or "Could not push tag"

**Solution:** Ensure CI/CD has permission to push:
1. Go to **Settings → Repository → Protected tags**
2. Allow "Maintainers" to create tags
3. Or unprotect tag pattern `v*`

### Packagist Not Updating

**Problem:** "Failed to notify Packagist"

**Solution:**
1. Verify `PACKAGIST_USERNAME` and `PACKAGIST_API_TOKEN` are set correctly
2. Check token hasn't expired at https://packagist.org/profile/
3. Ensure package is submitted to Packagist first
4. Check Packagist API status

### No Tag Created After Commit

**Problem:** Pushed to main but no tag was created

**Solution:**
1. Check commit message has correct prefix (`feat:`, `fix:`, etc.)
2. Verify pipeline ran (check CI/CD → Pipelines)
3. Check pipeline logs for `auto-tag` job
4. Ensure pushing to `main` or `master` branch

### Tests Failing

**Problem:** Pipeline fails at test stage

**Solution:**
1. Run tests locally: `composer test`
2. Run quality checks: `composer phpstan && composer cs-check`
3. Fix issues before pushing
4. Push again to trigger new pipeline

## Pipeline Status Badge

Add to your README.md:

```markdown
[![Pipeline Status](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/badges/main/pipeline.svg)](https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php/-/commits/main)
```

## Manual Packagist Update

If you need to manually trigger a Packagist update:

```bash
curl -X POST "https://packagist.org/api/update-package?username=YOUR_USERNAME&apiToken=YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"repository":{"url":"https://gitlab.hiroshiaki.com:8886/hiroshi-aki/datafabric-sdk-php"}}'
```

## Advanced Configuration

### Custom Version Rules

Edit `.gitlab-ci.yml` to customize version bump rules:

```yaml
# Add custom patterns
if echo "$COMMIT_MSG" | grep -qiE "^hotfix:"; then
  PATCH=$((PATCH + 1))
  VERSION_TYPE="patch (hotfix)"
fi
```

### Skip CI/CD

Add `[skip ci]` or `[ci skip]` to commit message:

```bash
git commit -m "docs: update README [skip ci]"
```

### Test on Specific PHP Versions

Modify `.gitlab-ci.yml` to test multiple PHP versions:

```yaml
test:
  parallel:
    matrix:
      - PHP_VERSION: ["8.0", "8.1", "8.2", "8.3"]
  image: php:${PHP_VERSION}-cli
```

## See Also

- [Publishing Guide](PUBLISHING.md) - Manual publishing process
- [Setup Summary](SETUP_SUMMARY.md) - Package development setup
- [Getting Started](GETTING_STARTED.md) - Using the package
