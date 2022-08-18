<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Block\Adminhtml\System;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Message\ManagerInterface;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Model\Config\Source\System\OpenClose;

/**
 * Class OpenTime
 * @package Mageplaza\StoreLocator\Block\Adminhtml\System
 */
class OpenTime extends Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/store-locator/open-time.phtml';

    /**
     * @var OpenClose
     */
    protected $_openClose;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * OpenTime constructor.
     *
     * @param Context $context
     * @param OpenClose $openClose
     * @param HelperData $helperData
     * @param ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        OpenClose $openClose,
        HelperData $helperData,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_openClose     = $openClose;
        $this->helperData     = $helperData;
        $this->messageManager = $messageManager;

        parent::__construct($context, $data);
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_decorateRowHtml($element, $this->toHtml());
    }

    /**
     * @param AbstractElement $element
     *
     * @return mixed
     */
    public function getFieldName(AbstractElement $element)
    {
        return $element->getName();
    }

    /**
     * Get open/close options
     *
     * @return array
     */
    public function getOpenCloseOption()
    {
        return $this->_openClose->toOptionArray();
    }

    /**
     * @return mixed|string
     */
    public function getOpenCloseConfig()
    {
        $decodedValue = '';
        $value        = $this->helperData->getStoreTimeSetting($this->getDayName());
        try {
            $decodedValue = $this->helperData->jsDecode($value);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $decodedValue;
    }

    /**
     * Get field day name
     *
     * @return mixed
     */
    public function getDayName()
    {
        $elementName  = $this->getElement()->getName();
        $elementName  = str_replace(['[', ']'], [',', ''], $elementName);
        $elementArray = explode(',', $elementName);

        return $elementArray[3];
    }
}
