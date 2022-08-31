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

namespace Mageplaza\StoreLocator\Test\Unit\Plugin\Adminhtml\Order\Create\Shipping\Method;

use Exception;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form;
use Mageplaza\StoreLocator\Plugin\Adminhtml\Order\Create\Shipping\Method\RemovePickupMethod;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RemovePickupMethodTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Adminhtml\Order\Create\Shipping\Method
 */
class RemovePickupMethodTest extends TestCase
{
    /**
     * @var RemovePickupMethod
     */
    private $object;

    /**
     * @throws Exception
     */
    public function testAfterGetShippingRates()
    {
        /** @var Form|MockObject $subject */
        $subject        = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()->getMock();
        $subjectMethods = get_class_methods(Form::class);
        if (!in_array('getShippingRates', $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
        $result = [
            'mpstorepickup' => [],
            'abc'           => []
        ];

        $expectedResult = [
            'abc' => []
        ];
        $this->assertEquals($expectedResult, $this->object->afterGetShippingRates($subject, $result));
    }

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(RemovePickupMethod::class);
    }
}
