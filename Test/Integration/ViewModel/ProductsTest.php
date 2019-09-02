<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\Products;

/**
 * Class ProductsTest
 * @package Yireo\CommonViewModels\Test\Integration
 */
class ProductsTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testGetProducts()
    {
        $productId = 1;

        $viewModel = $this->getViewModel();
        $products = $viewModel->getProducts($viewModel->getSearchCriteriaBuilder()->create());
        $this->assertEquals(true, (bool) count($products));
        $product = array_shift($products);
        $this->assertEquals($productId, $product->getId());
    }

    /**
     * @return Products
     */
    private function getViewModel(): Products
    {
        return $this->getObjectManager()->get(Products::class);
    }

    /**
     * @return ObjectManagerInterface
     */
    private function getObjectManager(): ObjectManagerInterface
    {
        return Bootstrap::getObjectManager();
    }
}
