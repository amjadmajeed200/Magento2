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
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Controller\StoreLocator\PickupLocationsData;
use Mageplaza\StoreLocator\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PickupLocationsDataTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\StoreLocator
 */
class PickupLocationsDataTest extends TestCase
{
    /**
     * @var PickupLocationsData
     */
    private $object;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var JsonFactory|MockObject
     */
    private $jsonResultFactoryMock;

    /**
     * @var Frontend|MockObject
     */
    private $frontEndMock;

    public function testExecute()
    {
        $result = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock->expects($this->once())->method('create')->willReturn($result);
        $this->frontEndMock->expects($this->once())->method('getPickupLocationList')->willReturn([]);
        $this->frontEndMock->expects($this->once())->method('getDataLocations')->willReturn('[]');
        $this->helperDataMock->expects($this->once())->method('jsDecode')
            ->with('[]')
            ->willReturn([]);

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
        $this->helperDataMock        = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonResultFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->frontEndMock          = $this->getMockBuilder(Frontend::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            PickupLocationsData::class,
            [
                'context'           => $contextMock,
                '_helperData'       => $this->helperDataMock,
                'jsonResultFactory' => $this->jsonResultFactoryMock,
                '_frontEnd'         => $this->frontEndMock
            ]
        );
    }
}
