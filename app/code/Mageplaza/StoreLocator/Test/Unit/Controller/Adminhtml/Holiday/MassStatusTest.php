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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday\MassStatus;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MassStatusTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Holiday
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

    public function testExecute()
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);

        $this->requestMock->expects($this->once())->method('getParam')->with('status')->willReturn(1);

        $this->filterMock->expects($this->once())->method('getCollection')->with($collection)->willReturn($collection);
        $holidayMethods = array_unique(array_merge(
            get_class_methods(Holiday::class),
            [
                'setStatus',
                'getName'
            ]
        ));
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('getItems')->willReturn([$holiday, $holiday]);
        $holiday->expects($this->exactly(2))->method('setStatus')->with(1)->willReturnSelf();
        $holiday->expects($this->at(1))->method('save')->willReturnSelf();
        $e = new Exception();
        $holiday->expects($this->at(1))->method('save')->willThrowException($e);
        $holiday->expects($this->once())->method('getName')->willReturn('Name');

        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
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

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);

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
