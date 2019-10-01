<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration;

use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;
use Yireo\CommonViewModels\ViewModel\CurrentCategory;

/**
 * Class CurrentCategoryTest
 * @package Yireo\CommonViewModels\Test\Integration
 */
class CurrentCategoryTest extends AbstractControllerTestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/category.php
     */
    public function testIfCheckOfRightPageFails()
    {
        $categoryName = 'Category 1';
        $this->dispatch('catalog/category/view/category/333');

        /** @var CurrentCategory $viewModel */
        $viewModel = $this->_objectManager->get(CurrentCategory::class);
        $this->assertEquals($categoryName, $viewModel->getCategoryName());
    }
}
