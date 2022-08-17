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
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use Mageplaza\StoreLocator\Observer\ActionProductSave;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ActionProductSaveTest
 * @package Mageplaza\StoreLocator\Test\Unit\Observer
 */
class ActionProductSaveTest extends TestCase
{
    /**
     * @var ActionProductSave
     */
    private $object;

    /**
     * @var CollectionFactory|MockObject
     */
    private $locationCollectionFactoryMock;

    /**
     * @var Session|MockObject
     */
    private $backendSessionMock;

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        /** @var Observer|MockObject $observer */
        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->getMock();
        $product  = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $observer->expects($this->once())->method('getData')->with('product')->willReturn($product);
        $product->expects($this->exactly(2))->method('getData')
            ->withConsecutive(['mp_pickup_locations'], ['entity_id'])
            ->willReturn('home-1,mageplaza-2', '1');
        $collection = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $this->locationCollectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('getItems')->willReturn([$location, $location, $location]);

        $location->expects($this->exactly(3))->method('getUrlKey')
            ->willReturnOnConsecutiveCalls('home', 'mageplaza', 'back');
        $location->expects($this->exactly(3))->method('getId')
            ->willReturnOnConsecutiveCalls('1', '2', '3');
        $location->expects($this->exactly(3))->method('getProductIds')
            ->willReturnOnConsecutiveCalls('2&3', '1&2&3', '1&2&3');

        $this->backendSessionMock->method('setSaveConfig')->with('1')->willReturnSelf();
        $location->expects($this->exactly(2))->method('setProductIds')
            ->withConsecutive()->willReturnSelf();
        $location->expects($this->exactly(2))->method('save')->willReturnSelf();

        $this->object->execute($observer);
    }

    protected function setUp()
    {
        $this->locationCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods                      = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['setSaveConfig']
            )
        );
        $this->backendSessionMock            = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            ActionProductSave::class,
            [
                'locationCollectionFactory' => $this->locationCollectionFactoryMock,
                'backendSession'            => $this->backendSessionMock,
            ]
        );
    }
}
