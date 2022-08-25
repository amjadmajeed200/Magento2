<?php
/**
 * Cpl
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Cpl
 * @package    Cpl_SocialConnect
 * @copyright  Copyright (c) 2022 Cpl (https://www.magento.com/)
 */

namespace Cpl\SocialConnect\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class CallbackUri
 * @package Cpl\SocialConnect\Block\Adminhtml\System\Config\Form
 */
class CallbackUri extends \Magento\Config\Block\System\Config\Form\Field
{
    
    /**
     * @var Data
     */
    protected $slHelper;

    /**
     * CallbackUri constructor.
     * @param Context   $context
     * @param Data      $slHelper
     * @param array     $data
     */    
    public function __construct(
        Context $context,
        Data $slHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->slHelper = $slHelper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $providerName = str_replace(['cpl_social_connect_', '_callback'], '', $element->getHtmlId());
        $urls = $this->slHelper->getCallbackUri($providerName, true);
        $htmlToReturn = '';
        foreach($urls as $url) {
            $htmlToReturn .= '<input id="'. $element->getHtmlId() .'" type="text" name="" value="'. $url .'" class="input-text" readonly="true" disabled="false" /><br/><br/>';
        }
        return $htmlToReturn;
    }
}