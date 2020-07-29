<?php

declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
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
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @throws NoSuchEntityException
     */
    public function testIfCheckOfRightPageFails()
    {
        $this->expectException(\RuntimeException::class);
        $productId = 1;

        /** @var CurrentProduct $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CurrentProduct::class);
        $viewModel->initialize();
        $product = $viewModel->getProduct();
        $this->assertEquals($productId, $product->getId());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @throws NoSuchEntityException
     */
    public function testIfTheRightPageWorks()
    {
        $productId = 1;

        /** @var Request $request */
        $this->dispatch('catalog/product/view/id/' . $productId);

        /** @var CurrentProduct $viewModel */
        $viewModel = Bootstrap::getObjectManager()->get(CurrentProduct::class);
        $viewModel->initialize();
        $product = $viewModel->getProduct();
        $this->assertEquals($productId, $product->getId());
    }
}
