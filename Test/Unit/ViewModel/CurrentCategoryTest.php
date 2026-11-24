<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Unit\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yireo\CommonViewModels\ViewModel\CurrentCategory;
use Yireo\CommonViewModels\ViewModel\CurrentProduct;

class CurrentCategoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testIfValidCategoryNameIsReturned(): void
    {
        $this->assertSame('foobar', $this->getCurrentCategory(true, '1/2/42')->getCategoryName());
    }

    /**
     * @return void
     */
    public function testIfInactiveCategoryIsIgnored(): void
    {
        $this->assertSame('Unknown category', $this->getCurrentCategory(false, '1/2/42')->getCategoryName());
    }

    /**
     * @return void
     */
    public function testIfCategoryOfOtherRootIsIgnored(): void
    {
        $this->assertSame('Unknown category', $this->getCurrentCategory(true, '1/22/42')->getCategoryName());
    }

    /**
     * @param bool $isActive
     * @param string $path
     * @return CurrentCategory
     */
    private function getCurrentCategory(bool $isActive, string $path): CurrentCategory
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturn(42);

        $category = $this->createStub(CategoryInterface::class);
        $category->method('getName')->willReturn('foobar');
        $category->method('getIsActive')->willReturn($isActive);
        $category->method('getPath')->willReturn($path);

        $categoryRepository = $this->createStub(CategoryRepositoryInterface::class);
        $categoryRepository->method('get')->willReturn($category);

        $currentProduct = $this->createStub(CurrentProduct::class);
        $currentProduct->method('getProduct')->willThrowException(new RuntimeException('Wrong page'));

        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(1);
        $store->method('getStoreGroupId')->willReturn(1);

        $group = $this->createStub(GroupInterface::class);
        $group->method('getRootCategoryId')->willReturn(2);

        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);
        $storeManager->method('getGroup')->willReturn($group);

        return new CurrentCategory($request, $categoryRepository, $currentProduct, $storeManager);
    }
}
