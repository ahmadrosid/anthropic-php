# Troubleshooting Guide

## Common Issues

### 404 Not Found Error

**Error Message:**
```
GuzzleHttp\Exception\ClientException: Client error: POST https://api.anthropic.com/v1/messages resulted in a 404 Not Found response
```

**Cause:**
This error occurs when the required `anthropic-version` header is missing from your request.

**Solution:**
Make sure to include the `anthropic-version` header when creating your client:

```php
use Anthropic\Anthropic;

$client = Anthropic::factory()
    ->withHeaders([
        'anthropic-version' => '2023-06-01',  // ✅ Required!
        'content-type' => 'application/json',
        'x-api-key' => env('ANTHROPIC_API_KEY')
    ])
    ->make();
```

**What NOT to do:**
```php
// ❌ This will cause a 404 error
$client = Anthropic::factory()
    ->withHeaders([
        'content-type' => 'application/json',
        'x-api-key' => env('ANTHROPIC_API_KEY')
        // Missing 'anthropic-version' header!
    ])
    ->make();
```

**Validation:**
As of the latest version, the library will throw a helpful `InvalidArgumentException` if you forget to include the required headers:

```
InvalidArgumentException: Missing required header: anthropic-version.
This header is required by the Anthropic API.
Use ->withHeader("anthropic-version", "2023-06-01") or include it in withHeaders().
```

### Invalid API Key

**Error Message:**
```
401 Unauthorized
```

**Cause:**
Your API key is missing or invalid.

**Solution:**
1. Get your API key from the [Anthropic Console](https://console.anthropic.com/)
2. Make sure it's correctly set in your environment or passed to the client:

```php
$client = Anthropic::factory()
    ->withHeaders([
        'anthropic-version' => '2023-06-01',
        'content-type' => 'application/json',
        'x-api-key' => 'sk-ant-...'  // Your actual API key
    ])
    ->make();
```

### Available API Versions

The current supported `anthropic-version` values:
- `2023-06-01` (recommended)

Check the [Anthropic API documentation](https://docs.anthropic.com/en/api/versioning) for the latest version information.

## Testing the 404 Error Fix

You can run the reproduction test to verify the fix:

```bash
vendor/bin/phpunit tests/Integration/Bug404ReproductionTest.php
```

This test demonstrates:
1. How missing headers cause a 404 error
2. How including all required headers fixes the issue
3. Validation of header requirements