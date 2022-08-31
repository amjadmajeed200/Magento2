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

namespace Mageplaza\StoreLocator\Test\Unit\Plugin\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteValidator;
use Mageplaza\StoreLocator\Plugin\Model\DismissValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DismissValidatorTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Model
 */
class DismissValidatorTest extends TestCase
{
    /**
     * @var bool
     */
    protected $isProceed = true;

    /**
     * @var DismissValidator
     */
    private $object;

    /**
     * @var QuoteValidator|MockObject
     */
    private $subjectMock;

    /**
     * @var Callable
     */
    private $proceedMock;

    /**
     * @return array
     */
    public function provider(): array
    {
        return [
            [null, true],
            ['mpstorepickup_mpstorepickup', false]
        ];
    }

    /**
     * @param $shippingMethod
     *
     * @param $shouldProceedRun
     *
     * @dataProvider provider
     */
    public function testAroundValidateBeforeSubmit($shippingMethod, $shouldProceedRun)
    {
        $this->isProceed = false;

        $this->checkMethodExits();
        /** @var Quote|MockObject $quote */
        $quote           = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $shippingAddress = $this->getMockBuilder(Address::class)->disableOriginalConstructor()->getMock();
        $quote->expects($this->once())->method('getShippingAddress')->willReturn($shippingAddress);
        $shippingAddress->expects($this->once())->method('getShippingMethod')->willReturn($shippingMethod);

        $this->object->aroundValidateBeforeSubmit($this->subjectMock, $this->proceedMock, $quote);
        $this->assertSame(
            $this->isProceed,
            $shouldProceedRun
        );
    }

    private function checkMethodExits()
    {
        $subjectMethods = get_class_methods(QuoteValidator::class);
        if (!in_array('validateBeforeSubmit', $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
    }

    protected function setUp()
    {
        $this->subjectMock = $this->getMockBuilder(QuoteValidator::class)
            ->disableOriginalConstructor()->getMock();
        $this->proceedMock = function () {
            $this->isProceed = true;
        };

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            DismissValidator::class
        );
    }
}
