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
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Controller\StoreLocator\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ViewTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\StoreLocator
 */
class ViewTest extends TestCase
{
    /**
     * @var View
     */
    private $object;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    public function testExecute()
    {
        $result = $this->getMockBuilder(Page::class)->disableOriginalConstructor()->getMock();
        $this->resultPageFactoryMock->expects($this->once())->method('create')->willReturn($result);

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
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            View::class,
            [
                'context'           => $contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }
}
