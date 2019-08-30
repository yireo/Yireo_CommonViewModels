<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;

class CurrentProduct implements ArgumentInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * CurrentProduct constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param RequestInterface $request
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        RequestInterface $request
    ) {
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * @return ProductInterface
     * @throws RuntimeException
     */
    public function getProduct(): ProductInterface
    {
        $request = $this->request;
        /** @var $request \Magento\Framework\App\Request\Http */
        if ($request->getControllerName() !== 'product') {
            throw new RuntimeException('Wrong page');
        }

        if ($this->request->getActionName() !== 'view') {
            throw new RuntimeException('Wrong page');
        }

        $productId = (int)$this->request->getParam('id');
        if (!$productId) {
            throw new RuntimeException('No product ID');
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch(NoSuchEntityException $exception) {
            throw new RuntimeException('Wrong product ID');
        }

        return $product;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        try {
            $product = $this->getProduct();

        } catch(RuntimeException $exception) {
            return '<!-- ' .$exception->getMessage(). ' -->';
        }

        $productSku = $product->getSku();
        return $productSku;
    }
}
