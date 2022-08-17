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

namespace Mageplaza\StoreLocator\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday as ResourceHoliday;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class HolidayTest
 * @package Mageplaza\StoreLocator\Test\Unit\Model
 */
class HolidayTest extends TestCase
{
    /**
     * @var Holiday
     */
    private $object;

    /**
     * @var ResourceHoliday|MockObject
     */
    private $resourceMock;

    public function testGetLocationIds()
    {
        $this->resourceMock->expects($this->once())->method('getLocationIds')->with($this->object)
            ->willReturn(['1', '2']);
        $this->assertEquals(['1', '2'], $this->object->getLocationIds());
    }

    protected function setUp()
    {
        $this->resourceMock  = $this->getMockBuilder(ResourceHoliday::class)
            ->disableOriginalConstructor()->getMock();
        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Holiday::class,
            [
                '_resource' => $this->resourceMock,
            ]
        );
    }
}
