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
use Mageplaza\StoreLocator\Model\HolidayFactory;

/**
 * Class Holiday
 * @package Mageplaza\StoreLocator\Controller\Adminhtml
 */
abstract class Holiday extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_StoreLocator::holiday';

    /**
     * Location model factory
     *
     * @var HolidayFactory
     */
    public $holidayFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Holiday constructor.
     *
     * @param HolidayFactory $holidayFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        HolidayFactory $holidayFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->holidayFactory = $holidayFactory;
        $this->coreRegistry   = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Mageplaza\StoreLocator\Model\Holiday
     */
    protected function initHoliday($register = false)
    {
        $holidayId = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\StoreLocator\Model\Holiday $holiday */
        $holiday = $this->holidayFactory->create();

        if ($holidayId) {
            $holiday->load($holidayId);
            if (!$holiday->getId()) {
                $this->messageManager->addErrorMessage(__('This holiday no longer exists.'));

                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('mageplaza_storelocator_holiday', $holiday);
        }

        return $holiday;
    }
}
