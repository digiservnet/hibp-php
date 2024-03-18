# HIBP-PHP Changelog

## [Unreleased]

## [6.4.0] - 2024-03-18

### Changed
- Update Illuminate/Collections for Laravel 11 - [[Jonas Drieghe](https://github.com/digiservnet/hibp-php/commits?author=jdrieghe)]
- Update dependencies

## [6.3.1] - 2023-10-28

### Added
- Add PHP 8.3 to `composer.json`.

## [6.3.0] - 2023-10-28

### Added
- Add `subscriptionFree` property to `BreachEntity`.
- Add `latestBreach()` method.

## [6.2.0] - 2023-06-20

### Added
- Add option to return NTLM hashes instead of SHA1 hashes.
- Add [Codecov coverage reporting](https://app.codecov.io/gh/digiservnet/hibp-php).

### Changed
- Minor update to Changelog formatting
- Transferred repo from https://github.com/icawebdesign/hibp-php to https://github.com/digiservnet/hibp-php

## [6.1.0] - 2023-04-18

### Fixed
- Fix request headers not being sent correctly.

## [5.3.0] - 2023-04-18

### Fixed
- Fix request headers not being sent correctly.

## [6.0.1] - 2023-02-16

### Changed
- Update [README](README.md) about ReadOnly properties.

## [6.0.0] - 2023-02-16

### Changed
- Refactor for minimum PHP 8.1 version
- Update dependencies and add dependency versions for Laravel 10
- Add more examples to [README](README.md) and include response type info
- All properties on entity objects are now public

## [5.2.0] - 2023-02-16

### Added
- Add malware flag to BreachSiteEntity

## [5.1.1] - 2022-10-03

### Changed
- Update contact info

## [5.1.0] - 2022-02-25

### Changed
- Update dependencies and add dependency versions for Laravel 9

## [5.0.6] - 2021-11-13

### Changed
- Update dependencies

## [5.0.5] - 2021-07-01

### Changed
- Updated dependencies to fix a security issue  
(https://github.com/advisories/GHSA-9f46-5r25-5wfm)

## [5.0.4] - 2021-05-07

### Changed
- Updated dependencies to fix a security issue

## [5.0.2] & *5.0.3*
- No code changes

## [5.0.1] - 2021-01-21

### Changed
- Updated Changelog format to be clearer on releases and items per release
- Updated dependencies

## [5.0.0] - 2021-01-16

### Added

- Guzzle Client options array for API consumption methods
- Mocked responses for test suite

### Removed
- Valid API key dependency to run test suite
- Dropped support for PHP <7.4

## [4.4.0]
  - Add options array to `Breaches` and `Pastes` methods to allow use of GuzzleHttp Client options

## [4.3.0]
- Add options array to `PwnedPassword` methods to allow use of GuzzleHttp Client options

## [4.2.2]
- Update package dependencies to resolve security issue in `symfony/http-kernel` package

## [4.2.1]
- Update package dependencies

## [4.2.0]
- Add `paddedRangeFromHash()`, `paddedRangeDataFromHash()` and `stripZeroMatchesData` methods to `PwnedPassword` class.
- Update package dependencies
- Add static analysis to CI pipeline
- Various internal code tidying

## [4.1.0]
- Update package dependencies
- Internal cody tidy up

## [4.0.0]
- Update to use HIBP API v3
- Remove deprecated `range()` and `rangeData()` methods from `PwnedPassword` class.

## [3.3.0]
- Add breach lookup params  
      Truncate responses to only return breach names  
      Filter results to specific domains  
      Include unverified results in responses

## [3.2.2]
- Update user-agent string

## [3.2.1]
- PHP CodeSniffer fixes

## [3.2.0]
- Add Laravel-specific classes for Service Providers and Facades

## [3.1.0]
- Update src directory structure and refactor unit tests for PHPUnit 8.x deprecations.

## [3.0.0]
- Update BreachSiteEntity to match HIBP API changes. LogoType has become LogoPath.

## [2.0.9]
- Package maintenance updates

## [2.0.8]
- Package maintenance updates

## [2.0.7]
- Add changelog
- Package maintenance updates

## [2.0.6]
- Package maintenance updates

## [2.0.5]
- Package maintenance updates

## [2.0.4]
- Add link to Pastebin for relevant pastes

## [2.0.3]
- Fix detection of null values for Paste dates

## [2.0.2]
- Package maintenance updates

## [2.0.1]
- Package maintenance updates

## [2.0.0]
- Core changes to reflect HIBP API changes
- Unit test enhancements

## [1.0.1]
- Use full namespace names for Collection package

## [1.0.0]
- Initial release
