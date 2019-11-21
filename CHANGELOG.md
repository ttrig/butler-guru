# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Use travis to test with different PHP, Laravel and PHPUnit versions.
- "squizlabs/php_codesniffer" 3.5.2 to detect violations of [PSR12](https://www.php-fig.org/psr/psr-12/).

### Fixed

- All tests in `EventRouterTest.php` now passes.


## [0.3.0] - 2019-11-04

### Added

- Laravel package auto-discovery

### Changed

- *BREAKING:* Moved config file `guru.php` to `butler.php`


## [0.2.1] - 2019-09-20

### Added

- Support for Laravel 6


## [0.2.0] - 2018-05-17

### Changed

- Add support for laravel


## [0.1.0] - 2018-05-17

### Added

- Initial release
