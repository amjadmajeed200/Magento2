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
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday\Edit;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EditTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
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

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var Registry|MockObject
     */
    private $coreRegistryMock;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    public function testExecuteWithNoHoliday()
    {
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(1);
        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->once())->method('getId')->willReturn(null);
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with(__('This holiday no longer exists.'))
            ->willReturnSelf();
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $resultRedirect->expects($this->once())->method('setPath')->with('*')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    /**
     * @return array
     */
    public function providerCreateData()
    {
        return [
            ['holiday_data'],
            [null]
        ];
    }

    /**
     * @param $data
     *
     * @dataProvider providerCreateData
     */
    public function testExecuteWithCreate($data)
    {
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(null);
        $holiday = $this->getMockBuilder(Holiday::class)->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $this->sessionMock->expects($this->once())->method('getData')
            ->with('mageplaza_storelocator_holiday_data', true)->willReturn($data);
        if (!empty($data)) {
            $holiday->expects($this->once())->method('setData')->with($data)->willReturnSelf();
        }

        $this->coreRegistryMock->expects($this->once())->method('register')
            ->with('mageplaza_storelocator_holiday', $holiday)->willReturnSelf();
        $resultPage = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultPageFactoryMock->method('create')->willReturn($resultPage);

        $resultPage->expects($this->once())->method('setActiveMenu')->with('Mageplaza_StoreLocator::holiday')
            ->willReturnSelf();
        $pageConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();
        $resultPage->expects($this->exactly(2))->method('getConfig')->willReturn($pageConfig);
        $pageTitle = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()->getMock();
        $pageConfig->expects($this->exactly(2))->method('getTitle')->willReturn($pageTitle);
        $pageTitle->expects($this->once())->method('set')->with(__('Manage General Holidays'))->willReturnSelf();

        $holiday->expects($this->once())->method('getId')->willReturn(null);
        $pageTitle->expects($this->once())->method('prepend')->with(__('New Holiday'))->willReturnSelf();

        $this->assertEquals($resultPage, $this->object->execute());
    }

    /**
     * @param $data
     *
     * @dataProvider providerCreateData
     */
    public function testExecuteWithEdit($data)
    {
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(1);

        $holidayMethods = array_unique(
            array_merge(
                get_class_methods(Holiday::class),
                ['getName']
            )
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactoryMock->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $holiday->expects($this->exactly(2))->method('getId')->willReturn(1);

        $this->sessionMock->expects($this->once())->method('getData')
            ->with('mageplaza_storelocator_holiday_data', true)->willReturn($data);
        if (!empty($data)) {
            $holiday->expects($this->once())->method('setData')->with($data)->willReturnSelf();
        }

        $this->coreRegistryMock->expects($this->once())->method('register')
            ->with('mageplaza_storelocator_holiday', $holiday)->willReturnSelf();
        $resultPage = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultPageFactoryMock->method('create')->willReturn($resultPage);

        $resultPage->expects($this->once())->method('setActiveMenu')->with('Mageplaza_StoreLocator::holiday')
            ->willReturnSelf();
        $pageConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()->getMock();
        $resultPage->expects($this->exactly(2))->method('getConfig')->willReturn($pageConfig);
        $pageTitle = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()->getMock();
        $pageConfig->expects($this->exactly(2))->method('getTitle')->willReturn($pageTitle);
        $pageTitle->expects($this->once())->method('set')->with(__('Manage General Holidays'))->willReturnSelf();

        $holiday->expects($this->once())->method('getName')->willReturn('Holiday');
        $pageTitle->expects($this->once())->method('prepend')->with('Holiday')->willReturnSelf();

        $this->assertEquals($resultPage, $this->object->execute());
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
        $this->sessionMock               = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()->getMock();
        $this->coreRegistryMock          = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultPageFactoryMock     = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock        = $this->getMockForAbstractClass(ManagerInterface::class);

        $contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getSession')->willReturn($this->sessionMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Edit::class,
            [
                'context'           => $contextMock,
                'holidayFactory'    => $this->holidayFactoryMock,
                'coreRegistry'      => $this->coreRegistryMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }
}
