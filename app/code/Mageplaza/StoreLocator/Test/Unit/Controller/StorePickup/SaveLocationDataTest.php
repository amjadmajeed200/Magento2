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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\StorePickup;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Controller\StorePickup\SaveLocationData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class SaveLocationDataTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\StorePickup
 */
class SaveLocationDataTest extends TestCase
{
    /**
     * @var SaveLocationData
     */
    private $object;

    /**
     * @var JsonFactory|MockObject
     */
    private $jsonResultFactoryMock;

    /**
     * @var Session|MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    public function testExecuteSet()
    {
        $this->requestMock->expects($this->exactly(3))->method('getParam')
            ->withConsecutive(['locationId'], ['timePickup'], ['type'])
            ->willReturnOnConsecutiveCalls(1, 'timePickup', 'set');
        $this->checkoutSessionMock->expects($this->once())->method('setLocationIdSelected')
            ->with(1)->willReturnSelf();
        $this->checkoutSessionMock->expects($this->once())->method('setPickupTime')
            ->with('timePickup')->willReturnSelf();

        $this->assertNull($this->object->execute());
    }

    public function testExecuteGet()
    {
        $this->requestMock->expects($this->exactly(3))->method('getParam')
            ->withConsecutive(['locationId'], ['timePickup'], ['type'])
            ->willReturnOnConsecutiveCalls(1, 'timePickup', 'get');
        $result = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock->expects($this->once())->method('create')->willReturn($result);
        $this->checkoutSessionMock->expects($this->once())->method('getLocationIdSelected')
            ->willReturn(1);
        $result->expects($this->once())->method('setData')->with(['locationId' => 1])->willReturnSelf();

        $this->assertEquals($result, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                 = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sessionMethods              = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['setLocationIdSelected', 'setPickupTime', 'getLocationIdSelected']
            )
        );
        $this->checkoutSessionMock   = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()->getMock();
        $requestMethods              = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['isAjax']
            )
        );
        $this->requestMock           = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );

        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            SaveLocationData::class,
            [
                'context'           => $contextMock,
                '_checkoutSession'  => $this->checkoutSessionMock,
                'resultJsonFactory' => $this->jsonResultFactoryMock,
            ]
        );
    }
}
