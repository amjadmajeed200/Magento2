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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Block\Product\View\AvailableStore;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AvailableStoreTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Product\View
 */
class AvailableStoreTest extends TestCase
{
    /**
     * @var AvailableStore
     */
    private $availableStoreBlock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $locationCollectionFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    /**
     * @var Frontend|MockObject
     */
    private $frontendMock;

    public function testGetAppliedLocationIds()
    {
        $this->productMock->expects($this->once())->method('getData')
            ->with('mp_pickup_locations')
            ->willReturn('home-1,mageplaza-2');
        $this->assertEquals(['1', '2'], $this->availableStoreBlock->getAppliedLocationIds());
    }

    public function testGetLocationsData()
    {
        $locationIds = [1];
        $collection  = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationCollectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $collection->expects($this->exactly(2))->method('addFieldToFilter')
            ->withConsecutive(['location_id', $locationIds], ['status', 1])
            ->willReturnSelf();
        $this->frontendMock->expects($this->once())->method('filterLocation')->with($collection)
            ->willReturn([$location]);
        $location->expects($this->once())->method('getIsShowProductPage')->willReturn(1);
        $location->expects($this->once())->method('getName')->willReturn('name');
        $location->expects($this->once())->method('getStreet')->willReturn('street');
        $location->expects($this->once())->method('getStateProvince')->willReturn('state_province');
        $location->expects($this->once())->method('getCity')->willReturn('city');
        $location->expects($this->once())->method('getCountry')->willReturn('country');

        $result = [
            [
                'name'    => 'name',
                'address' => 'street state_province, city, country'
            ]
        ];

        $this->assertEquals($result, $this->availableStoreBlock->getLocationsData($locationIds));
    }

    public function testGetLocationsDataWithException()
    {
        $locationIds = [1];
        $collection  = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationCollectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);
        $collection->expects($this->exactly(2))->method('addFieldToFilter')
            ->withConsecutive(['location_id', $locationIds], ['status', 1])
            ->willReturnSelf();
        $this->frontendMock->expects($this->once())->method('filterLocation')->with($collection)
            ->willThrowException(new NoSuchEntityException(__('No such entity')));
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('No such entity'))
            ->willReturnSelf();

        $this->assertEquals([], $this->availableStoreBlock->getLocationsData($locationIds));
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
        $this->frontendMock                  = $this->getMockBuilder(Frontend::class)
            ->disableOriginalConstructor()->getMock();

        $this->productMock        = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $objectManagerHelper = new ObjectManager($this);

        $this->availableStoreBlock = $objectManagerHelper->getObject(
            AvailableStore::class,
            [
                'context'           => $contextMock,
                'collectionFactory' => $this->locationCollectionFactoryMock,
                '_frontend'         => $this->frontendMock,
                'messageManager'    => $this->messageManagerMock,
                'data'              => [
                    'product' => $this->productMock
                ]
            ]
        );
    }
}
