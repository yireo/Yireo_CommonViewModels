<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RuntimeException;

/**
 * Class CurrentProduct
 * @package Yireo\CommonViewModels\ViewModel
 */
class CurrentProduct implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Product
     */
    private $productViewModel;

    /**
     * CurrentProduct constructor.
     * @param RequestInterface $request
     * @param Product $productViewModel
     */
    public function __construct(
        RequestInterface $request,
        Product $productViewModel
    ) {
        $this->request = $request;
        $this->productViewModel = $productViewModel;
    }

    /**
     * @throws RuntimeException
     * @throws NoSuchEntityException
     */
    public function initialize()
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

        $this->productViewModel->setProductById($productId);
    }

    /**
     * @return ProductInterface
     * @throws RuntimeException
     */
    public function getProduct(): ProductInterface
    {
        return $this->productViewModel->getProduct();
    }

    /**
     * @param string $methodName
     * @param array $methodArguments
     */
    public function __call(string $methodName, array $methodArguments = [])
    {
        if (method_exists($this->productViewModel, $methodName)) {
            return call_user_func([$this->productViewModel, $methodName], $methodArguments);
        }

        throw new RuntimeException('Invalid method: ' . $methodName);
    }
}
