# Laravel FinanzOnline Webservices

[![Latest Version on Packagist](https://img.shields.io/packagist/v/csoellinger/php-laravel-fon-ws.svg?style=flat-square)](https://packagist.org/packages/csoellinger/php-laravel-fon-ws)
[![Total Downloads](https://img.shields.io/packagist/dt/csoellinger/php-laravel-fon-ws.svg?style=flat-square)](https://packagist.org/packages/csoellinger/php-laravel-fon-ws)

A Laravel package providing seamless integration with the Austrian FinanzOnline (FON) web services. This package wraps the [php-fon-webservices](https://github.com/CSoellinger/php-fon-webservices) library with Laravel-specific features like service container bindings, facades, and artisan commands.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Usage Examples](#usage-examples)
- [Configuration](#configuration)
- [Documentation](#documentation)
- [Development Environment](#development-environment)
- [Testing](#testing)
- [Related Packages](#related-packages)

## Features

- 🔐 **Session Management** - Automatic authentication handling
- ✅ **VAT ID Validation** - Check Austrian VAT IDs with two detail levels
- 📥 **Databox Download** - Retrieve tax documents from your digital mailbox
- 📤 **File Upload** - Submit documents to FinanzOnline
- 🏦 **Bank Data Transmission** - Exchange financial data
- 📊 **Query Data Transmission** - Submit information requests
- 🎯 **Dependency Injection** - Full Laravel container integration
- 🎨 **Facades** - Convenient static interface to services
- 🛠️ **Artisan Commands** - CLI tools for common operations
- ✨ **Laravel 12 Ready** - Full support for the latest Laravel version

## Requirements

- PHP 8.1, 8.2, 8.3, or 8.4
- Laravel 11.x or 12.x
- PHP SOAP extension enabled

> **📦 Multiple PHP Versions:** This package is tested against PHP 8.1-8.4. See [PHP_VERSIONS.md](PHP_VERSIONS.md) for testing with different versions locally.

## Installation

You can install the package via composer:

```bash
composer require csoellinger/php-laravel-fon-ws
```

### Publish Configuration

Publish the configuration file to customize the package:

```bash
php artisan vendor:publish --provider="CSoellinger\Laravel\FonWebservices\FonWebservicesServiceProvider" --tag="fon-webservices-config"
```

This will create a `config/fon-webservices.php` file in your application.

### Configuration

The package reads credentials from the `config/fon-webservices.php` file. By default, it uses environment variables, but you can configure credentials from **any source** (database, cache, config files, etc.).

**Option 1: Using Environment Variables** (recommended for local development)

Add to your `.env` file:

```env
FON_TE_ID=your_teilnehmer_id
FON_TE_UID=your_teilnehmer_uid
FON_BEN_ID=your_benutzer_id
FON_BEN_PIN=your_benutzer_pin
```

**Option 2: From Database** (recommended for production)

Edit `config/fon-webservices.php`:

```php
'credentials' => [
    'te_id' => DB::table('settings')->value('fon_te_id'),
    'te_uid' => DB::table('settings')->value('fon_te_uid'),
    'ben_id' => DB::table('settings')->value('fon_ben_id'),
    'ben_pin' => decrypt(DB::table('settings')->value('fon_ben_pin')),
],
```

**Option 3: From Cache or Other Sources**

```php
'credentials' => [
    'te_id' => Cache::get('fon_credentials')['te_id'],
    // ... or any other source
],
```

> **⚠️ Security Warning:** Never commit credentials to version control! Store sensitive data encrypted in your database or use Laravel's encryption features.

## Usage

### Dependency Injection

The recommended way to use the services is through dependency injection:

```php
use CSoellinger\FonWebservices\VatIdCheckWs;
use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;

class InvoiceController extends Controller
{
    public function __construct(
        private VatIdCheckWs $vatIdCheck
    ) {}

    public function store(Request $request)
    {
        $result = $this->vatIdCheck->check(
            $request->input('vat_id'),
            VatIdCheckLevel::ExtendedCheck
        );

        if ($result->valid) {
            // VAT ID is valid - process invoice
        }
    }
}
```

### Using Facades

For quick operations, you can use the provided facades:

```php
use CSoellinger\Laravel\FonWebservices\Facades\FonVatIdCheck;
use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;

$result = FonVatIdCheck::check('ATU12345678', VatIdCheckLevel::SimpleCheck);

if ($result instanceof \CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne) {
    echo "Company: {$result->name}";
}
```

### Available Facades

- `FonSession` - Session management
- `FonVatIdCheck` - VAT ID validation
- `FonDataboxDownload` - Databox operations
- `FonFileUpload` - File upload operations
- `FonBankDataTransmission` - Bank data exchange
- `FonQueryDataTransmission` - Query submissions

### Artisan Commands

#### Check VAT ID

```bash
# Simple check (level 1)
php artisan fon:check-vat ATU12345678

# Extended check (level 2) with address information
php artisan fon:check-vat ATU12345678 --level=2

# JSON output
php artisan fon:check-vat ATU12345678 --json
```

#### List Databox Items

```bash
# List all items
php artisan fon:list-databox

# Filter by type
php artisan fon:list-databox --type=Veranlagung

# Filter by date range
php artisan fon:list-databox --from=2024-01-01 --to=2024-12-31

# JSON output
php artisan fon:list-databox --json
```

## Usage Examples

### In Controllers

```php
use CSoellinger\FonWebservices\VatIdCheckWs;
use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;

class VatController extends Controller
{
    public function __construct(
        private VatIdCheckWs $vatIdCheck
    ) {}

    public function check(Request $request)
    {
        $result = $this->vatIdCheck->check(
            $request->input('vat_id'),
            VatIdCheckLevel::ExtendedCheck
        );

        if ($result instanceof \CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo) {
            return response()->json([
                'valid' => true,
                'name' => $result->name,
                'address' => [
                    'street' => $result->street,
                    'zip' => $result->zip,
                    'city' => $result->city,
                ],
            ]);
        }

        return response()->json(['valid' => false]);
    }
}
```

### In Jobs

```php
use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\Enum\DataboxType;

class ProcessDataboxItems implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue;

    public function handle(DataboxDownloadWs $databox): void
    {
        $from = new \DateTime('-7 days');
        $to = new \DateTime();

        $items = $databox->get(
            type: DataboxType::All,
            from: $from,
            to: $to
        );

        foreach ($items as $item) {
            // Process each databox item
            $content = $databox->getEntry($item->applkey);
            // Store or process the document...
        }
    }
}
```

### In API Routes

```php
use Illuminate\Support\Facades\Route;
use CSoellinger\Laravel\FonWebservices\Facades\FonVatIdCheck;

Route::get('/api/vat/check/{vatId}', function (string $vatId) {
    $result = FonVatIdCheck::check($vatId);

    return response()->json([
        'vat_id' => $vatId,
        'valid' => $result->valid,
    ]);
});
```

### In Livewire Components

```php
use Livewire\Component;
use CSoellinger\FonWebservices\VatIdCheckWs;

class VatChecker extends Component
{
    public string $vatId = '';
    public ?bool $isValid = null;

    public function checkVat(VatIdCheckWs $vatIdCheck)
    {
        $result = $vatIdCheck->check($this->vatId);
        $this->isValid = $result->valid;
    }

    public function render()
    {
        return view('livewire.vat-checker');
    }
}
```

### Session Management

Session management is handled automatically - services will auto-login when needed. For manual session control:

```php
use CSoellinger\FonWebservices\SessionWs;

class CustomService
{
    public function __construct(
        private SessionWs $session
    ) {}

    public function performOperations(): void
    {
        // Manually login
        $loginResponse = $this->session->login();

        // Perform operations...

        // Manually logout
        $this->session->logout();
    }
}
```

## Configuration

The `config/fon-webservices.php` file allows you to customize:

- **Credentials** - FinanzOnline authentication details
- **SOAP Options** - Configure SOAP client behavior
- **Service Bindings** - Enable/disable specific services

### SOAP Options

These options are passed directly to PHP's [SoapClient constructor](https://www.php.net/manual/en/soapclient.construct.php) and control the underlying SOAP behavior:

```php
'soap_options' => [
    'trace' => env('FON_SOAP_TRACE', false),           // Enable request/response tracing (debugging)
    'exceptions' => env('FON_SOAP_EXCEPTIONS', true),  // Throw exceptions on SOAP errors
    'connection_timeout' => env('FON_SOAP_TIMEOUT', 30), // Connection timeout in seconds
    'cache_wsdl' => env('FON_SOAP_CACHE_WSDL', WSDL_CACHE_DISK), // WSDL caching mode
],
```

You can add any [valid SoapClient option](https://www.php.net/manual/en/soapclient.construct.php) here. Common options include `compression`, `user_agent`, `proxy_host`, etc.

### Disable Unused Services

To reduce memory usage, you can disable services you don't use:

```php
'services' => [
    'session' => true,
    'vat_id_check' => true,
    'databox_download' => false,  // Disabled
    'file_upload' => false,       // Disabled
    'bank_data_transmission' => false,
    'query_data_transmission' => false,
],
```

## Documentation

For the base class API documentation go to https://csoellinger.github.io/php-fon-webservices/api/

Also check out repo from the base class: https://github.com/CSoellinger/php-fon-webservices

## Development Environment

This package uses [Laravel Sail](https://laravel.com/docs/sail) for local development with Docker or Podman.

### Quick Start

```bash
# Clone and setup
git clone https://github.com/csoellinger/php-laravel-fon-ws.git
cd php-laravel-fon-ws
cp .env.example .env

# Add your FON credentials to .env (for local testing)
# Or configure them in config/fon-webservices.php from any source

# Install dependencies and start
composer install
./vendor/bin/sail up -d

# Run tests
./vendor/bin/sail composer test
```

### Testing with Different PHP Versions

```bash
# Test against PHP 8.1, 8.2, 8.3, or 8.4
PHP_VERSION=8.1 ./vendor/bin/sail up -d
```

## Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse

# Format code
composer format
```

## Related Packages

- [csoellinger/php-fon-webservices](https://github.com/CSoellinger/php-fon-webservices) - Core PHP library (framework-agnostic)
