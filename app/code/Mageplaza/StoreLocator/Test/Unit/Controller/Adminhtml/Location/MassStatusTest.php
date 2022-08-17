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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location\MassStatus;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MassStatusTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location
 */
class MassStatusTest extends TestCase
{
    /**
     * @var MassStatus
     */
    private $object;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Filter|MockObject
     */
    private $filterMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    public function testExecute()
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);

        $this->requestMock->expects($this->once())->method('getParam')->with('status')->willReturn(1);

        $this->filterMock->expects($this->once())->method('getCollection')->with($collection)->willReturn($collection);
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('getItems')->willReturn([$location, $location]);
        $location->expects($this->exactly(2))->method('setStatus')->with(1)->willReturnSelf();
        $location->expects($this->at(1))->method('save')->willReturnSelf();
        $e = new Exception();
        $location->expects($this->at(1))->method('save')->willThrowException($e);
        $location->expects($this->once())->method('getName')->willReturn('Name');

        $this->sessionMock->expects($this->once())->method('addException')
            ->with($e, __('Something went wrong while updating status for %1.', 'Name'))
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('A total of %1 record(s) have been updated.', 1))
            ->willReturnSelf();

        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultFactoryMock->expects($this->once())->method('create')->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirect);
        $resultRedirect->expects($this->once())->method('setPath')->with('*/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                 = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterMock            = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock    = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->requestMock           = $this->getMockForAbstractClass(RequestInterface::class);
        $sessionMethods              = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['addException']
            )
        );
        $this->sessionMock           = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->resultFactoryMock     = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $contextMock->method('getSession')->willReturn($this->sessionMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            MassStatus::class,
            [
                'context'           => $contextMock,
                'filter'            => $this->filterMock,
                'collectionFactory' => $this->collectionFactoryMock
            ]
        );
    }
}
