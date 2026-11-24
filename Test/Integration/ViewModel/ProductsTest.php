<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration\ViewModel;

use InvalidArgumentException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\Products;

class ProductsTest extends TestCase
{
    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, ['sku' => 'cvm-simple'])]
    public function testGetProducts(): void
    {
        $viewModel = $this->getViewModel();
        $products = $viewModel->getProducts($viewModel->getSearchCriteriaBuilder()->create());
        $this->assertContains('cvm-simple', $this->getSkus($products));
    }

    /**
     * @return void
     */
    #[
        DataFixture(ProductFixture::class, ['sku' => 'cvm-enabled', 'name' => 'Cvm enabled']),
        DataFixture(ProductFixture::class, [
            'sku' => 'cvm-disabled',
            'name' => 'Cvm disabled',
            'status' => Status::STATUS_DISABLED
        ]),
        DataFixture(ProductFixture::class, [
            'sku' => 'cvm-invisible',
            'name' => 'Cvm invisible',
            'visibility' => Visibility::VISIBILITY_NOT_VISIBLE
        ])
    ]
    public function testIfOnlyEnabledAndVisibleProductsAreReturned(): void
    {
        $skus = $this->getSkus($this->getViewModel()->getProductsByName('Cvm %'));
        $this->assertSame(['cvm-enabled'], $skus);
    }

    /**
     * @return void
     */
    public function testIfNonAllowedAttributeIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getViewModel()->getProductsBySearchAttribute('cost', '%');
    }

    /**
     * @return void
     */
    #[
        DataFixture(ProductFixture::class, ['sku' => 'cvm-page-1', 'name' => 'Cvm page 1']),
        DataFixture(ProductFixture::class, ['sku' => 'cvm-page-2', 'name' => 'Cvm page 2'])
    ]
    public function testIfPageSizeIsClamped(): void
    {
        $this->assertCount(1, $this->getViewModel()->getProductsByName('Cvm page %', 0));
        $this->assertCount(2, $this->getViewModel()->getProductsByName('Cvm page %', 1000));
    }

    /**
     * @return void
     */
    #[
        DataFixture(ProductFixture::class, ['sku' => 'cvm-percent', 'name' => 'Cvm 100% cotton']),
        DataFixture(ProductFixture::class, ['sku' => 'cvm-plain', 'name' => 'Cvm plain cotton'])
    ]
    public function testIfWildcardsAreEscapedOnRequest(): void
    {
        $viewModel = $this->getViewModel();
        $this->assertSame(['cvm-percent'], $this->getSkus($viewModel->getProductsByName('%', 10, true)));
        $this->assertSame(['cvm-plain'], $this->getSkus($viewModel->getProductsByName('%m_plain%', 10)));
        $this->assertSame([], $this->getSkus($viewModel->getProductsByName('m_plain', 10, true)));
        $this->assertSame(['cvm-plain'], $this->getSkus($viewModel->getProductsSku('plain', 10, true)));
    }

    /**
     * @param ProductInterface[] $products
     * @return string[]
     */
    private function getSkus(array $products): array
    {
        $skus = array_values(array_map(fn (ProductInterface $product) => (string)$product->getSku(), $products));
        sort($skus);
        return $skus;
    }

    /**
     * @return Products
     */
    private function getViewModel(): Products
    {
        return Bootstrap::getObjectManager()->create(Products::class);
    }
}
