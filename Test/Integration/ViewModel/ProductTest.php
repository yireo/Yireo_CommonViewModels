<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\Product;

/**
 * Class ProductTest
 * @package Yireo\CommonViewModels\Test\Integration
 */
class ProductTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testSetProduct()
    {
        $productId = 1;
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->getObjectManager()->get(ProductRepositoryInterface::class);
        $product = $productRepository->getById($productId);
        $this->assertEquals($productId, $product->getId());

        $viewModel = $this->getViewModel();
        $viewModel->setProduct($product);
        $this->assertEquals($productId, $viewModel->getProduct()->getId());
    }

    /**
     * @return Product
     */
    private function getViewModel(): Product
    {
        return $this->getObjectManager()->get(Product::class);
    }

    /**
     * @return ObjectManagerInterface
     */
    private function getObjectManager(): ObjectManagerInterface
    {
        return Bootstrap::getObjectManager();
    }
}
