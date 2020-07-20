<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use RuntimeException;

/**
 * Class Product
 * @package Yireo\CommonViewModels\ViewModel
 */
class Product implements ArgumentInterface
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Product constructor.
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    /**
     * @param int $productId
     * @throws NoSuchEntityException
     */
    public function setProductById(int $productId): void
    {
        $this->product = $this->productRepository->getById($productId);
    }


    /**
     * @param string $productSku
     * @throws NoSuchEntityException
     */
    public function setProductBySku(string $productSku): void
    {
        $this->product = $this->productRepository->get($productSku);
    }

    /**
     * @return ProductInterface
     * @throws RuntimeException
     */
    public function getProduct(): ProductInterface
    {
        if (!$this->product instanceof ProductInterface) {
            throw new RuntimeException('No valid product set');
        }

        return $this->product;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getProductData('name');
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->getProductData('sku');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getProductData('description');
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    public function getProductData(string $name)
    {
        try {
            $product = $this->getProduct();
        } catch (Exception $exception) {
            return '<!-- ' . $exception->getMessage() . ' -->';
        }

        return $product->getData($name);
    }
}
