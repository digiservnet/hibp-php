# PHP library for Have I Been Pwned and Pwned Passwords.

[![Latest Stable Version](https://poser.pugx.org/icawebdesign/hibp-php/version)](https://packagist.org/packages/icawebdesign/hibp-php)
[![Total Downloads](https://poser.pugx.org/icawebdesign/hibp-php/downloads)](https://packagist.org/packages/icawebdesign/hibp-php)
[![codecov](https://codecov.io/gh/digiservnet/hibp-php/branch/develop/graph/badge.svg?token=H7GAEEFXLP)](https://codecov.io/gh/digiservnet/hibp-php)
[![License](https://poser.pugx.org/icawebdesign/hibp-php/license)](https://packagist.org/packages/icawebdesign/hibp-php)

HIBP-PHP is a composer library for accessing the [Have I Been Pwned](https://haveibeenpwned.com) and Pwned Passwords APIs (currently v3).

The HIBP API now requires an [API Key](https://haveibeenpwned.com/API/Key) that needs to be purchased at the HIBP site for any lookups that use an email address. This currently means that if you're only using this package for lookups from the PwnedPassword section of the API, then an API key isn't required.

Version `5.x` has dropped support for older PHP versions (`< 7.4`). If you still need a version of this package to run on an older PHP version, then please use the `icawebdesign/hibp-php:^4.0` tag, though the `4.x` branch will no longer receive updates. 

Version `6.x` now requires PHP `8.1+`. If you need to support previous versions of PHP, please use the `icawebdesign/hibp-php:^5.0` tag. This version however, will only receive security fixes.

## Requirements

- PHP 8.1+

## Installation
```bash
composer require icawebdesign/hibp-php:"^6.0"
```

## ReadOnly properties
Now that we're targeting a minimum of PHP `8.1`, this gives us the ability to utilise ReadOnly properties in objects. With this, entity getters have been removed and properties can now be accessed directly on the object.

## Usage examples for Breach Sites data

### Get all breach sites
```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$breachSites = $breach->getAllBreachSites();
```

This will return a `Collection` of `BreachSiteEntity` objects.

Or we can filter for a domain the breach was listed in:

```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$breachSites = $breach->getAllBreachSites('adobe.com');
```

This will return a `Collection` of `BreachSiteEntity` objects.

### Get single breach site
```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$breachSite = $breach->getBreach('adobe');
```

This will return a single `BreachSiteEntity`.

### Get list of data classes for breach sites
```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$dataClasses = $breach->getAllDataClasses();
```

This will return an `array` of Data Classes, eg;
```php
[
  "Account balances",
  "Address book contacts",
  "Age groups",
  "Ages",
  ...
]
```

### Get data for a breached email account
```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$data = $breach->getBreachedAccount('test@example.com');
```

We can retrieve unverified accounts too by specifying `true` for the second param (not retrieved by default):

```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$data = $breach->getBreachedAccount('test@example.com', includeUnverified: true);
```

We can also filter results back to a specific breached domain by adding a domain as the 3rd param

```php
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;

$breach = new Breach(new HibpHttp($apiKey));
$data = $breach->getBreachedAccount(
    'test@example.com', 
    includeUnverified: true,
    domainFilter: 'adobe.com', 
);
```

These calls will return a `Collection` of `BreachSiteEntity` objects.

## Usage examples for Pwned Passwords

The `PwnedPasswd` methods can now take a second param of an `array` to specify [GuzzleHttp request options](https://docs.guzzlephp.org/en/stable/request-options.html).

### Get number of times the start of a hash appears in the system matching against a full hash
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$count = $pwnedPassword->rangeFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

This will return an `int` of the count.

You can also check against `NTLM` hashes:
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$count = $pwnedPassword->ntlmRangeFromHash('8846F7EAEE8FB117AD06BDD830B7586C');
```

### Get number of times the start of a hash appears in the system as above, but with padded values to help prevent fingerprinting
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$hashData = $pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

You can also check against `NTLM` hashes:
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$hashData = $pwnedPassword->paddedNtlmRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

This will return a `Collection` of `PwnedPassword` model objects.

### Get a collection of hash data from a start of a hash and matching against a full hash
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$hashData = $pwnedPassword->rangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

This will return a `Collection` of `PwnedPassword` model objects.

### Get a collection of hash data from a start of a hash and matching against a full hash as above, but with padded values to help prevent fingerprinting
```php
use Icawebdesign\Hibp\Password\PwnedPassword;
use Icawebdesign\Hibp\HibpHttp;

$pwnedPassword = new PwnedPassword(new HibpHttp($apiKey));
$hashData = $pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

// Strip padded values from results
$hashData = PwnedPassword::stripZeroMatchesData($hashData, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

This will return a `Collection` of `PwnedPassword` model objects.

## Usage examples for Paste lists

### Get a collection of pastes that a specified email account has appeared in
```php
use Icawebdesign\Hibp\Paste\Paste;
use Icawebdesign\Hibp\HibpHttp;

$paste = new Paste(new HibpHttp($apiKey));
$data = $paste->lookup('test@example.com');
```

This will return a `Collection` of `PasteEntity` objects.

## Laravel specifics
If using the package within a Laravel application, you can use the provided facades.
First, you need to add your HIBP API key to your `.env` file, or your preferred method for adding values to your server environment variables.

```
HIBP_API_KEY=abcdefgh123456789
```

You can then use the facades to call the relevant methods:

```php
// Breach
use Icawebdesign\Hibp\Facades\Breach;

$breachSites = Breach::getAllBreachSites();

// Paste
use Icawebdesign\Hibp\Facades\Paste;

$paste = Paste::lookup('test@example.com');

// PwnedPassword
use Icawebdesign\Hibp\Facades\PwnedPassword;

$count = PwnedPassword::rangeFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ian.h@digiserv.net instead of using the issue tracker.

## Credits

- [Ian.H](https://github.com/icawebdesign)
- [All Contributors](../../contributors)

Thank you to [Artem Fomenko](https://github.com/ArtemFo) for being the first external contributor to the package 
providing request options for Guzzle for the PwnedPassword methods.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
