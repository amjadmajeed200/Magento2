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

namespace Mageplaza\StoreLocator\Test\Unit\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url;
use Mageplaza\StoreLocator\Controller\Router;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RouterTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller
 */
class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $object;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var ActionFactory|MockObject
     */
    private $actionFactoryMock;

    public function testMatchWithWrongUrlSuffix()
    {
        $requestMethods = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPathInfo']
            )
        );
        /** @var RequestInterface|MockObject $request */
        $request = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $request->expects($this->once())->method('getPathInfo')->willReturn('/find-a-store');
        $this->helperDataMock->expects($this->once())->method('getUrlSuffix')->willReturn('html');

        $this->assertNull($this->object->match($request));
    }

    /**
     * @return array[]
     */
    public function providerExecuteReturnNull()
    {
        return [
            ['/find-a-store.html', 0, ['home']],
            ['/find-a-store.html', 1, ['home']],
            ['/find-a-store.html/abc', 1, ['home']],
        ];
    }

    /**
     * @dataProvider providerExecuteReturnNull
     *
     * @param $pathInfo
     * @param $isEnabled
     * @param $allLocationUrl
     */
    public function testMatchWithNull($pathInfo, $isEnabled, $allLocationUrl)
    {
        $requestMethods = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPathInfo']
            )
        );
        /** @var RequestInterface|MockObject $request */
        $request = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $request->expects($this->once())->method('getPathInfo')->willReturn($pathInfo);
        $this->helperDataMock->expects($this->once())->method('getUrlSuffix')->willReturn('.html');
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn($isEnabled);

        $identifier = trim($pathInfo, '/');
        if ($isEnabled && count(explode('/', $identifier)) === 1) {
            $this->helperDataMock->expects($this->once())->method('getAllLocationUrl')->willReturn($allLocationUrl);
        }

        $this->assertNull($this->object->match($request));
    }

    public function testExecuteWithStore()
    {
        $requestMethods = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPathInfo', 'setControllerName', 'setAlias', 'setPathInfo']
            )
        );
        /** @var RequestInterface|MockObject $request */
        $request = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $request->expects($this->once())->method('getPathInfo')->willReturn('/find-a-store.html');
        $this->helperDataMock->expects($this->once())->method('getUrlSuffix')->willReturn('.html');
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->helperDataMock->expects($this->once())->method('getAllLocationUrl')->willReturn(['find-a-store']);
        $this->helperDataMock->expects($this->once())->method('getRoute')->willReturn('find-a-store');
        $request->expects($this->once())->method('setModuleName')->with('mpstorelocator')->willReturnSelf();
        $request->expects($this->once())->method('setControllerName')->with('storelocator')->willReturnSelf();
        $request->expects($this->once())->method('setAlias')
            ->with(Url::REWRITE_REQUEST_PATH_ALIAS, 'find-a-store.html')->willReturnSelf();
        $request->expects($this->once())->method('setActionName')->with('store')->willReturnSelf();
        $request->expects($this->once())->method('setPathInfo')
            ->with('/mpstorelocator/storelocator/store')->willReturnSelf();
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->helperDataMock->expects($this->once())->method('getLocationByUrl')
            ->with('find-a-store')
            ->willReturn($location);
        $location->expects($this->once())->method('getLocationId')->willReturn(0);
        $result = $this->getMockBuilder(Forward::class)->disableOriginalConstructor()->getMock();
        $this->actionFactoryMock->expects($this->once())->method('create')->with(Forward::class)->willReturn($result);

        $this->assertEquals($result, $this->object->match($request));
    }

    public function testExecuteWithView()
    {
        $requestMethods = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPathInfo', 'setControllerName', 'setAlias', 'setPathInfo']
            )
        );
        /** @var RequestInterface|MockObject $request */
        $request = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $request->expects($this->once())->method('getPathInfo')->willReturn('/home.html');
        $this->helperDataMock->expects($this->once())->method('getUrlSuffix')->willReturn('.html');
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->helperDataMock->expects($this->once())->method('getAllLocationUrl')
            ->willReturn(['find-a-store', 'home']);
        $this->helperDataMock->expects($this->once())->method('getRoute')->willReturn('find-a-store');
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->helperDataMock->expects($this->once())->method('getLocationByUrl')
            ->with('home')
            ->willReturn($location);
        $location->expects($this->exactly(2))->method('getLocationId')->willReturn(1);
        $request->expects($this->once())->method('setModuleName')->with('mpstorelocator')->willReturnSelf();
        $request->expects($this->once())->method('setControllerName')->with('storelocator')->willReturnSelf();
        $request->expects($this->once())->method('setAlias')
            ->with(Url::REWRITE_REQUEST_PATH_ALIAS, 'home.html')->willReturnSelf();
        $request->expects($this->once())->method('setActionName')->with('view')->willReturnSelf();
        $request->expects($this->once())->method('setPathInfo')
            ->with('mpstorelocator/storelocator/view/1')->willReturnSelf();
        $result = $this->getMockBuilder(Forward::class)->disableOriginalConstructor()->getMock();
        $this->actionFactoryMock->expects($this->once())->method('create')->with(Forward::class)->willReturn($result);

        $this->assertEquals($result, $this->object->match($request));
    }

    protected function setUp()
    {
        $this->actionFactoryMock = $this->getMockBuilder(ActionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock    = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            Router::class,
            [
                'actionFactory' => $this->actionFactoryMock,
                '_helperData'   => $this->helperDataMock,
            ]
        );
    }
}
