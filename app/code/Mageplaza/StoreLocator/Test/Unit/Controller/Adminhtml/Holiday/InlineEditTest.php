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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday\InlineEdit;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class InlineEditTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday
 */
class InlineEditTest extends TestCase
{
    /**
     * @var InlineEdit
     */
    private $object;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var HolidayFactory|MockObject
     */
    private $holidayFactoryMock;

    /**
     * @var JsonFactory|MockObject
     */
    private $jsonFactoryMock;

    public function testExecuteWithWrongData()
    {
        $resultJson = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())->method('create')->willReturn($resultJson);
        $this->requestMock->expects($this->once())->method('getParam')
            ->withConsecutive(['items', []])
            ->willReturnOnConsecutiveCalls([]);

        $resultJson->expects($this->once())->method('setData')->with([
            'messages' => [__('Please correct the data sent.')],
            'error'    => true,
        ])->willReturnSelf();

        $this->object->execute();
    }

    public function testExecuteWithWrongNoAjax()
    {
        $resultJson = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())->method('create')->willReturn($resultJson);
        $this->requestMock->expects($this->exactly(2))->method('getParam', 'isAjax')
            ->withConsecutive(['items', []])
            ->willReturnOnConsecutiveCalls([1 => ['data']], false);

        $resultJson->expects($this->once())->method('setData')->with([
            'messages' => [__('Please correct the data sent.')],
            'error'    => true,
        ])->willReturnSelf();

        $this->assertEquals($resultJson, $this->object->execute());
    }

    public function testExecute()
    {
        $resultJson = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())->method('create')->willReturn($resultJson);
        $this->requestMock->expects($this->exactly(2))->method('getParam', 'isAjax')
            ->withConsecutive(['items', []])
            ->willReturnOnConsecutiveCalls([1 => ['data']], true);

        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('addData')->with(['data'])->willReturnSelf();
        $holiday->expects($this->once())->method('save')->willReturnSelf();

        $resultJson->expects($this->once())->method('setData')->with([
            'messages' => [],
            'error'    => false,
        ])->willReturnSelf();

        $this->assertEquals($resultJson, $this->object->execute());
    }

    public function testExecuteThrowRunTimeException()
    {
        $resultJson = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())->method('create')->willReturn($resultJson);
        $this->requestMock->expects($this->exactly(2))->method('getParam', 'isAjax')
            ->withConsecutive(['items', []])
            ->willReturnOnConsecutiveCalls([1 => ['data']], true);

        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('addData')->with(['data'])->willReturnSelf();
        $holiday->expects($this->once())->method('save')->willThrowException(new RuntimeException(
            'Runtime Exception'
        ));

        $holiday->expects($this->once())->method('getId')->willReturn(1);

        $resultJson->expects($this->once())->method('setData')->with([
            'messages' => ['[Holiday ID: 1] Runtime Exception'],
            'error'    => true,
        ])->willReturnSelf();

        $this->assertEquals($resultJson, $this->object->execute());
    }

    public function testExecuteThrowException()
    {
        $resultJson = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock->expects($this->once())->method('create')->willReturn($resultJson);
        $this->requestMock->expects($this->exactly(2))->method('getParam', 'isAjax')
            ->withConsecutive(['items', []])
            ->willReturnOnConsecutiveCalls([1 => ['data']], true);

        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('addData')->with(['data'])->willReturnSelf();
        $holiday->expects($this->once())->method('save')->willThrowException(new Exception(
            'Exception'
        ));

        $holiday->expects($this->once())->method('getId')->willReturn(1);

        $resultJson->expects($this->once())->method('setData')->with([
            'messages' => ['[Holiday ID: 1] Something went wrong while saving the Holiday. Exception'],
            'error'    => true,
        ])->willReturnSelf();

        $this->assertEquals($resultJson, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock              = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock        = $this->getMockForAbstractClass(RequestInterface::class);
        $this->holidayFactoryMock = $this->getMockBuilder(HolidayFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonFactoryMock    = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            InlineEdit::class,
            [
                'context'        => $contextMock,
                'holidayFactory' => $this->holidayFactoryMock,
                'jsonFactory'    => $this->jsonFactoryMock,
            ]
        );
    }
}
