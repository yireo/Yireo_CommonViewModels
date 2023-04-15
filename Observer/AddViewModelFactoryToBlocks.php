<?php declare(strict_types=1);

namespace Yireo\CommonViewModels\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\Template;
use Yireo\CommonViewModels\Factory\ViewModelFactory;

class AddViewModelFactoryToBlocks implements ObserverInterface
{
    private ViewModelFactory $viewModelFactory;

    public function __construct(ViewModelFactory $viewModelFactory) {
        $this->viewModelFactory = $viewModelFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $block = $event->getBlock();
        if (!$block instanceof Template) {
            return;
        }

        $block->assign('viewModelFactory', $this->viewModelFactory);
    }
}
