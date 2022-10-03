# HIBP-PHP Changelog
## *5.1.1* - 2022-10-03
- Update contact info

## *5.1.0* - 2022-02-25

# Update
- Update dependencies and add dependency versions for Laravel 9

## *5.0.6* - 2021-11-13

### Update
- Update dependencies

## *5.0.5* - 2021-07-01

### Update
- Updated dependencies to fix a security issue  
(https://github.com/advisories/GHSA-9f46-5r25-5wfm)

## *5.0.4* - 2021-05-07

### Update
- Updated dependencies to fix a security issue

## *5.0.2* & *5.0.3*
No code changes

## *5.0.1* - 2021-01-21

### Update
- Updated Changelog format to be clearer on releases and items per release
- Updated dependencies

## *5.0.0* - 2021-01-16

### Added

- Guzzle Client options array for API consumption methods
- Mocked responses for test suite

### Removed
- Valid API key dependency to run test suite
- Dropped support for PHP <7.4

## *4.4.0*
  - Add options array to `Breaches` and `Pastes` methods to allow use of GuzzleHttp Client options

## *4.3.0*
- Add options array to `PwnedPassword` methods to allow use of GuzzleHttp Client options

## *4.2.2*
- Update package dependencies to resolve security issue in `symfony/http-kernel` package

## *4.2.1*
- Update package dependencies

## *4.2.0*
- Add `paddedRangeFromHash()`, `paddedRangeDataFromHash()` and `stripZeroMatchesData` methods to `PwnedPassword` class.
- Update package dependencies
- Add static analysis to CI pipeline
- Various internal code tidying

## *4.1.0*
- Update package dependencies
- Internal cody tidy up

## *4.0.0*
- Update to use HIBP API v3
- Remove deprecated `range()` and `rangeData()` methods from `PwnedPassword` class.

## *3.3.0*
- Add breach lookup params  
      Truncate responses to only return breach names  
      Filter results to specific domains  
      Include unverified results in responses

## *3.2.2*
- Update user-agent string

## *3.2.1*
- PHP CodeSniffer fixes

## *3.2.0*
- Add Laravel-specific classes for Service Providers and Facades

## *3.1.0*
- Update src directory structure and refactor unit tests for PHPUnit 8.x deprecations.

## *3.0.0*
- Update BreachSiteEntity to match HIBP API changes. LogoType has become LogoPath.

## *2.0.9*
- Package maintenance updates

## *2.0.8*
- Package maintenance updates

## *2.0.7*
- Add changelog
- Package maintenance updates

## *2.0.6*
- Package maintenance updates

## *2.0.5*
- Package maintenance updates

## *2.0.4*
- Add link to Pastebin for relevant pastes

## *2.0.3*
- Fix detection of null values for Paste dates

## *2.0.2*
- Package maintenance updates

## *2.0.1*
- Package maintenance updates

## *2.0.0*
- Core changes to reflect HIBP API changes
- Unit test enhancements

## *1.0.1*
- Use full namespace names for Collection package

## *1.0.0*
- Initial release
