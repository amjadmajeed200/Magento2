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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\Holidays as RendererHolidays;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Holidays
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class Holidays extends Location
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * JS helper
     *
     * @var Js
     */
    protected $_jsonHelper;

    /**
     * Holidays constructor.
     *
     * @param PageFactory $pageFactory
     * @param JsonHelper $jsonHelper
     * @param LocationFactory $locationFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        PageFactory $pageFactory,
        JsonHelper $jsonHelper,
        LocationFactory $locationFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($locationFactory, $coreRegistry, $context);

        $this->_jsonHelper  = $jsonHelper;
        $this->_pageFactory = $pageFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->initLocation(true);

        $page = $this->_pageFactory->create();
        $html = $page->getLayout()
            ->createBlock(RendererHolidays::class)->toHtml();
        if ($this->getRequest()->getParam('loadGrid')) {
            $html = $this->_jsonHelper->jsonEncode($html);
        }

        return $this->getResponse()->representJson($html);
    }
}
