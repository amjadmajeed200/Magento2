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

namespace Mageplaza\StoreLocator\Model\Api\Data;

use Magento\Framework\DataObject;
use Mageplaza\StoreLocator\Api\Data\LocationInterface;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Model\Api\Data
 */
class Location extends DataObject implements LocationInterface
{
    /**
     * @inheritDoc
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLocationId($value)
    {
        return $this->setData(self::LOCATION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTimePickup()
    {
        return $this->getData(self::TIME_PICKUP);
    }

    /**
     * @inheritDoc
     */
    public function setTimePickup($value)
    {
        return $this->setData(self::TIME_PICKUP, $value);
    }
}
