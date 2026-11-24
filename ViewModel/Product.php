<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Product implements ArgumentInterface
{
    private ?ProductInterface $product = null;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param ProductInterface $product
     * @return void
     */
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    /**
     * @param int $productId
     * @return void
     * @throws NoSuchEntityException
     */
    public function setProductById(int $productId): void
    {
        $this->product = $this->productRepository->getById($productId);
    }

    /**
     * @param string $productSku
     * @return void
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
        return (string)$this->getProductData('name');
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return (string)$this->getProductData('sku');
    }

    /**
     * Returns the raw (WYSIWYG) HTML description: output it via Magento\Catalog\Helper\Output::productAttribute()
     *
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->getProductData('description');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getProductData(string $name): mixed
    {
        try {
            $product = $this->getProduct();
        } catch (RuntimeException $exception) {
            $this->logger->debug('Yireo_CommonViewModels: ' . $exception->getMessage());
            return null;
        }

        return $product->getData($name);
    }
}
