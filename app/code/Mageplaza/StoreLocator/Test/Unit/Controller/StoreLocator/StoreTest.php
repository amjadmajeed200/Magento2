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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\StoreLocator;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Controller\StoreLocator\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class StoreTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\StoreLocator
 */
class StoreTest extends TestCase
{
    /**
     * @var Store
     */
    private $object;

    /**
     * @var JsonFactory|MockObject
     */
    private $jsonResultFactoryMock;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var LayoutFactory|MockObject
     */
    private $resultLayoutFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    public function testExecute()
    {
        $this->requestMock->expects($this->once())->method('isAjax')->willReturn(false);
        $result = $this->getMockBuilder(Page::class)->disableOriginalConstructor()->getMock();
        $this->resultPageFactoryMock->expects($this->once())->method('create')->willReturn($result);

        $this->assertEquals($result, $this->object->execute());
    }

    public function testAjaxExecute()
    {
        $this->requestMock->expects($this->once())->method('isAjax')->willReturn(true);
        $result = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock->expects($this->once())->method('create')->willReturn($result);
        $resultLayout = $this->getMockBuilder(Layout::class)->disableOriginalConstructor()->getMock();
        $this->resultLayoutFactoryMock->expects($this->once())->method('create')->willReturn($resultLayout);
        $layout = $this->getMockForAbstractClass(LayoutInterface::class);
        $resultLayout->expects($this->once())->method('getLayout')->willReturn($layout);
        $block = $this->getMockBuilder(Frontend::class)->disableOriginalConstructor()->getMock();
        $layout->expects($this->once())->method('createBlock')
            ->with(Frontend::class)
            ->willReturn($block);
        $block->expects($this->once())->method('setTemplate')
            ->with('Mageplaza_StoreLocator::storelocator/index.phtml')
            ->willReturnSelf();
        $block->expects($this->once())->method('toHtml')->willReturn('html');
        $result->expects($this->once())->method('setData')
            ->with([
                'success' => 'html'
            ])->willReturnSelf();

        $this->assertEquals($result, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                   = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock   = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock   = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultLayoutFactoryMock = $this->getMockBuilder(LayoutFactory::class)
            ->disableOriginalConstructor()->getMock();
        $requestMethods                = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['isAjax']
            )
        );
        $this->requestMock             = $this->getMockForAbstractClass(
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
            Store::class,
            [
                'context'              => $contextMock,
                'resultPageFactory'    => $this->resultPageFactoryMock,
                '_resultLayoutFactory' => $this->resultLayoutFactoryMock,
                '_resultJsonFactory'   => $this->jsonResultFactoryMock
            ]
        );
    }
}
