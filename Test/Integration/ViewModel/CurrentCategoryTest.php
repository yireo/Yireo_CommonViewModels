<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration\ViewModel;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Test\Fixture\Category as CategoryFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;
use Yireo\CommonViewModels\ViewModel\CurrentCategory;

class CurrentCategoryTest extends AbstractControllerTestCase
{
    /**
     * @return void
     */
    #[DataFixture('Magento/Catalog/_files/category.php')]
    public function testIfCategoryPageWorks(): void
    {
        $this->dispatch('catalog/category/view/category/333');
        $this->assertEquals('Category 1', $this->getViewModel()->getCategoryName());
    }

    /**
     * @return void
     */
    #[DataFixture(CategoryFixture::class, ['name' => 'Valid category'], 'category')]
    public function testIfActiveCategoryOfCurrentStoreWorks(): void
    {
        $this->getRequest()->setParam('category', $this->getFixtureCategory()->getId());
        $this->assertEquals('Valid category', $this->getViewModel()->getCategoryName());
    }

    /**
     * @return void
     */
    #[DataFixture(CategoryFixture::class, ['name' => 'Inactive category', 'is_active' => false], 'category')]
    public function testIfInactiveCategoryIsNotReturned(): void
    {
        $this->getRequest()->setParam('category', $this->getFixtureCategory()->getId());
        $this->assertEquals('Unknown category', $this->getViewModel()->getCategoryName());
    }

    /**
     * @return void
     */
    #[
        DataFixture(CategoryFixture::class, ['name' => 'Other root', 'parent_id' => 1], 'root'),
        DataFixture(CategoryFixture::class, ['name' => 'Foreign category', 'parent_id' => '$root.id$'], 'category')
    ]
    public function testIfCategoryOfOtherRootIsNotReturned(): void
    {
        $this->getRequest()->setParam('category', $this->getFixtureCategory()->getId());
        $this->assertEquals('Unknown category', $this->getViewModel()->getCategoryName());
    }

    /**
     * @return CategoryInterface
     */
    private function getFixtureCategory(): CategoryInterface
    {
        return DataFixtureStorageManager::getStorage()->get('category');
    }

    /**
     * @return CurrentCategory
     */
    private function getViewModel(): CurrentCategory
    {
        return $this->_objectManager->create(CurrentCategory::class);
    }
}
