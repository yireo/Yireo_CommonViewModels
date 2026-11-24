# Common ViewModels for usage in Magento 2

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-common-view-models)
<!-- badges.specs.end -->

Gathering of common useful ViewModels for usage in the XML layout.

## Installation
```bash
composer require yireo/magento2-common-view-models
bin/magento module:enable Yireo_CommonViewModels
```

## Roadmap
There is no roadmap. Feel free to open an **Issue** to request creation of a new ViewModel or enhancement of an existing ViewModel. Feel free to submit PRs. I'll treat it respectfully once I drink my morning coffee.

## Generic usage of ViewModels
In Magento 2.2.1 or newer, create a XML layout instruction:
```xml
<block name="example" template="example.phtml">
    <arguments>
        <argument name="example_view_model" xsi:type="object">Yireo\CommonViewModels\ViewModel\Example</argument>        
    </arguments>
</block>
```

In your `example.phtml`:
```php
<?php
/** @var \Yireo\CommonViewModels\ViewModel\Example $viewModel */
$exampleViewModel = $block->getExampleViewModel();
?>
```

## `Yireo\CommonViewModels\ViewModel\Product`
Initialize the product with the data you have:
```php
$productViewModel->setProduct($product);
$productViewModel->setProductById($productId);
$productViewModel->setProductBySku($productSku);
```
And then use it:
```php
$productViewModel->getProduct();
$productViewModel->getSku();
```

`getDescription()` returns the raw HTML of the description. Output it via `Magento\Catalog\Helper\Output::productAttribute()` and never echo it directly. When no product is set, `getProductData()` returns `null`.

## `Yireo\CommonViewModels\ViewModel\CurrentProduct`
Only working when the page is `catalog/product/view` and when the `id` is set in the URL. Initialize it with the request first:
```php
$currentProductViewModel->initialize();
```

And then use it:
```php
$currentProductViewModel->getProduct();
$currentProductViewModel->getSku();
```

The `CurrentProduct` ViewModel uses the `Product` ViewModel through injection and offers `getName()`, `getSku()`, `getDescription()` and `getProductData($name)`. It only returns products that are enabled, visible in the catalog or search and assigned to the current website. Otherwise, it throws an exception.

## `Yireo\CommonViewModels\ViewModel\Products`
Use existing filters:
```php
$productsViewModel->getProductsByName('%hoodie%');
$productsViewModel->getProductsSku('B01%');
```

Only `name` and `sku` can be searched, the page size is limited to 1-100, and only enabled products that are visible in the catalog or search of the current store are returned.

When the search term comes from user input, pass `$escape = true`. This escapes the LIKE wildcards `%` and `_` and matches the term anywhere in the value:
```php
$productsViewModel->getProductsByName($searchTerm, 10, true);
```
or build your own:
```php
$searchCriteriaBuilder = $productsViewModel->getSearchCriteriaBuilder();
$productsViewModel->getProducts($searchCriteriaBuilder->create());
```

`getProducts()` does not add any filters: with your own search criteria, you are responsible for filtering on status, visibility and store.

## `Yireo\CommonViewModels\ViewModel\FormKey`
Usage:
```php
$formkeyViewModel->getToken();
```
## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CommonViewModels/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CommonViewModels/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CommonViewModels/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CommonViewModels/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CommonViewModels/compile.yml?label=compile)
<!-- badges.test.end -->
