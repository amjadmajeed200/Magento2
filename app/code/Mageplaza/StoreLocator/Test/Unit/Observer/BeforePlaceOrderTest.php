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

namespace Mageplaza\StoreLocator\Test\Unit\Observer;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;
use Mageplaza\StoreLocator\Observer\BeforePlaceOrder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BeforePlaceOrderTest
 * @package Mageplaza\StoreLocator\Test\Unit\Observer
 */
class BeforePlaceOrderTest extends TestCase
{
    /**
     * @var BeforePlaceOrder
     */
    private $object;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var LocationResource|MockObject
     */
    private $locationResourceMock;

    /**
     * @var LocationFactory|MockObject
     */
    private $locationFactoryMock;

    /**
     * @var Session|MockObject
     */
    private $checkoutSessionMock;

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        /** @var Observer|MockObject $observer */
        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->getMock();
        $event    = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $observer->expects($this->once())->method('getEvent')->willReturn($event);
        $orderMethods = array_unique(
            array_merge(
                get_class_methods(Order::class),
                ['setMpTimePickup']
            )
        );
        $order        = $this->getMockBuilder(Order::class)
            ->setMethods($orderMethods)
            ->disableOriginalConstructor()->getMock();
        $event->expects($this->once())->method('getData')->with('order')->willReturn($order);
        $order->expects($this->once())->method('getShippingMethod')->willReturn('mpstorepickup_mpstorepickup');
        $locationId = '1';
        $pickupTime = '2020-12-06';
        $this->checkoutSessionMock->expects($this->once())->method('getLocationIdSelected')->willReturn($locationId);
        $this->checkoutSessionMock->expects($this->once())->method('getPickupTime')->willReturn($pickupTime);

        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $this->locationResourceMock->expects($this->once())->method('load')
            ->with($location, $locationId)
            ->willReturn($location);
        $shippingDescription = 'Store Pickup';
        $this->helperDataMock->expects($this->once())->method('getPickupMethodName')->willReturn($shippingDescription);
        $order->expects($this->once())->method('setShippingDescription')
            ->with($shippingDescription)->willReturnSelf();
        $order->expects($this->once())->method('save')->willReturnSelf();
        $order->expects($this->once())->method('setMpTimePickup')
            ->with($pickupTime)->willReturnSelf();
        $location->expects($this->once())->method('getName')->willReturn('name');
        $location->expects($this->once())->method('getStreet')->willReturn('street');
        $location->expects($this->once())->method('getCity')->willReturn('city');
        $location->expects($this->once())->method('getStateProvince')->willReturn('region');
        $location->expects($this->once())->method('getPostalCode')->willReturn('postcode');
        $location->expects($this->once())->method('getCountry')->willReturn('country_id');
        $location->expects($this->once())->method('getPhoneOne')->willReturn('telephone');
        $location->expects($this->once())->method('getEmail')->willReturn('email');
        $shippingAddress = $this->getMockBuilder(Address::class)->disableOriginalConstructor()->getMock();
        $order->expects($this->once())->method('getShippingAddress')->willReturn($shippingAddress);
        $shippingData = [
            'prefix'               => '',
            'firstname'            => 'name',
            'middlename'           => '',
            'lastname'             => ',',
            'suffix'               => '',
            'company'              => '',
            'street'               => 'street',
            'city'                 => 'city',
            'region'               => 'region',
            'region_id'            => 0,
            'postcode'             => 'postcode',
            'country_id'           => 'country_id',
            'telephone'            => 'telephone',
            'fax'                  => '',
            'email'                => 'email',
            'save_in_address_book' => false
        ];
        $shippingAddress->expects($this->once())->method('addData')->with($shippingData)->willReturnSelf();
        $shippingAddress->expects($this->once())->method('save')->willReturnSelf();

        $this->object->execute($observer);
    }

    protected function setUp()
    {
        $this->helperDataMock       = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationResourceMock = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock  = $this->getMockBuilder(LocationFactory::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods             = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['getLocationIdSelected', 'getPickupTime']
            )
        );
        $this->checkoutSessionMock  = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            BeforePlaceOrder::class,
            [
                '_helperData'       => $this->helperDataMock,
                '_locationResource' => $this->locationResourceMock,
                '_locationFactory'  => $this->locationFactoryMock,
                '_checkoutSession'  => $this->checkoutSessionMock,
            ]
        );
    }
}
