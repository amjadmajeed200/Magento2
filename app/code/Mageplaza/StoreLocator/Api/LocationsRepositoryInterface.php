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
 * Interface LocationsRepositoryInterface
 * @package Mageplaza\StoreLocator\Api
 */
interface LocationsRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\StoreLocator\Api\Data\LocationsSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\StoreLocator\Api\Data\LocationsSearchResultInterface
     */
    public function getLocations(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @return \Mageplaza\StoreLocator\Api\Data\DataConfigLocationInterface
     */
    public function getDataConfigLocation();

    /**
     * @param string|null $storeId
     *
     * @return string
     */
    public function getMapTemplate($storeId = null);

    /**
     * @param string|null $storeId
     *
     * @return \Mageplaza\StoreLocator\Api\Data\PickupConfigInterface
     */
    public function getPickupData($storeId = null);

    /**
     * @param \Mageplaza\StoreLocator\Api\Data\LocationInterface $location
     *
     * @return string
     */
    public function saveLocation($location);

    /**
     * @return string locationId
     */
    public function getLocationId();
}
