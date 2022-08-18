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
 * Interface LocationInterface
 * @package Mageplaza\StoreLocator\Api\Data
 */
interface LocationInterface
{
    const LOCATION_ID = 'locationId';
    const TIME_PICKUP = 'timePickup';

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
    public function getTimePickup();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTimePickup($value);
}
