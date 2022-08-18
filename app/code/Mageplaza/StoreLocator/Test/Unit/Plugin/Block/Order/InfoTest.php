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

namespace Mageplaza\StoreLocator\Test\Unit\Plugin\Block\Order;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order;
use Mageplaza\StoreLocator\Plugin\Block\Order\Info;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InfoTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Block\Order
 */
class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    private $object;

    public function testBeforeToHtml()
    {
        /** @var \Magento\Sales\Block\Order\Info|MockObject $subject */
        $subject        = $this->getMockBuilder(\Magento\Sales\Block\Order\Info::class)
            ->disableOriginalConstructor()->getMock();
        $subjectMethods = get_class_methods(\Magento\Sales\Block\Order\Info::class);
        if (!in_array('toHtml', $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
        $order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()->getMock();
        $subject->expects($this->once())->method('getOrder')->willReturn($order);
        $order->expects($this->once())->method('getShippingMethod')->willReturn('mpstorepickup_mpstorepickup');
        $subject->expects($this->once())->method('getTemplate')->willReturn('test.phtml');
        $subject->expects($this->once())->method('setTemplate')
            ->with('Mageplaza_StoreLocator::order/info.phtml')->willReturnSelf();

        $this->object->beforeToHtml($subject);
    }

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(Info::class);
    }
}
