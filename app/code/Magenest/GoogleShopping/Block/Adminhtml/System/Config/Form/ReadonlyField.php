<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ReadonlyField
 * @package Magenest\GoogleShopping\Adminhtml\System\Config
 */
class ReadonlyField extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setReadonly(true);
        $element->setClass('readonly-field');
        return parent::_getElementHtml($element);
    }
}
