# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 25 November 2026
### Added
- ViewModel factory, available as `$viewModelFactory` in frontend templates, to create ViewModels from within a template
- Setters of the CurrentProduct ViewModel can be daisy-chained
- Optional `$escape` flag in the Products ViewModel search to escape LIKE wildcards in user input

### Changed
- Requires PHP 8.2 to 8.5; PHP 8.1 and older are no longer supported
- CurrentProduct offers explicit getters only and no longer forwards arbitrary method calls (including setters) to the Product ViewModel
- Documented how to use the ViewModels safely

### Fixed
- Missing dependencies on Magento_Store and psr/log are now declared

### Security
- CurrentProduct could return disabled, invisible or other-website products; it now only works on product pages and returns enabled, visible products of the current website
- CurrentCategory could return inactive categories or categories outside the current store root
- Products search could filter on any attribute and return any number of disabled or invisible products; it is now limited to name and SKU, a capped page size and visible, enabled products of the current store
- Product ViewModel leaked exception messages into the page as HTML comments; errors are now logged instead
- ViewModel factory only creates real ViewModel classes and is only added to frontend blocks

## [0.0.2] - 29 July 2020
### Added
- Magento 2.4 compatibility

## [0.0.1] - Undocumented
