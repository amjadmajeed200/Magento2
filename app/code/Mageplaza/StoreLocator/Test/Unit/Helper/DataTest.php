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

namespace Mageplaza\StoreLocator\Test\Unit\Helper\Data;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;
use Mageplaza\StoreLocator\Model\Carrier\Shipping;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DataTest
 * @package Mageplaza\StoreLocator\Test\Unit\Helper\Data
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $object;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * @var TranslitUrl|MockObject
     */
    private $translitUrlMock;

    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutMock;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystemMock;

    /**
     * @var DirectoryList|MockObject
     */
    private $directoryListMock;

    /**
     * @var LocationFactory|MockObject
     */
    private $locationFactoryMock;

    /**
     * @var Image|MockObject
     */
    private $imageHelperMock;

    /**
     * @var Shipping|MockObject
     */
    private $shippingMock;

    /**
     * @var Json|MockObject
     */
    private $jsonMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    public function testGetPageUrl()
    {
        $storeMethods = array_unique(array_merge(
            get_class_methods(StoreInterface::class),
            ['getBaseUrl']
        ));
        $store        = $this->getMockForAbstractClass(
            StoreInterface::class,
            [],
            '',
            false,
            false,
            false,
            $storeMethods
        );
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($store);
        $store->expects($this->once())->method('getBaseUrl')->willReturn('base_url/');

        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->exactly(2))->method('getValue')
            ->withConsecutive(['storelocator/general/url_key'], ['catalog/seo/category_url_suffix'])
            ->willReturn('find-a-store', '.html');

        $this->assertEquals('base_url/find-a-store.html', $this->object->getPageUrl());
    }

    public function testGetPageUrlWithException()
    {
        $this->storeManagerMock->expects($this->once())->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->exactly(2))->method('getValue')
            ->withConsecutive(['storelocator/general/url_key'], ['catalog/seo/category_url_suffix'])
            ->willReturnOnConsecutiveCalls('find-a-store', '.html');

        $this->assertEquals('find-a-store.html', $this->object->getPageUrl());
    }

    public function testGetStoreLocatorUrl()
    {
        //call only once without $urlKey and $type
        $urlKey = null;
        $type   = null;
        $state  = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with('storelocator/general/url_key')
            ->willReturn('find-a-store');
        $this->urlBuilderMock->expects($this->once())->method('getUrl')
            ->with('find-a-store/')
            ->willReturn('url/');

        $this->assertEquals('url', $this->object->getStoreLocatorUrl());
    }

    /**
     * @return array[]
     */
    public function providerCanShowLink()
    {
        return [
            [1, '1,2,3', true],
            [1, '2,3', false],
        ];
    }

    /**
     * @param $position
     * @param $positionConfig
     * @param $result
     *
     * @dataProvider providerCanShowLink
     */
    public function testCanShowLink($position, $positionConfig, $result)
    {
        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with('storelocator/general/show_on')
            ->willReturn($positionConfig);

        $this->assertEquals($result, $this->object->canShowLink($position));
    }

    public function testGenerateUrlKeyCreateNew()
    {
        $this->translitUrlMock->expects($this->once())->method('filter')
            ->with('name')->willReturn('name');

        /** @var LocationResource|MockObject $resource */
        $resource = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()->getMock();
        $adapter  = $this->getMockForAbstractClass(AdapterInterface::class);
        $resource->expects($this->once())->method('getConnection')->willReturn($adapter);
        $select = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()->getMock();
        $adapter->expects($this->once())->method('select')->willReturn($select);
        $resource->expects($this->once())->method('getMainTable')->willReturn('mageplaza_storelocator_location');
        $select->expects($this->once())->method('from')->with('mageplaza_storelocator_location')->willReturnSelf();
        $select->expects($this->once())->method('where')->with('url_key = :url_key')->willReturnSelf();
        $object = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $object->expects($this->once())->method('getId')->willReturn(null);
        $adapter->expects($this->once())->method('fetchOne')
            ->with($select, ['url_key' => 'name'])
            ->willReturn('');

        $this->assertEquals('name', $this->object->generateUrlKey($resource, $object, 'name'));
    }

    public function testGenerateUrlKeyEdit()
    {
        $this->translitUrlMock->expects($this->once())->method('filter')
            ->with('name')->willReturn('name');

        /** @var LocationResource|MockObject $resource */
        $resource = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()->getMock();
        $adapter  = $this->getMockForAbstractClass(AdapterInterface::class);
        $resource->expects($this->once())->method('getConnection')->willReturn($adapter);
        $select = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()->getMock();
        $adapter->expects($this->once())->method('select')->willReturn($select);
        $resource->expects($this->once())->method('getMainTable')->willReturn('mageplaza_storelocator_location');
        $select->expects($this->once())->method('from')->with('mageplaza_storelocator_location')->willReturnSelf();
        $resource->expects($this->once())->method('getIdFieldName')->willReturn('location_id');
        $select->expects($this->exactly(2))->method('where')
            ->withConsecutive(['url_key = :url_key'], ['location_id != :object_id'])->willReturnSelf();
        $object = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $object->expects($this->once())->method('getId')->willReturn(1);
        $adapter->expects($this->once())->method('fetchOne')
            ->with($select, ['url_key' => 'name', 'object_id' => 1])
            ->willReturn('');

        $this->assertEquals('name', $this->object->generateUrlKey($resource, $object, 'name'));
    }

    public function testGenerateUrlKeyThrowException()
    {
        $this->translitUrlMock->expects($this->exactly(11))->method('filter')
            ->with('name')->willReturn('name');

        /** @var LocationResource|MockObject $resource */
        $resource = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()->getMock();
        $adapter  = $this->getMockForAbstractClass(AdapterInterface::class);
        $resource->expects($this->exactly(11))->method('getConnection')->willReturn($adapter);
        $select = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()->getMock();
        $adapter->expects($this->exactly(11))->method('select')->willReturn($select);
        $resource->expects($this->exactly(11))->method('getMainTable')->willReturn('mageplaza_storelocator_location');
        $select->expects($this->exactly(11))->method('from')->with('mageplaza_storelocator_location')->willReturnSelf();
        $select->expects($this->exactly(11))->method('where')->with('url_key = :url_key')->willReturnSelf();
        $object = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $object->expects($this->exactly(11))->method('getId')->willReturn(null);
        $adapter->expects($this->exactly(11))->method('fetchOne')
            ->withConsecutive(
                [$select, ['url_key' => 'name']],
                [$select, ['url_key' => 'name1']],
                [$select, ['url_key' => 'name2']],
                [$select, ['url_key' => 'name3']],
                [$select, ['url_key' => 'name4']],
                [$select, ['url_key' => 'name5']],
                [$select, ['url_key' => 'name6']],
                [$select, ['url_key' => 'name7']],
                [$select, ['url_key' => 'name8']],
                [$select, ['url_key' => 'name9']],
                [$select, ['url_key' => 'name10']]
            )->willReturnOnConsecutiveCalls(
                '123',
                '123',
                '123',
                '123',
                '123',
                '123',
                '123',
                '123',
                '123',
                '123',
                '123'
            );
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Unable to generate url key. Please check the setting and try again.');

        $this->object->generateUrlKey($resource, $object, 'name');
    }

    public function testGetConfigOpenTime()
    {
        $code  = 'code';
        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with('storelocator/time_default/code')
            ->willReturn('[]');

        $this->jsonMock->expects($this->once())->method('unserialize')
            ->with('[]')
            ->willReturn([]);

        $this->jsonMock->expects($this->once())->method('serialize')
            ->with([
                'use_system_config' => 1
            ])
            ->willReturn('');
        $this->object->getConfigOpenTime($code);
    }

    /**
     * @return array
     */
    public function providerLocationIdFromRouter()
    {
        return [
            ['mpstorelocator/storelocator/view/1', '1'],
            ['mpstorelocator/storelocator/store', false],
        ];
    }

    /**
     * @param $pathInfo
     * @param $result
     *
     * @dataProvider providerLocationIdFromRouter
     */
    public function testGetLocationIdFromRouter($pathInfo, $result)
    {
        $this->requestMock->expects($this->once())->method('getPathInfo')
            ->willReturn($pathInfo);

        $this->assertEquals($result, $this->object->getLocationIdFromRouter());
    }

    public function testGetAllLocationUrl()
    {
        $state = $this->getMockBuilder(State::class)->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->method('get')
            ->with(State::class)->willReturn($state);
        $state->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with('storelocator/general/url_key')
            ->willReturn('find-a-store');

        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $locations = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getCollection')->willReturn($locations);
        $locations->expects($this->once())->method('getItems')->willReturn([$location]);
        $location->expects($this->once())->method('getUrlKey')->willReturn('url-key');

        $this->assertEquals(['find-a-store', 'url-key'], $this->object->getAllLocationUrl());
    }

    public function testGetMessagesHtml()
    {
        $messageBlock = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()->getMock();
        $this->layoutMock->expects($this->once())->method('createBlock')
            ->with(Messages::class)->willReturn($messageBlock);
        $messageBlock->expects($this->once())->method('adderror')
            ->with('message')
            ->willReturnSelf();
        $messageBlock->expects($this->once())->method('toHtml')->willReturn('message-html');

        $this->object->getMessagesHtml('adderror', 'message');
    }

    public function testGetDefaultStoreLocation()
    {
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $locations = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getCollection')->willReturn($locations);
        $locations->expects($this->once())->method('addFieldToFilter')
            ->with('is_default_store', 1)
            ->willReturnSelf();
        $locations->expects($this->once())->method('setOrder')
            ->with('sort_order', 'ASC')
            ->willReturnSelf();
        $locations->expects($this->once())->method('getFirstItem')->willReturn($location);

        $this->assertEquals($location, $this->object->getDefaultStoreLocation());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock               = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock   = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $this->storeManagerMock    = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->messageManagerMock  = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->translitUrlMock     = $this->getMockBuilder(TranslitUrl::class)
            ->disableOriginalConstructor()->getMock();
        $this->layoutMock          = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->filesystemMock      = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $this->directoryListMock   = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock = $this->getMockBuilder(LocationFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->imageHelperMock     = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()->getMock();
        $this->shippingMock        = $this->getMockBuilder(Shipping::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonMock            = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()->getMock();
        $this->scopeConfigMock     = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->urlBuilderMock      = $this->getMockForAbstractClass(UrlInterface::class);
        $requestMethods            = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPathInfo']
            )
        );
        $this->requestMock         = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        $contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->object = new Data(
            $contextMock,
            $this->objectManagerMock,
            $this->storeManagerMock,
            $this->messageManagerMock,
            $this->translitUrlMock,
            $this->layoutMock,
            $this->filesystemMock,
            $this->directoryListMock,
            $this->locationFactoryMock,
            $this->imageHelperMock,
            $this->shippingMock,
            $this->jsonMock
        );
    }
}
