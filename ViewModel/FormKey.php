<?php
declare(strict_types=1);

namespace Yireo\CommonViewModels\ViewModel;

use Magento\Framework\Data\Form\FormKey as FormKeyModel;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class FormKey
 * @package Yireo\CommonViewModels\ViewModel
 */
class FormKey implements ArgumentInterface
{
    /**
     * @var FormKeyModel
     */
    private $formKey;

    /**
     * FormKey constructor.
     * @param FormKeyModel $formKey
     */
    public function __construct(
        FormKeyModel $formKey
    ) {
        $this->formKey = $formKey;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return (string)$this->formKey->getFormKey();
    }

    /**
     * @return bool
     */
    public function isPresent(): bool
    {
        return (bool)$this->formKey->isPresent();
    }
}
