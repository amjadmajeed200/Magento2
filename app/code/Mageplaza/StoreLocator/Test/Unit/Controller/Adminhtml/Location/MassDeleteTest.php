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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location\MassDelete;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MassDeleteTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location
 */
class MassDeleteTest extends TestCase
{
    /**
     * @var MassDelete
     */
    private $object;

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

        $this->filterMock->expects($this->once())->method('getCollection')->with($collection)->willReturn($collection);

        $collection->expects($this->once())->method('walk')->with('delete')->willReturnSelf();
        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('Locations has been deleted.'))
            ->willReturnSelf();
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultFactoryMock->expects($this->once())->method('create')->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirect);
        $resultRedirect->expects($this->once())->method('setPath')->with('*/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteWithException()
    {
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);

        $this->filterMock->expects($this->once())->method('getCollection')->with($collection)->willReturn($collection);

        $collection->expects($this->once())->method('walk')->with('delete')
            ->willThrowException(new Exception('Exception'));
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with(__('Something wrong when deleting Locations. %1', 'Exception'))
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
        $this->resultFactoryMock     = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            MassDelete::class,
            [
                'context'           => $contextMock,
                'filter'            => $this->filterMock,
                'collectionFactory' => $this->collectionFactoryMock
            ]
        );
    }
}
