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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday\Save;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InlineEditTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
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
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var DateTime|MockObject
     */
    private $dateMock;

    /**
     * @var Js|MockObject
     */
    private $jsHelperMock;

    /**
     * @var EventManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var MessageManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    public function testExecuteWithNoPostValue()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn(null);

        $resultRedirect->expects($this->once())->method('setPath')->with('mpstorelocator/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    /**
     * @return array[]
     */
    public function providerExecuteWithBack()
    {
        return [
            //create new/edit, save, with locations data
            [[], 0],
            //create new/edit, save, without locations data
            [null, 0],
            //create new/edit, save and continue edit, with locations data
            [[], 1],
            //create new/edit, save and continue edit, without locations data
            [null, 1]
        ];
    }

    /**
     * @param $locations
     * @param $isBack
     *
     * @dataProvider providerExecuteWithBack
     */
    public function testExecuteWithCreateNew($locations, $isBack)
    {
        $data           = ['general' => []];
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive(['id'], ['back'])->willReturn(null, $isBack);

        $holidayMethods = array_unique(
            array_merge(
                get_class_methods(Holiday::class),
                [
                    'getCreatedAt',
                    'setIsLocationGrid',
                    'setLocationsIds'
                ]
            )
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('getCreatedAt')->willReturn(null);
        $this->dateMock->method('date')->willReturn('2020-12-06 00:00:00');
        $data['general']['created_at'] = '2020-12-06 00:00:00';
        $data['general']['updated_at'] = '2020-12-06 00:00:00';
        $holiday->expects($this->once())->method('addData')->with($data['general'])->willReturnSelf();

        $this->requestMock->expects($this->once())->method('getPost')->with('locations')->willReturn($locations);

        if ($locations) {
            $holiday->expects($this->once())->method('setIsLocationGrid')->with(true)->willReturnSelf();
            $this->jsHelperMock->expects($this->once())->method('decodeGridSerializedInput')
                ->with($locations)->willReturn([1, 2]);
            $holiday->expects($this->once())->method('setLocationsIds')->with([1, 2])->willReturnSelf();
        }
        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with(
                'mageplaza_storelocator_holiday_prepare_save',
                ['holiday' => $holiday, 'request' => $this->requestMock]
            )->willReturnSelf();
        $holiday->expects($this->once())->method('save')->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('The holiday has been saved.'))
            ->willReturnSelf();
        $this->sessionMock->expects($this->once())->method('setData')
            ->with('mageplaza_storelocator_holiday_data', false)
            ->willReturnSelf();

        if ($isBack) {
            $holiday->method('getId')->willReturn(1);
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/edit', ['id' => 1, '_current' => true])->willReturnSelf();
        } else {
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/')->willReturnSelf();
        }

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    /**
     * @param $locations
     * @param $isBack
     *
     * @dataProvider providerExecuteWithBack
     */
    public function testExecuteWithEdit($locations, $isBack)
    {
        $data           = ['general' => []];
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive(['id'], ['back'])->willReturn(1, $isBack);

        $holidayMethods = array_unique(
            array_merge(
                get_class_methods(Holiday::class),
                [
                    'getCreatedAt',
                    'setIsLocationGrid',
                    'setLocationsIds'
                ]
            )
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->willReturnSelf();
        $holiday->method('getId')->willReturn(1);
        $holiday->expects($this->once())->method('getCreatedAt')->willReturn(null);
        $this->dateMock->method('date')->willReturn('2020-12-06 00:00:00');
        $data['general']['created_at'] = '2020-12-06 00:00:00';
        $data['general']['updated_at'] = '2020-12-06 00:00:00';
        $holiday->expects($this->once())->method('addData')->with($data['general'])->willReturnSelf();

        $this->requestMock->expects($this->once())->method('getPost')->with('locations')->willReturn($locations);

        if ($locations) {
            $holiday->expects($this->once())->method('setIsLocationGrid')->with(true)->willReturnSelf();
            $this->jsHelperMock->expects($this->once())->method('decodeGridSerializedInput')
                ->with($locations)->willReturn([1, 2]);
            $holiday->expects($this->once())->method('setLocationsIds')->with([1, 2])->willReturnSelf();
        }
        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with(
                'mageplaza_storelocator_holiday_prepare_save',
                ['holiday' => $holiday, 'request' => $this->requestMock]
            )->willReturnSelf();
        $holiday->expects($this->once())->method('save')->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('The holiday has been saved.'))
            ->willReturnSelf();
        $this->sessionMock->expects($this->once())->method('setData')
            ->with('mageplaza_storelocator_holiday_data', false)
            ->willReturnSelf();

        if ($isBack) {
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/edit', ['id' => 1, '_current' => true])->willReturnSelf();
        } else {
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/')->willReturnSelf();
        }

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
        $requestMethods                  = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPostValue', 'getPost']
            )
        );
        $this->requestMock               = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $this->dateMock                  = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsHelperMock              = $this->getMockBuilder(Js::class)
            ->disableOriginalConstructor()->getMock();
        $this->eventManagerMock          = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock        = $this->getMockBuilder(MessageManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods                  = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['setData']
            )
        );
        $this->sessionMock               = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock        = $this->getMockBuilder(HolidayFactory::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);
        $contextMock->method('getEventManager')->willReturn($this->eventManagerMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getSession')->willReturn($this->sessionMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Save::class,
            [
                'context'        => $contextMock,
                'date'           => $this->dateMock,
                'jsHelper'       => $this->jsHelperMock,
                'holidayFactory' => $this->holidayFactoryMock,
            ]
        );
    }
}
