<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\Product;

class ProductTest extends TestCase
{
    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, as: 'product')]
    public function testSetProduct(): void
    {
        /** @var ProductInterface $product */
        $product = DataFixtureStorageManager::getStorage()->get('product');

        $viewModel = $this->getViewModel();
        $viewModel->setProduct($product);
        $this->assertEquals($product->getId(), $viewModel->getProduct()->getId());
        $this->assertEquals($product->getSku(), $viewModel->getSku());
    }

    /**
     * @return void
     */
    public function testIfMissingProductReturnsNoMarkup(): void
    {
        $viewModel = $this->getViewModel();
        $this->assertNull($viewModel->getProductData('name'));
        $this->assertSame('', $viewModel->getName());
        $this->assertSame('', $viewModel->getSku());
        $this->assertSame('', $viewModel->getDescription());
    }

    /**
     * @return Product
     */
    private function getViewModel(): Product
    {
        return Bootstrap::getObjectManager()->create(Product::class);
    }
}
