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

namespace Mageplaza\StoreLocator\Block\Link;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Model\Config\Source\System\ShowOn;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class Header
 * @package Mageplaza\StoreLocator\Block\Link
 */
class Header extends Top
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Header constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        HttpContext $httpContext,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $helperData, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_helperData->isEnabled() || !$this->_helperData->canShowLink(ShowOn::TOP_LINK) || $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return '';
        }

        return parent::_toHtml();
    }
}
