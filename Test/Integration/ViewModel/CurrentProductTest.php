<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Test\Fixture\Website as WebsiteFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Request;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;
use RuntimeException;
use Yireo\CommonViewModels\ViewModel\CurrentProduct;

class CurrentProductTest extends AbstractControllerTestCase
{
    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, as: 'product')]
    public function testIfCheckOfRightPageFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getViewModel()->initialize();
    }

    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, as: 'product')]
    public function testIfTheRightPageWorks(): void
    {
        $product = $this->getFixtureProduct();
        $this->setRequest('catalog', (int)$product->getId());

        $viewModel = $this->getViewModel();
        $this->assertEquals($product->getId(), $viewModel->getProduct()->getId());
        $this->assertEquals($product->getName(), $viewModel->getName());
        $this->assertEquals($product->getSku(), $viewModel->getSku());
        $this->assertEquals($product->getSku(), $viewModel->getProductData('sku'));
    }

    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, as: 'product')]
    public function testIfOtherRouteWithSameControllerAndActionFails(): void
    {
        $this->setRequest('review', (int)$this->getFixtureProduct()->getId());

        $this->expectException(RuntimeException::class);
        $this->getViewModel()->initialize();
    }

    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, ['status' => Status::STATUS_DISABLED], 'product')]
    public function testIfDisabledProductFails(): void
    {
        $this->setRequest('catalog', (int)$this->getFixtureProduct()->getId());

        $this->expectException(NoSuchEntityException::class);
        $this->getViewModel()->getProduct();
    }

    /**
     * @return void
     */
    #[DataFixture(ProductFixture::class, ['visibility' => Visibility::VISIBILITY_NOT_VISIBLE], 'product')]
    public function testIfInvisibleProductFails(): void
    {
        $this->setRequest('catalog', (int)$this->getFixtureProduct()->getId());

        $this->expectException(NoSuchEntityException::class);
        $this->getViewModel()->getProduct();
    }

    /**
     * @return void
     */
    #[
        DataFixture(WebsiteFixture::class, as: 'website'),
        DataFixture(
            ProductFixture::class,
            ['extension_attributes' => ['website_ids' => ['$website.id$']]],
            'product'
        )
    ]
    public function testIfProductOfOtherWebsiteFails(): void
    {
        $this->setRequest('catalog', (int)$this->getFixtureProduct()->getId());

        $this->expectException(NoSuchEntityException::class);
        $this->getViewModel()->getProduct();
    }

    /**
     * @param string $routeName
     * @param int $productId
     * @return void
     */
    private function setRequest(string $routeName, int $productId): void
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $request->setRouteName($routeName);
        $request->setControllerName('product');
        $request->setActionName('view');
        $request->setParam('id', $productId);
    }

    /**
     * @return ProductInterface
     */
    private function getFixtureProduct(): ProductInterface
    {
        return DataFixtureStorageManager::getStorage()->get('product');
    }

    /**
     * @return CurrentProduct
     */
    private function getViewModel(): CurrentProduct
    {
        return $this->_objectManager->create(CurrentProduct::class);
    }
}
