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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday\Delete;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private $object;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var HolidayFactory|MockObject
     */
    private $holidayFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    public function testExecuteWithNoId()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(null);
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with(__('Holiday to delete was not found.'))
            ->willReturnSelf();
        $resultRedirect->expects($this->once())->method('setPath')->with('mpstorelocator/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteWithParamId()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(1);
        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('delete')->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('The Holiday has been deleted.'))
            ->willReturnSelf();
        $resultRedirect->expects($this->once())->method('setPath')->with('mpstorelocator/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteThrowException()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(1);
        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('delete')->willThrowException(new Exception('delete error'));
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with('delete error')
            ->willReturnSelf();
        $resultRedirect->expects($this->once())->method('setPath')
            ->with('mpstorelocator/*/edit', ['id' => 1])->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                     = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock               = $this->getMockForAbstractClass(RequestInterface::class);
        $this->holidayFactoryMock        = $this->getMockBuilder(HolidayFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock        = $this->getMockForAbstractClass(ManagerInterface::class);

        $contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Delete::class,
            [
                'context'        => $contextMock,
                'holidayFactory' => $this->holidayFactoryMock
            ]
        );
    }
}
