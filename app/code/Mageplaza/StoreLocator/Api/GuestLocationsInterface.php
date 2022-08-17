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

namespace Mageplaza\StoreLocator\Api;

/**
 * Interface GuestLocationsInterface
 * @package Mageplaza\StoreLocator\Api
 */
interface GuestLocationsInterface
{
    /**
     * @param string $cartId
     *
     * @return \Mageplaza\StoreLocator\Api\Data\LocationsSearchResultInterface
     */
    public function getLocations($cartId);
}