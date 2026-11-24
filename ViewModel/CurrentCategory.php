<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use RuntimeException;

class CurrentCategory implements ArgumentInterface
{
    /**
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CurrentProduct $currentProduct
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CurrentProduct $currentProduct,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        try {
            return (string)$this->getCategoryFromRequest()->getName();
        } catch (NoSuchEntityException $e) {
        }

        try {
            return (string)$this->getCategoryFromCurrentProduct()->getName();
        } catch (NoSuchEntityException | RuntimeException $e) {
        }

        return 'Unknown category';
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategoryFromRequest(): CategoryInterface
    {
        return $this->loadValidCategory((int)$this->request->getParam('category'));
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     * @throws RuntimeException
     */
    private function getCategoryFromCurrentProduct(): CategoryInterface
    {
        /** @var Product $product */
        $product = $this->currentProduct->getProduct();

        if ($product->getCategoryId()) {
            return $this->loadValidCategory((int)$product->getCategoryId());
        }

        $categoryIds = (array)$product->getCategoryIds();
        if (count($categoryIds) === 1) {
            return $this->loadValidCategory((int)reset($categoryIds));
        }

        throw new NoSuchEntityException(__('Dont know which category you want'));
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function loadValidCategory(int $categoryId): CategoryInterface
    {
        if ($categoryId < 1) {
            throw new NoSuchEntityException(__('No category ID'));
        }

        $store = $this->storeManager->getStore();
        $category = $this->categoryRepository->get($categoryId, (int)$store->getId());
        if (!$category->getIsActive()) {
            throw new NoSuchEntityException(__('Category is not active'));
        }

        $rootCategoryId = (int)$this->storeManager->getGroup($store->getStoreGroupId())->getRootCategoryId();
        if (!str_contains('/' . $category->getPath() . '/', '/' . $rootCategoryId . '/')) {
            throw new NoSuchEntityException(__('Category does not belong to the current store'));
        }

        return $category;
    }
}
