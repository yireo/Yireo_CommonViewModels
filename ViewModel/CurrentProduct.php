<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use RuntimeException;

class CurrentProduct implements ArgumentInterface
{
    /**
     * @param RequestInterface $request
     * @param Product $productViewModel
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param Visibility $visibility
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly Product $productViewModel,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly Visibility $visibility
    ) {
    }

    /**
     * @return CurrentProduct
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function initialize(): CurrentProduct
    {
        if (!$this->request instanceof HttpRequest
            || $this->request->getFullActionName() !== 'catalog_product_view') {
            throw new RuntimeException('Wrong page');
        }

        $productId = (int)$this->request->getParam('id');
        if ($productId < 1) {
            throw new RuntimeException('No product ID');
        }

        $store = $this->storeManager->getStore();
        $product = $this->productRepository->getById($productId, false, (int)$store->getId());
        if (!$this->isProductAvailable($product, (int)$store->getWebsiteId())) {
            throw new NoSuchEntityException(__('Product is not available'));
        }

        $this->productViewModel->setProduct($product);
        return $this;
    }

    /**
     * @return ProductInterface
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function getProduct(): ProductInterface
    {
        $this->initialize();
        return $this->productViewModel->getProduct();
    }

    /**
     * @return string
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function getName(): string
    {
        $this->initialize();
        return $this->productViewModel->getName();
    }

    /**
     * @return string
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function getSku(): string
    {
        $this->initialize();
        return $this->productViewModel->getSku();
    }

    /**
     * Returns the raw (WYSIWYG) HTML description: output it via Magento\Catalog\Helper\Output::productAttribute()
     *
     * @return string
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function getDescription(): string
    {
        $this->initialize();
        return $this->productViewModel->getDescription();
    }

    /**
     * @param string $name
     * @return mixed
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function getProductData(string $name): mixed
    {
        $this->initialize();
        return $this->productViewModel->getProductData($name);
    }

    /**
     * @param ProductInterface $product
     * @param int $websiteId
     * @return bool
     */
    private function isProductAvailable(ProductInterface $product, int $websiteId): bool
    {
        if (!$product instanceof ProductModel) {
            return false;
        }

        if ((int)$product->getStatus() !== Status::STATUS_ENABLED) {
            return false;
        }

        if (!in_array((int)$product->getVisibility(), $this->visibility->getVisibleInSiteIds(), true)) {
            return false;
        }

        return in_array($websiteId, array_map('intval', (array)$product->getWebsiteIds()), true);
    }
}
