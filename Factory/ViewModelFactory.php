<?php declare(strict_types=1);

namespace Yireo\CommonViewModels\Factory;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ViewModelFactory
{
    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * @param string $className
     * @return ArgumentInterface
     * @throws InvalidArgumentException
     */
    public function create(string $className): ArgumentInterface
    {
        if (!class_exists($className) || !is_a($className, ArgumentInterface::class, true)) {
            throw new InvalidArgumentException('Class is not a valid ViewModel');
        }

        /** @var ArgumentInterface $viewModel */
        $viewModel = $this->objectManager->create($className);
        return $viewModel;
    }
}
