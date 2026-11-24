<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\Test\Integration\Factory;

use InvalidArgumentException;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\CommonViewModels\Factory\ViewModelFactory;
use Yireo\CommonViewModels\ViewModel\FormKey;

class ViewModelFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function testIfViewModelIsCreated(): void
    {
        $factory = Bootstrap::getObjectManager()->get(ViewModelFactory::class);
        $this->assertInstanceOf(FormKey::class, $factory->create(FormKey::class));
    }

    /**
     * @return void
     */
    public function testIfNonViewModelIsRejectedBeforeInstantiation(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->never())->method('create');

        $this->expectException(InvalidArgumentException::class);
        (new ViewModelFactory($objectManager))->create(DataObject::class);
    }

    /**
     * @return void
     */
    public function testIfUnknownClassIsRejected(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->never())->method('create');

        $this->expectException(InvalidArgumentException::class);
        (new ViewModelFactory($objectManager))->create('Yireo\CommonViewModels\DoesNotExist');
    }
}
