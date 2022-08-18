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
use Mageplaza\StoreLocator\Api\Data\PickupConfigInterface;

/**
 * Class PickupConfig
 * @package Mageplaza\StoreLocator\Model\Api\Data
 */
class PickupConfig extends DataObject implements PickupConfigInterface
{

    /**
     * @inheritDoc
     */
    public function getStoreMapUrl()
    {
        return $this->getData(self::STORES_MAP_URL);
    }

    /**
     * @inheritDoc
     */
    public function setStoreMapUrl($value)
    {
        return $this->setData(self::STORES_MAP_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLocationSessionUrl()
    {
        return $this->getData(self::LOCATION_SESSION_URL);
    }

    /**
     * @inheritDoc
     */
    public function setLocationSessionUrl($value)
    {
        return $this->setData(self::LOCATION_SESSION_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLocationsData()
    {
        return $this->getData(self::LOCATIONS_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setLocationsData($value)
    {
        return $this->setData(self::LOCATIONS_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPickupAfterDays()
    {
        return $this->getData(self::PICKUP_AFTER_DAYS);
    }

    /**
     * @inheritDoc
     */
    public function setPickupAfterDays($value)
    {
        return $this->setData(self::PICKUP_AFTER_DAYS, $value);
    }
}
