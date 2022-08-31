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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mageplaza\StoreLocator\Helper\Data;

/**
 * Class Time
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Import\Edit
 */
class Time extends Template
{
    const MONDAY    = 'operation_mon';
    const TUESDAY   = 'operation_tue';
    const WEDNESDAY = 'operation_wed';
    const THURSDAY  = 'operation_thu';
    const FRIDAY    = 'operation_fri';
    const SATURDAY  = 'operation_sat';
    const SUNDAY    = 'operation_sun';

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_StoreLocator::location/form/time.phtml';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Time constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Get operation time data in each day
     *
     * @return mixed
     */
    public function getOperationTime()
    {
        $data     = [];
        $location = $this->getLocationObject();
        switch ($this->getName()) {
            case self::MONDAY:
                $data = $location->getOperationMon() ?
                    $this->_helperData->jsDecode($location->getOperationMon()) : [];
                break;
            case self::TUESDAY:
                $data = $location->getOperationTue() ?
                    $this->_helperData->jsDecode($location->getOperationTue()) : [];
                break;
            case self::WEDNESDAY:
                $data = $location->getOperationWed() ?
                    $this->_helperData->jsDecode($location->getOperationWed()) : [];
                break;
            case self::THURSDAY:
                $data = $location->getOperationThu() ?
                    $this->_helperData->jsDecode($location->getOperationThu()) : [];
                break;
            case self::FRIDAY:
                $data = $location->getOperationFri() ?
                    $this->_helperData->jsDecode($location->getOperationFri()) : [];
                break;
            case self::SATURDAY:
                $data = $location->getOperationSat() ?
                    $this->_helperData->jsDecode($location->getOperationSat()) : [];
                break;
            case self::SUNDAY:
                $data = $location->getOperationSun() ?
                    $this->_helperData->jsDecode($location->getOperationSun()) : [];
                break;
        }

        return $data;
    }
}
