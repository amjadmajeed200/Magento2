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
 * Interface PickupConfigInterface
 * @package Mageplaza\StoreLocator\Api\Data
 */
interface PickupConfigInterface
{
    const STORES_MAP_URL       = 'stores_map_url';
    const LOCATION_SESSION_URL = 'location_session_url';
    const LOCATIONS_DATA       = 'locationsData';
    const PICKUP_AFTER_DAYS    = 'pickupAfterDays';

    /**
     * @return string
     */
    public function getStoreMapUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreMapUrl($value);

    /**
     * @return string
     */
    public function getLocationSessionUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLocationSessionUrl($value);

    /**
     * @return \Mageplaza\StoreLocator\Api\Data\LocationDataInterface[]
     */
    public function getLocationsData();

    /**
     * @param \Mageplaza\StoreLocator\Api\Data\LocationDataInterface[] $value
     *
     * @return $this
     */
    public function setLocationsData($value);

    /**
     * @return string
     */
    public function getPickupAfterDays();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPickupAfterDays($value);
}
