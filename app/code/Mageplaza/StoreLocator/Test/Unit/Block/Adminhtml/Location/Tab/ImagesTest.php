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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Images;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ImagesTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab
 */
class ImagesTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var Images
     */
    private $imagesBlock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    public function testGetDataObject()
    {
        $locationMock = $this->getDataObject();

        $this->assertEquals($locationMock, $this->imagesBlock->getDataObject());
    }

    /**
     * @return Location|MockObject
     */
    public function getDataObject()
    {
        $locationMock = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->registryMock->expects($this->once())->method('registry')->with('mageplaza_storelocator_location')
            ->willReturn($locationMock);

        return $locationMock;
    }

    public function testGetImages()
    {
        $images       = '[{"position":"1","file":"\/i\/m\/image.png","label":""}]';
        $result       = [
            [
                'position' => '1',
                'file'     => '/i/m/image.png',
                'label'    => ''
            ]
        ];
        $locationMock = $this->getDataObject();
        $locationMock->expects($this->once())
            ->method('getImages')
            ->willReturn($images);
        $this->helperDataMock->expects($this->once())
            ->method('jsDecode')
            ->with($images)
            ->willReturn($result);

        $this->assertEquals($result, $this->imagesBlock->getImages());
    }

    protected function setUp()
    {
        $this->contextMock    = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->registryMock   = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->imagesBlock   = $objectManagerHelper->getObject(
            Images::class,
            [
                'context'       => $this->contextMock,
                '_coreRegistry' => $this->registryMock,
                'helperData'    => $this->helperDataMock
            ]
        );
    }
}
