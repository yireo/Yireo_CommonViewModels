<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Unit\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\ViewModel\CurrentCategory;
use Yireo\CommonViewModels\ViewModel\CurrentProduct;

class CurrentCategoryTest extends TestCase
{
    public function testSomething()
    {
        $inputCategoryName = 'foobar';

        $mockBuilder = $this->getMockBuilder(RequestInterface::class);
        $mockBuilder->disableOriginalConstructor();
        $request = $mockBuilder->getMock();
        $request->method('getParam')->willReturn(42);


        $mockBuilder = $this->getMockBuilder(CategoryInterface::class);
        $mockBuilder->disableOriginalConstructor();
        $category = $mockBuilder->getMock();
        $category->method('getName')->willReturn($inputCategoryName);

        $mockBuilder = $this->getMockBuilder(CategoryRepositoryInterface::class);
        $mockBuilder->disableOriginalConstructor();
        $categoryRepository = $mockBuilder->getMock();
        $categoryRepository->method('get')->willReturn($category);


        /** @var CurrentProduct $currentProduct */
        $mockBuilder = $this->getMockBuilder(CurrentProduct::class);
        $mockBuilder->disableOriginalConstructor();
        $currentProduct = $mockBuilder->getMock();

        $currentCategory = new CurrentCategory($request, $categoryRepository, $currentProduct);
        $this->assertInstanceOf(CurrentCategory::class, $currentCategory);

        $categoryName = $currentCategory->getCategoryName();
        $this->assertEquals($inputCategoryName, $categoryName);
    }
}
