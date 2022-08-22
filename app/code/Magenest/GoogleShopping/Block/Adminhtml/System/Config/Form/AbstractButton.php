<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class AbstractButton
 * @package Magenest\GoogleShopping\Block\Adminhtml\System\Form
 */
abstract class AbstractButton extends Field
{
    /** @var string  */
    protected $_template = "system/config/form/get-access-token.phtml";

    /** @var string  */
    protected $_buttonLabel = "Get Information";

    /**
     * @return AbstractButton
     */
    protected function _prepareLayout()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate($this->_template);
        }
        return parent::_prepareLayout();
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    public function getButtonLabel()
    {
        return $this->_buttonLabel;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setData([
            'html_id' => $element->getHtmlId()
        ]);

        return $this->_toHtml();
    }
}
