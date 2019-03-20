# PHP library for Have I Been Pwned and Pwned Passwords.

[![pipeline status](https://gitlab.2up.io/ian/hibp-php/badges/master/pipeline.svg)](https://gitlab.2up.io/ian/hibp-php/commits/master) [![coverage report](https://gitlab.2up.io/ian/hibp-php/badges/master/coverage.svg)](https://gitlab.2up.io/ian/hibp-php/commits/master)

HIBP-PHP is a composer library for accessing the [Have I Been Pwned](https://haveibeenpwned.com) and [Pwned Passwords](https://pwnedpassword.com) APIs.

## Requirements

* PHP 7.1.3+

## Installation


```bash
composer require icawebdesign/hibp-php
```

## Usage examples for Breach Sites data

### Get all breach sites

```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach();
$breachSites = $breach->getAllBreachSites();
```

### Get single breach site

```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach();
$breachSite = $breach->getBreach('adobe');
```

### Get list of data classes for breach sites
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach();
$dataClasses = $breach->getAllDataClasses();
```

### Get data for a breached email account
```php
use Icawebdesign\Hibp\Breach;

$breach = new Breach();
$data = $breach->getBreachedAccount('test@example.com');
```

## Usage examples for Pwned Passwords

### Get number of times the start of a hash appears in the system matching against a full hash
```php
use Icawebdesign\Hibp\PwnedPassword;

$pwnedPassword = new PwnedPassword();
$count = $pwnedPassword->range(
    '5baa6',
    '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'
);
```

### Get a collection of hash data from a start of a hash and matching against a full hash
```php
use Icawebdesign\Hibp\PwnedPassword;

$pwnedPassword = new PwnedPassword();
$hashData = $pwnedPassword->rangeData(
    '5baa6',
    '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'
);
```

## Usage examples for Paste lists

### Get a collection of pastes that a specified email account has appeared in
```php
use Icawebdesign\Hibp\Paste;

$paste = new Paste();
$data = $paste->lookup('test@example.com');
```

## Laravel specifics
If using the package within a Laravel application, you can simply use the provided facades, eg:
```php
// Breach
$breachSites = Breach::getAllBreachSites();

// Paste
$paste = Paste::lookup('test@example.com');

// PwnedPassword
$count = PwnedPassword::range(
    '5baa6',
    '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'
);
```
