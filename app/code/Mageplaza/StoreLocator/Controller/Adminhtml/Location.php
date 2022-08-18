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

namespace Mageplaza\StoreLocator\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\StoreLocator\Model\LocationFactory;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Controller\Adminhtml
 */
abstract class Location extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_StoreLocator::location';

    /**
     * Location model factory
     *
     * @var LocationFactory
     */
    public $locationFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Location constructor.
     *
     * @param LocationFactory $locationFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        LocationFactory $locationFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->locationFactory = $locationFactory;
        $this->coreRegistry    = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mageplaza\StoreLocator\Model\Location
     */
    protected function initLocation($register = false)
    {
        $locationId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\StoreLocator\Model\Location $location */
        $location = $this->locationFactory->create();

        if ($locationId) {
            $location->load($locationId);
            if (!$location->getId()) {
                $this->messageManager->addErrorMessage(__('This location no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_storelocator_location', $location);
        }
        $this->_getSession()->setData('mageplaza_storelocator_location_model', $location);

        return $location;
    }
}
