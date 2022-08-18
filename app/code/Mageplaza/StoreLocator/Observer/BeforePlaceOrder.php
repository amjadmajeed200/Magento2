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
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;

/**
 * Class BeforePlaceOrder
 * @package Mageplaza\NameYourPrice\Observer
 */
class BeforePlaceOrder implements ObserverInterface
{
    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var LocationResource
     */
    protected $_locationResource;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * BeforePlaceOrder constructor.
     *
     * @param Data $helperData
     * @param LocationResource $locationResource
     * @param LocationFactory $locationFactory
     * @param Session $checkoutSession
     */
    public function __construct(
        Data $helperData,
        LocationResource $locationResource,
        LocationFactory $locationFactory,
        Session $checkoutSession
    ) {
        $this->_helperData       = $helperData;
        $this->_locationResource = $locationResource;
        $this->_locationFactory  = $locationFactory;
        $this->_checkoutSession  = $checkoutSession;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order          = $observer->getEvent()->getData('order');
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod === 'mpstorepickup_mpstorepickup') {
            $locationId = $this->_checkoutSession->getLocationIdSelected();
            $pickupTime = $this->_checkoutSession->getPickupTime();
            $location   = $this->_locationFactory->create();
            $this->_locationResource->load($location, $locationId);
            $shippingDescription = $this->_helperData->getPickupMethodName();
            $order->setShippingDescription($shippingDescription)->save();
            $order->setMpTimePickup($pickupTime);

            $shippingData = [
                'prefix'               => '',
                'firstname'            => $location->getName(),
                'middlename'           => '',
                'lastname'             => ',',
                'suffix'               => '',
                'company'              => '',
                'street'               => $location->getStreet(),
                'city'                 => $location->getCity(),
                'region'               => $location->getStateProvince(),
                'region_id'            => 0,
                'postcode'             => $location->getPostalCode(),
                'country_id'           => $location->getCountry(),
                'telephone'            => $location->getPhoneOne() ?: '0',
                'fax'                  => '',
                'email'                => $location->getEmail() ?: 'no@email.com',
                'save_in_address_book' => false
            ];

            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress) {
                $shippingAddress->addData($shippingData)->save();
            }
        }
    }
}
