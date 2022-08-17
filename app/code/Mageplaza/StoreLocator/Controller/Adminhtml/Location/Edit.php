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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Edit
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class Edit extends Location
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param LocationFactory $locationFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LocationFactory $locationFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($locationFactory, $registry, $context);
    }

    /**
     * @return Page|ResponseInterface|Redirect|ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Mageplaza\StoreLocator\Model\Location $location */
        $location = $this->initLocation();
        if (!$location) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_storelocator_location_data', true);
        if (!empty($data)) {
            $location->setData($data);
        }

        $this->coreRegistry->register('mageplaza_storelocator_location', $location);

        /** @var Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_StoreLocator::location');
        $resultPage->getConfig()->getTitle()->set(__('Manage General Locations'));

        $title = $location->getId() ? $location->getName() : __('New Location');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
