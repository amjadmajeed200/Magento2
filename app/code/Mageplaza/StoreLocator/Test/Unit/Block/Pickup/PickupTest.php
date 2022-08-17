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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Pickup;

use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Block\Pickup\Pickup;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday as HolidayResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PickupTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Pickup
 */
class PickupTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var Pickup
     */
    private $pickupBlock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $locationCollectionFactoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var LocationResource|MockObject
     */
    private $locationResourceMock;

    /**
     * @var HolidayFactory|MockObject
     */
    private $holidayFactoryMock;

    /**
     * @var HolidayResource|MockObject
     */
    private $holidayResourceMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    public function testGetStoreMapUrl()
    {
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('mpstorelocator/storelocator/store')
            ->willReturn('store-map-url');

        $this->assertEquals('store-map-url', $this->pickupBlock->getStoreMapUrl());
    }

    public function testGetPickupData()
    {
        $this->urlBuilderMock->expects($this->exactly(2))->method('getUrl')
            ->withConsecutive(['mpstorelocator/storelocator/store'], ['mpstorelocator/storepickup/saveLocationData'])
            ->willReturnOnConsecutiveCalls('store-map-url', 'save-location-url');

        $locationCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();

        $this->locationCollectionFactoryMock->method('create')->willReturn($locationCollection);

        $locationCollection->expects($this->once())->method('addFieldToFilter')
            ->with('status', 1)
            ->willReturnSelf();

        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($store);

        $storeId = '1';
        $store->expects($this->once())->method('getId')->willReturn($storeId);

        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        $locationCollection->expects($this->once())->method('getItems')->willReturn([$location]);

        $location->method('getStoreIds')->willReturn('1,2,3');

        $locationId = 1;

        $location->method('getId')->willReturn($locationId);
        $location->method('getName')->willReturn('Location');
        $this->escaperMock->expects($this->once())->method('escapeHtml')->with('Location')->willReturn('Location');
        $location->method('getCountry')->willReturn('Country');
        $location->method('getStateProvince')->willReturn('State Province');
        $location->method('getStreet')->willReturn('123');
        $location->method('getPhoneOne')->willReturn('123');
        $location->method('getPostalCode')->willReturn('123');
        $location->method('getCity')->willReturn('City');

        $operation = '{"value":"1","from":["08","00"],"to":["17","30"],"use_system_config":1}';
        $location->method('getOperationSun')->willReturn($operation);
        $location->method('getOperationMon')->willReturn($operation);
        $location->method('getOperationTue')->willReturn($operation);
        $location->method('getOperationWed')->willReturn($operation);
        $location->method('getOperationThu')->willReturn($operation);
        $location->method('getOperationFri')->willReturn($operation);
        $location->method('getOperationSat')->willReturn($operation);
        $operationData = [
            'value'             => '1',
            'form'              => ['08', '00'],
            'to'                => ['17', '30'],
            'use_system_config' => 1
        ];
        $this->helperDataMock
            ->method('jsDecode')
            ->with($operation)
            ->willReturn($operationData);

        $timeData = [
            0 => $operationData,
            1 => $operationData,
            2 => $operationData,
            3 => $operationData,
            4 => $operationData,
            5 => $operationData,
            6 => $operationData,
        ];

        $holidayIds = [1];

        $this->locationResourceMock->expects($this->once())
            ->method('getHolidayIdsByLocation')
            ->with($locationId)->willReturn($holidayIds);

        $holidayMethods = array_merge(
            get_class_methods(Holiday::class),
            [
                'getStatus',
                'getFrom',
                'getTo'
            ]
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->method('create')->willReturn($holiday);

        $this->holidayResourceMock->method('load')->with($holiday, 1)->willReturn($holiday);

        $from = '2020-05-27 07:00:00';
        $to   = '2020-05-27 07:00:00';
        $holiday->method('getStatus')->willReturn(1);
        $holiday->method('getFrom')->willReturn($from);
        $holiday->method('getTo')->willReturn($to);

        $holidayData = [
            1 => [
                'from' => $from,
                'to'   => $to,
            ]
        ];

        $locationsData = [
            'name'        => 'Location',
            'countryId'   => 'Country',
            'regionId'    => '0',
            'region'      => 'State Province',
            'street'      => '123',
            'telephone'   => '123',
            'postcode'    => '123',
            'city'        => 'City',
            'timeData'    => $timeData,
            'holidayData' => $holidayData
        ];

        $params = [
            'stores_map_url'       => 'store-map-url',
            'location_session_url' => 'save-location-url',
            'locationsData'        => [$locationId => $locationsData],
            'pickupAfterDays'      => '3'
        ];

        $this->helperDataMock->expects($this->once())->method('getAvailableProduct')->willReturn('3');
        $this->helperDataMock->expects($this->once())->method('jsEncode')
            ->with($params)
            ->willReturn('encodeValue');

        $this->assertEquals('encodeValue', $this->pickupBlock->getPickupData());
    }

    public function testGetPickupDataWithException()
    {
        $this->urlBuilderMock->expects($this->exactly(2))->method('getUrl')
            ->withConsecutive(['mpstorelocator/storelocator/store'], ['mpstorelocator/storepickup/saveLocationData'])
            ->willReturnOnConsecutiveCalls('store-map-url', 'save-location-url');

        $locationCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();

        $this->locationCollectionFactoryMock->method('create')->willReturn($locationCollection);

        $locationCollection->expects($this->once())->method('addFieldToFilter')
            ->with('status', 1)
            ->willReturnSelf();

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException(__('No such entity')));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('No such entity'))
            ->willReturnSelf();

        $this->helperDataMock->expects($this->once())->method('jsEncode')
            ->with([])
            ->willReturn('[]');

        $this->assertEquals('[]', $this->pickupBlock->getPickupData());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                         = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->locationCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock              = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->messageManagerMock            = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->helperDataMock                = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationResourceMock          = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->holidayFactoryMock            = $this->getMockBuilder(HolidayFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->holidayResourceMock           = $this->getMockBuilder(HolidayResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlBuilderMock                = $this->getMockForAbstractClass(UrlInterface::class);
        $this->escaperMock                   = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $contextMock->method('getEscaper')->willReturn($this->escaperMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->pickupBlock = $objectManagerHelper->getObject(
            Pickup::class,
            [
                'context'             => $contextMock,
                '_locationColFactory' => $this->locationCollectionFactoryMock,
                '_storeManager'       => $this->storeManagerMock,
                '_helperData'         => $this->helperDataMock,
                '_locationResource'   => $this->locationResourceMock,
                'holidayFactory'      => $this->holidayFactoryMock,
                'messageManager'      => $this->messageManagerMock,
            ]
        );
    }
}
