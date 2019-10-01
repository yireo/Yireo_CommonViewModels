<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\CurrentProduct;
use Yireo\CommonViewModels\ViewModel\Products;

/**
 * Class CurrentProductTest
 * @package Yireo\CommonViewModels\Test\Integration
 */
class CurrentProductTest extends AbstractControllerTestCase
{
    public function testIfCheckOfRightPageFails()
    {
        $this->expectException(\RuntimeException::class);
        $productId = 1;

        $viewModel = Bootstrap::getObjectManager()->get(CurrentProduct::class);
        $viewModel->initialize();
        $product = $viewModel->getProduct();
        $this->assertEquals($productId, $product->getId());
    }

    public function testIfTheRightPageWorks()
    {
        $productId = 1;

        /** @var Request $request */
        //$request = Bootstrap::getObjectManager()->get(RequestInterface::class);
        //$request->setActionName('view');
        //$request->setControllerName('product');
        //$request->setParams(['id' => $productId]);
        $this->dispatch('catalog/product/view/id/'.$productId);

        $viewModel = Bootstrap::getObjectManager()->get(CurrentProduct::class);
        $viewModel->initialize();
        $product = $viewModel->getProduct();
        $this->assertEquals($productId, $product->getId());
    }
}
