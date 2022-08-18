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
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderRepository as OrderRepositoryModel;
use Mageplaza\StoreLocator\Plugin\Model\OrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderRepositoryTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Model
 */
class OrderRepositoryTest extends TestCase
{
    /**
     * @var OrderRepository
     */
    private $object;

    /**
     * @var OrderRepositoryModel|MockObject
     */
    private $subjectMock;

    public function testAfterGet()
    {
        $this->checkMethodExits('get');
        $methods = array_unique(
            array_merge(
                get_class_methods(OrderInterface::class),
                ['getMpTimePickup']
            )
        );
        /** @var OrderInterface|MockObject $result */
        $result = $this->getMockBuilder(OrderInterface::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result->expects($this->once())->method('getMpTimePickup')->willReturn('');

        $this->object->afterGet($this->subjectMock, $result);
    }

    /**
     * @param $method
     */
    private function checkMethodExits($method)
    {
        $subjectMethods = get_class_methods(OrderRepositoryModel::class);
        if (!in_array($method, $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
    }

    public function testAfterGetList()
    {
        $this->checkMethodExits('getList');
        /** @var OrderSearchResultInterface|MockObject $result */
        $result = $this->getMockBuilder(OrderSearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result->expects($this->once())->method('getItems')->willReturn([]);

        $this->object->afterGetList($this->subjectMock, $result);
    }

    protected function setUp()
    {
        $this->subjectMock = $this->getMockBuilder(OrderRepositoryModel::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            OrderRepository::class
        );
    }
}
