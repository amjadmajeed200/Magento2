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

namespace Mageplaza\StoreLocator\Observer;

use Exception;
use Magento\Backend\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class ActionProductSave
 * @package Mageplaza\StoreLocator\Observer
 */
class ActionProductSave implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * ActionProductSave constructor.
     *
     * @param CollectionFactory $locationCollectionFactory
     * @param Session $backendSession
     */
    public function __construct(
        CollectionFactory $locationCollectionFactory,
        Session $backendSession
    ) {
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->backendSession            = $backendSession;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $product         = $observer->getData('product');
        $pickupLocations = $product->getData('mp_pickup_locations');
        $selectLocation  = !$pickupLocations ? [] : explode(',', $pickupLocations);
        $productId       = $product->getData('entity_id');
        $collection      = $this->locationCollectionFactory->create();

        /** @var Location $location */
        foreach ($collection->getItems() as $location) {
            $name             = $location->getUrlKey() . '-' . $location->getId();
            $productIdsString = $location->getProductIds();
            $productIds       = empty($productIdsString) ? [] : explode('&', $productIdsString);

            if (count($selectLocation) && in_array($name, $selectLocation, true)) {
                if (count($productIds) && !in_array($productId, $productIds, true)) {
                    $productIds[]  = $productId;
                    $newProductIds = implode('&', $productIds);
                    $this->saveConfigLocator($location, $newProductIds);
                }
            } elseif (count($productIds) && in_array($productId, $productIds, true)) {
                $newProductIds = implode('&', array_diff($productIds, [$productId]));
                $this->saveConfigLocator($location, $newProductIds);
            }
        }
    }

    /**
     * @param Location $location
     * @param string $newProductIds
     *
     * @throws Exception
     */
    public function saveConfigLocator($location, $newProductIds)
    {
        $this->backendSession->setSaveConfig('1');
        $location->setProductIds($newProductIds);
        $location->save();
    }
}
