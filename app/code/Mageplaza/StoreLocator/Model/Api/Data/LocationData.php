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
use Mageplaza\StoreLocator\Api\Data\LocationDataInterface;

/**
 * Class LocationData
 * @package Mageplaza\StoreLocator\Model\Api\Data
 */
class LocationData extends DataObject implements LocationDataInterface
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCountryId($value)
    {
        return $this->setData(self::COUNTRY_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($value)
    {
        return $this->setData(self::REGION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion($value)
    {
        return $this->setData(self::REGION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($value)
    {
        return $this->setData(self::STREET, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * @inheritDoc
     */
    public function setTelephone($value)
    {
        return $this->setData(self::TELEPHONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode($value)
    {
        return $this->setData(self::POSTCODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($value)
    {
        return $this->setData(self::CITY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTimeData()
    {
        return $this->getData(self::TIME_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setTimeData($value)
    {
        return $this->setData(self::TIME_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHolidayData()
    {
        return $this->getData(self::HOLIDAY_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setHolidayData($value)
    {
        return $this->setData(self::HOLIDAY_DATA, $value);
    }
}
