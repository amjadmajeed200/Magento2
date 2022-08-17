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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;

/**
 * Class Locations
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Holiday
 */
class Locations extends Holiday
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Locations constructor.
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param HolidayFactory $holidayFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        HolidayFactory $holidayFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($holidayFactory, $coreRegistry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->initHoliday(true);

        return $this->resultLayoutFactory->create();
    }
}
