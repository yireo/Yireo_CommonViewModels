<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class Products
 * @package Yireo\CommonViewModels\ViewModel
 */
class Products implements ArgumentInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * Product constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param string $searchWord
     * @param int $pageSize
     * @return ProductInterface[]
     */
    public function getProductsByName(string $search, int $pageSize = 10): array
    {
        return $this->getProductsBySearchAttribute('name', $search, $pageSize);
    }

    /**
     * @param string $searchWord
     * @param int $pageSize
     * @return ProductInterface[]
     */
    public function getProductsSku(string $search, int $pageSize = 10): array
    {
        return $this->getProductsBySearchAttribute('sku', $search, $pageSize);
    }

    /**
     * @param string $searchWord
     * @param int $pageSize
     * @return ProductInterface[]
     */
    public function getProductsBySearchAttribute(string $attribute, string $search, int $pageSize = 10): array
    {
        $searchCriteriaBuilder = $this->getSearchCriteriaBuilder();
        $searchCriteriaBuilder->setPageSize($pageSize);
        $searchCriteriaBuilder->addFilter($attribute, $search, 'like');

        $searchResult = $this->productRepository->getList($searchCriteriaBuilder->create());
        return $searchResult->getItems();
    }

    /**
     * @param string $searchWord
     * @param int $pageSize
     * @return ProductInterface[]
     */
    public function getProducts(SearchCriteria $searchCriteria): array
    {
        $searchResult = $this->productRepository->getList($searchCriteria);
        return $searchResult->getItems();
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilderFactory->create();
    }
}
