# Common ViewModels for usage in Magento 2
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
        <argument name="view_model" xsi:type="object">Yireo\CommonViewModels\ViewModel\Example</argument>        
    </arguments>
</block>
```

In your `example.phtml`:
```php
<?php
/** @var \Yireo\CommonViewModels\ViewModel\Example $viewModel */
$viewModel = $block->getViewModel();
?>
```

## `Yireo\CommonViewModels\ViewModel\Product`
Initialize the product with the data you have:
```php
$viewModel->setProduct($product);
$viewModel->setProductById($productId);
$viewModel->setProductBySku($productSku);
```
And then use it:
```php
$viewModel->getProduct();
$viewModel->getSku();
```

## `Yireo\CommonViewModels\ViewModel\CurrentProduct`
Only working when the page is `catalog/product/view` and when the `id` is set in the URL. Initialize it with the request first:
```php
$viewModel->initialize();
```

And then use it:
```php
$viewModel->getProduct();
$viewModel->getSku();
```

The `CurrentProduct` ViewModel extends the `Product` ViewModel through injection.

## `Yireo\CommonViewModels\ViewModel\Products`
Use existing filters:
```php
$viewModel->getProductsByName('%hoodie%');
$viewModel->getProductsBySku('B01%');
```
or build your own:
```php
$searchCriteriaBuilder = $viewModel->getSearchCriteriaBuilder();
$viewModel->getProducts($searchCriteriaBuilder->create());

```