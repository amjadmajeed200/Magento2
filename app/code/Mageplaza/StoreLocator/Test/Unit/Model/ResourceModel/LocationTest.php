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

namespace Mageplaza\StoreLocator\Test\Unit\Model\ResourceModel;

use Exception;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location as LocationModel;
use Mageplaza\StoreLocator\Model\ResourceModel\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class LocationTest
 * @package Mageplaza\StoreLocator\Test\Unit\Model\ResourceModel
 */
class LocationTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    protected $context;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $_eventManager;

    /**
     * @var DateTime|MockObject
     */
    protected $_dateTime;

    /**
     * @var Data|MockObject
     */
    protected $_helperData;

    /**
     * @var RequestInterface|MockObject
     */
    protected $_request;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $_productCollectionFactory;

    /**
     * @var Product|MockObject
     */
    protected $_productResource;

    /**
     * @var MessageInterface|MockObject
     */
    protected $_messageManager;

    /**
     * @var ResourceConnection|MockObject
     */
    protected $_resources;

    /**
     * @var Location
     */
    private $object;

    /**
     * @var Session|MockObject
     */
    private $backendSession;

    public function testAdminInstance()
    {
        $this->assertInstanceOf(Location::class, $this->object);
    }

    /**
     * @return array[]
     */
    public function providerUpdatePickupLocationAttribute()
    {
        return [
            [1, '1&2&3'],
            [0, '1&2&3'],
            [1, ''],
            [0, ''],
        ];
    }

    /**
     * @param $saveConfig
     * @param $sessionProductIds
     *
     * @dataProvider providerUpdatePickupLocationAttribute
     */
    public function testUpdatePickupLocationAttribute($saveConfig, $sessionProductIds)
    {
        $check = true;
        /** @var LocationModel|MockObject $object */
        $object = $this->getMockBuilder(LocationModel::class)
            ->disableOriginalConstructor()->getMock();

        $locationData    = '1&2&3';
        $oldLocationData = '1&2';
        $this->_request->method('getParam')->with('product_ids')
            ->willReturn($locationData);
        $object->expects($this->once())->method('getProductIds')->willReturn($oldLocationData);
        $this->backendSession->expects($this->once())->method('getSaveConfig')->willReturn($saveConfig);
        if ($saveConfig) {
            $this->backendSession->expects($this->once())->method('unsSaveConfig')->willReturnSelf();
            $check = false;
        }

        $this->backendSession->expects($this->once())->method('getProductIds')->willReturn($sessionProductIds);
        if ($sessionProductIds) {
            $check        = false;
            $locationData = $sessionProductIds;
            $this->backendSession->expects($this->once())->method('unsProductIds')->willReturnSelf();
        }

        if ($locationData || $oldLocationData) {
            $object->expects($this->once())->method('getLocationId')->willReturn(1);
            $object->expects($this->once())->method('getUrlKey')->willReturn('home');
            $productCollection = $this->getMockBuilder(Collection::class)
                ->disableOriginalConstructor()->getMock();
            $this->_productCollectionFactory->method('create')->willReturn($productCollection);
            $productCollection->expects($this->once())->method('addAttributeToSelect')
                ->with('*')->willReturnSelf();
            $productCollection->expects($this->once())->method('addFieldToFilter')
                ->with('entity_id', ['in' => ['1', '2', '3']])->willReturnSelf();
            $product = $this->getMockBuilder(ProductModel::class)
                ->disableOriginalConstructor()->getMock();
            $productCollection->expects($this->once())->method('getItems')
                ->willReturn([$product, $product]);

            $product->expects($this->exactly(2))->method('getData')
                ->withConsecutive(['mp_pickup_locations'])
                ->willReturn('home-1,mageplaza-2', 'mageplaza-2');
            $product->expects($this->exactly(2))->method('setData')
                ->withConsecutive(
                    ['mp_pickup_locations', 'home-1,mageplaza-2'],
                    ['mp_pickup_locations', 'mageplaza-2,home-1']
                )->willReturnSelf();
            $this->_productResource->expects($this->exactly(2))->method('saveAttribute')
                ->with($product, 'mp_pickup_locations')->willReturnSelf();

            if ($check) {
                $object->expects($this->once())->method('getIsSelectedAllProduct')->willReturn('1');
                $productCollection->expects($this->once())->method('getAllIds')
                    ->willReturn([]);
            }
        }

        $this->object->updatePickupLocationAttribute($object);
    }

    /**
     * @return array
     */
    public function providerUncheckProductGrid()
    {
        return [
            ['1'],
            [''],
            [1],
        ];
    }

    /**
     * @param $isSelectedAll
     *
     * @dataProvider providerUncheckProductGrid
     */
    public function testUncheckProductGrid($isSelectedAll)
    {
        $productIds          = '2&3';
        $oldProductIds       = ['1', '2', '3'];
        $oldProductIdsString = '1&2&3';
        $diffProductIds      = ['1'];
        $this->_request->method('getParam')->with('product_ids')
            ->willReturn($productIds);
        /** @var LocationModel|MockObject $object */
        $object = $this->getMockBuilder(LocationModel::class)
            ->disableOriginalConstructor()->getMock();
        $object->expects($this->once())->method('getIsSelectedAllProduct')->willReturn($isSelectedAll);
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->_productCollectionFactory->expects($this->once())->method('create')->willReturn($collection);
        if ($isSelectedAll === '1') {
            $this->_productCollectionFactory->expects($this->once())
                ->method('create')->willReturn($collection);
            $collection->expects($this->once())->method('getAllIds')
                ->willReturn($oldProductIds);
        } else {
            $object->expects($this->once())->method('getData')
                ->with('product_ids')->willReturn($oldProductIdsString);
        }

        $this->_request->expects($this->once())->method('getFullActionName')
            ->willReturn('');
        $collection->expects($this->once())->method('addAttributeToSelect')
            ->with('*')->willReturnSelf();
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('entity_id', ['in' => $diffProductIds])->willReturnSelf();

        $product = $this->getMockBuilder(ProductModel::class)
            ->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('getItems')
            ->willReturn([$product, $product]);

        $product->expects($this->exactly(2))->method('getData')
            ->with('mp_pickup_locations')
            ->willReturnOnConsecutiveCalls('home-1,mageplaza-2', 'home-1');

        $product->expects($this->exactly(2))->method('setData')
            ->withConsecutive(
                ['mp_pickup_locations', 'mageplaza-2'],
                ['mp_pickup_locations', '0']
            )
            ->willReturnSelf();
        $this->_productResource->expects($this->exactly(2))->method('saveAttribute')
            ->with($product, 'mp_pickup_locations')->willReturnSelf();

        $this->object->uncheckProductGrid($object, 'home-1');
    }

    public function testSaveAttributeWithException()
    {
        $product   = $this->getMockBuilder(ProductModel::class)
            ->disableOriginalConstructor()->getMock();
        $attribute = 'mp_pickup_locations';
        $this->_productResource->expects($this->once())->method('saveAttribute')
            ->willThrowException(new Exception('Exception Message'));
        $this->_messageManager->expects($this->once())->method('addErrorMessage')
            ->with('Exception Message')->willReturnSelf();

        $this->object->saveAttribute($product, $attribute);
    }

    /**
     * @throws LocalizedException
     */
    public function testSaveHolidayRelationWithNoHolidaysAndHolidayGridFalse()
    {
        $locationId = '1';
        $holidays   = null;
        /** @var LocationModel|MockObject $location */
        $location = $this->getMockBuilder(LocationModel::class)
            ->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('setIsChangedHolidayList')
            ->with(false)->willReturnSelf();
        $location->expects($this->once())->method('getId')->willReturn($locationId);
        $location->expects($this->once())->method('getHolidaysIds')->willReturn($holidays);
        $location->expects($this->once())->method('getIsHolidayGrid')->willReturn(false);

        $this->object->saveHolidayRelation($location);
    }

    /**
     * @return array[]
     */
    public function providerSaveHolidayRelation(): array
    {
        return [
            [null, 1, ['1'], [0], []],
            [
                [1 => '', 2 => ''],
                1,
                ['2', '3'],
                [0, 1],
                [
                    [
                        'location_id' => 1,
                        'holiday_id'  => 1
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $holidays
     * @param $isHolidayGrid
     * @param $oldHolidays
     * @param $holidayIds
     * @param $insertData
     *
     * @throws LocalizedException
     * @dataProvider providerSaveHolidayRelation
     */
    public function testSaveHolidayRelation($holidays, $isHolidayGrid, $oldHolidays, $holidayIds, $insertData)
    {
        $id = '1';
        /** @var LocationModel|MockObject $location */
        $location = $this->getMockBuilder(LocationModel::class)
            ->disableOriginalConstructor()->getMock();

        $location->expects($this->once())->method('getId')->willReturn($id);
        $location->expects($this->once())->method('getHolidaysIds')->willReturn($holidays);
        if ($holidays === null) {
            $location->expects($this->once())->method('getIsHolidayGrid')->willReturn($isHolidayGrid);
        }

        $location->expects($this->once())->method('getHolidayIds')->willReturn($oldHolidays);

        $holidays = $holidays ? array_keys($holidays) : [];
        $insert   = array_diff($holidays, $oldHolidays);
        $delete   = array_diff($oldHolidays, $holidays);

        $adapter = $this->getMockBuilder(AdapterInterface::class)
            ->disableArgumentCloning()->getMockForAbstractClass();
        $this->_resources->expects($this->once())->method('getConnection')
            ->with(ResourceConnection::DEFAULT_CONNECTION)
            ->willReturn($adapter);

        $this->_resources->method('getTableName')
            ->with('mageplaza_storelocator_location_holiday')
            ->willReturn('mageplaza_storelocator_location_holiday');

        if (!empty($delete)) {
            $condition = ['holiday_id IN(?)' => $delete, 'location_id=?' => $id];
            $adapter->expects($this->once())->method('delete')
                ->with('mageplaza_storelocator_location_holiday', $condition)
                ->willReturnSelf();
        }

        if (!empty($insert)) {
            $adapter->expects($this->once())->method('insertMultiple')
                ->with('mageplaza_storelocator_location_holiday', $insertData)
                ->willReturnSelf();
        }

        if (!empty($insert) || !empty($delete)) {
            $location->expects($this->exactly(2))->method('setIsChangedHolidayList')
                ->withConsecutive([false], [true])->willReturnSelf();
            $this->_eventManager->expects($this->once())->method('dispatch')
                ->with(
                    'mageplaza_storelocator_location_change_holidays',
                    ['location' => $location, 'holiday_ids' => $holidayIds]
                )->willReturnSelf();
            $location->expects($this->once())->method('setAffectedHolidayIds')
                ->with($holidayIds)->willReturnSelf();
        } else {
            $location->expects($this->once())->method('setIsChangedHolidayList')
                ->with(false)->willReturnSelf();
        }

        $this->object->saveHolidayRelation($location);
    }

    protected function setUp()
    {
        $this->context                   = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->_dateTime                 = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()->getMock();
        $this->_eventManager             = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_helperData               = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $requestMethods                  = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getFullActionName']
            )
        );
        $this->_request                  = $this->getMockBuilder(RequestInterface::class)
            ->setMethods($requestMethods)
            ->getMockForAbstractClass();
        $this->_productCollectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->_productResource          = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods                  = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['getSaveConfig', 'unsSaveConfig', 'getProductIds', 'unsProductIds']
            )
        );
        $this->backendSession            = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->_messageManager           = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_resources                = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()->getMock();
        $this->context->method('getResources')->willReturn($this->_resources);

        $this->object = new Location(
            $this->context,
            $this->_dateTime,
            $this->_eventManager,
            $this->_helperData,
            $this->_request,
            $this->_productResource,
            $this->_messageManager,
            $this->_productCollectionFactory,
            $this->backendSession
        );
    }
}
