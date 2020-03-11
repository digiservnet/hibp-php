# PHP library for Have I Been Pwned and Pwned Passwords.

[![Latest Stable Version](https://poser.pugx.org/icawebdesign/hibp-php/version)](https://packagist.org/packages/icawebdesign/hibp-php)
[![Total Downloads](https://poser.pugx.org/icawebdesign/hibp-php/downloads)](https://packagist.org/packages/icawebdesign/hibp-php)
[![License](https://poser.pugx.org/icawebdesign/hibp-php/license)](https://packagist.org/packages/icawebdesign/hibp-php)

HIBP-PHP is a composer library for accessing the [Have I Been Pwned](https://haveibeenpwned.com) and Pwned Passwords APIs (currently v3).

The HIBP API now requires an [API Key](https://haveibeenpwned.com/API/Key) that needs to be purchased at the HIBP site for any lookups that use an email address. This currently means that if you're only using this package for lookups from the PwnedPassword section of the API, then an API key isn't required.

The minimum PHP version for this package is now **7.2.0**.

## Requirements

* PHP 7.2.0+

## Installation
```bash
composer require icawebdesign/hibp-php
```

## Usage examples for Breach Sites data

### Get all breach sites
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$breachSites = $breach->getAllBreachSites();
```

Or we can filter for a domain the breach was listed in:

```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$breachSites = $breach->getAllBreachSites('adobe.com');
```

### Get single breach site
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$breachSite = $breach->getBreach('adobe');
```

### Get list of data classes for breach sites
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$dataClasses = $breach->getAllDataClasses();
```

### Get data for a breached email account
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$data = $breach->getBreachedAccount('test@example.com');
```

We can retrieve unverified accounts too by specifying `true` for the second param (not retrieved by default):

```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$data = $breach->getBreachedAccount('test@example.com', true);
```

We can also filter results back to a specific breached domain by adding a domain as the 3rd param

```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach($apiKey);
$data = $breach->getBreachedAccount('test@example.com', true, 'adobe.com');
```

## Usage examples for Pwned Passwords

### Get number of times the start of a hash appears in the system matching against a full hash
```php
use Icawebdesign\Hibp\Password;

$pwnedPassword = new PwnedPassword();
$count = $pwnedPassword->rangeFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

### Get number of times the start of a hash appears in the system as above, but with padded values to help prevent fingerprinting
```php
use Icawebdesign\Hibp\Password;

$pwnedPassword = new PwnedPassword();
$hashData = $pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

### Get a collection of hash data from a start of a hash and matching against a full hash
```php
use Icawebdesign\Hibp\PwnedPassword;

$pwnedPassword = new PwnedPassword();
$hashData = $pwnedPassword->rangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

### Get a collection of hash data from a start of a hash and matching against a full hash as above, but with padded values to help prevent fingerprinting
```php
use Icawebdesign\Hibp\PwnedPassword;

$pwnedPassword = new PwnedPassword();
$hashData = $pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

// Strip padded values from results
$hashData = PwnedPassword::stripZeroMatchesData($hashData, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

## Usage examples for Paste lists

### Get a collection of pastes that a specified email account has appeared in
```php
use Icawebdesign\Hibp\Paste;

$paste = new Paste($apiKey);
$data = $paste->lookup('test@example.com');
```

## Laravel specifics
If using the package within a Laravel application, you can use the provided facades.
First, you need to add your HIBP API key to your `.env` file, or your preferred method for adding values to your server environment variables.

```
HIBP_API_KEY=abcdefgh123456789
```

You can then use the facades to call the relevant methods:

```php
// Breach
$breachSites = Breach::getAllBreachSites();

// Paste
$paste = Paste::lookup('test@example.com');

// PwnedPassword
$count = PwnedPassword::rangeFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email ian@ianh.io instead of using the issue tracker.

## Credits

- [Ian.H](https://github.com/icawebdesign)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
