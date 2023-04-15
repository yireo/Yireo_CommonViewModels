<?php declare(strict_types=1);

namespace Yireo\CommonViewModels\Factory;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ViewModelFactory
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return ArgumentInterface
     * @throws InvalidArgumentException
     */
    public function create(string $className): ArgumentInterface
    {
        $viewModel = $this->objectManager->create($className);
        if (empty($viewModel) || !is_object($viewModel)) {
            throw new InvalidArgumentException('Class "' . $className . '" not found');
        }

        if (!$viewModel instanceof ArgumentInterface) {
            throw new InvalidArgumentException('Class "' . $className . '" must implement ' . ArgumentInterface::class);
        }

        return $viewModel;
    }
}
