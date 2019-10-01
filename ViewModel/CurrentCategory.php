<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CurrentCategory implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CurrentProduct
     */
    private $currentProduct;

    /**
     * CurrentCategory constructor.
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        CurrentProduct $currentProduct
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->currentProduct = $currentProduct;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        try {
            return $this->getCategoryFromRequest()->getName();
        } catch (NoSuchEntityException $e) {
        }

        try {
            return $this->getCategoryFromCurrentProduct()->getName();
        } catch (NoSuchEntityException $e) {
        }

        return 'Unknown category';
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategoryFromRequest(): CategoryInterface
    {
        $categoryId = (int)$this->request->getParam('category');
        return $this->categoryRepository->get($categoryId);
    }

    /**
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    private function getCategoryFromCurrentProduct(): CategoryInterface
    {
        /** @var Product $product */
        $product = $this->currentProduct->getProduct();

        if ($product->getCategoryId()) {
            return $this->categoryRepository->get($product->getCategoryId());
        }

        $categoryIds = $product->getCategoryIds();
        if (count($categoryIds) === 1) {
            $categoryId = array_shift($product->getCategoryIds());
            return $this->categoryRepository->get($categoryId);
        }

        throw new NoSuchEntityException(__('Dont know which category you want'));
    }
}
