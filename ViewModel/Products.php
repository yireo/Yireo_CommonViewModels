<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use InvalidArgumentException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class Products implements ArgumentInterface
{
    private const SEARCHABLE_ATTRIBUTES = ['name', 'sku'];
    private const MAX_PAGE_SIZE = 100;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param StoreManagerInterface $storeManager
     * @param Visibility $visibility
     * @param DbHelper $dbHelper
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly Visibility $visibility,
        private readonly DbHelper $dbHelper
    ) {
    }

    /**
     * @param string $search
     * @param int $pageSize
     * @param bool $escape Escape LIKE wildcards in $search and match it anywhere in the value
     * @return ProductInterface[]
     */
    public function getProductsByName(string $search, int $pageSize = 10, bool $escape = false): array
    {
        return $this->getProductsBySearchAttribute('name', $search, $pageSize, $escape);
    }

    /**
     * @param string $search
     * @param int $pageSize
     * @param bool $escape Escape LIKE wildcards in $search and match it anywhere in the value
     * @return ProductInterface[]
     */
    public function getProductsSku(string $search, int $pageSize = 10, bool $escape = false): array
    {
        return $this->getProductsBySearchAttribute('sku', $search, $pageSize, $escape);
    }

    /**
     * @param string $attribute
     * @param string $search
     * @param int $pageSize
     * @param bool $escape Escape LIKE wildcards in $search and match it anywhere in the value
     * @return ProductInterface[]
     * @throws InvalidArgumentException
     */
    public function getProductsBySearchAttribute(
        string $attribute,
        string $search,
        int $pageSize = 10,
        bool $escape = false
    ): array {
        if (!in_array($attribute, self::SEARCHABLE_ATTRIBUTES, true)) {
            throw new InvalidArgumentException('Attribute "' . $attribute . '" is not searchable');
        }

        if ($escape) {
            $search = $this->dbHelper->escapeLikeValue($search, ['position' => 'any']);
        }

        $searchCriteriaBuilder = $this->getSearchCriteriaBuilder();
        $searchCriteriaBuilder->setPageSize(max(1, min($pageSize, self::MAX_PAGE_SIZE)));
        $searchCriteriaBuilder->addFilter($attribute, $search, 'like');
        $searchCriteriaBuilder->addFilter('status', Status::STATUS_ENABLED);
        $searchCriteriaBuilder->addFilter('visibility', $this->visibility->getVisibleInSiteIds(), 'in');
        $searchCriteriaBuilder->addFilter('store_id', (int)$this->storeManager->getStore()->getId());

        $searchResult = $this->productRepository->getList($searchCriteriaBuilder->create());
        return $searchResult->getItems();
    }

    /**
     * Raw repository access: the caller is responsible for status, visibility and store filters
     *
     * @param SearchCriteria $searchCriteria
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
