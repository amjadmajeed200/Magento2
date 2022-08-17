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

namespace Mageplaza\StoreLocator\Api\Data;

/**
 * Interface LocationDataInterface
 * @package Mageplaza\StoreLocator\Api\Data
 */
interface LocationDataInterface
{
    const LOCATION_ID  = 'locationId';
    const NAME         = 'name';
    const COUNTRY_ID   = 'countryId';
    const REGION_ID    = 'regionId';
    const REGION       = 'region';
    const STREET       = 'street';
    const TELEPHONE    = 'telephone';
    const POSTCODE     = 'postcode';
    const CITY         = 'city';
    const TIME_DATA    = 'timeData';
    const HOLIDAY_DATA = 'holidayData';

    /**
     * @return string
     */
    public function getLocationId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLocationId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getCountryId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCountryId($value);

    /**
     * @return string
     */
    public function getRegionId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRegionId($value);

    /**
     * @return string
     */
    public function getRegion();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRegion($value);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStreet($value);

    /**
     * @return string
     */
    public function getTelephone();

    /**
     * @param string $value
     *
     * @return string
     */
    public function setTelephone($value);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPostcode($value);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCity($value);

    /**
     * @return string[]
     */
    public function getTimeData();

    /**
     * @param string[] $value
     *
     * @return $this
     */
    public function setTimeData($value);

    /**
     * @return string[]
     */
    public function getHolidayData();

    /**
     * @param string[] $value
     *
     * @return $this
     */
    public function setHolidayData($value);
}
