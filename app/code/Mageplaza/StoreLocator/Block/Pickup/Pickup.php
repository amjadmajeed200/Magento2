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

namespace Mageplaza\StoreLocator\Block\Pickup;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Mageplaza\StoreLocator\Block\Frontend;

/**
 * Class Pickup
 * @package Mageplaza\StoreLocator\Block\Pickup
 */
class Pickup extends Frontend
{
    /**
     * @return string
     */
    public function getPickupData()
    {
        $params = [];
        try {
            $params = [
                'stores_map_url'       => $this->getStoreMapUrl(),
                'location_session_url' => $this->getLocationSessionUrl(),
                'locationsData'        => $this->getLocationsData(),
                'pickupAfterDays'      => $this->_helperData->getAvailableProduct() ?: 0
            ];
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_helperData->jsEncode($params);
    }

    /**
     * @return string
     */
    public function getStoreMapUrl()
    {
        return $this->getUrl('mpstorelocator/storelocator/store');
    }

    /**
     * @return string
     */
    public function getLocationSessionUrl()
    {
        return $this->getUrl('mpstorelocator/storepickup/saveLocationData');
    }

    /**
     * @return Phrase|null
     */
    public function getNoticeAvailable()
    {
        $days = $this->_helperData->getAvailableProduct();

        if ($days) {
            return __('Products is only available at store after %1 day(s).', $days);
        }

        return null;
    }
}
