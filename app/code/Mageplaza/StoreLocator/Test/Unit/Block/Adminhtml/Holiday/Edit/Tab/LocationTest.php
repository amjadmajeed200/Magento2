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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Holiday\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Mageplaza\StoreLocator\Block\Adminhtml\Holiday\Edit\Tab\Location;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class LocationTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Holiday\Edit\Tab
 */
class LocationTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystemMock;

    /**
     * @var Registry|MockObject
     */
    private $_coreRegistryMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $locationCollectionFactoryMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $holidayMock;

    /**
     * @var Location
     */
    private $locationBlock;

    /**
     * @return array
     */
    public function providerSelected()
    {
        return [[[1, 2], [1 => 1, 2 => 2]], [null, []], ['abc', []]];
    }

    /**
     * @param $selected
     *
     * @param $result
     *
     * @throws LocalizedException
     * @dataProvider providerSelected
     */
    public function testGetSelectedLocations($selected, $result)
    {
        $this->holidayMock->expects($this->once())->method('getLocationIds')->willReturn($selected);

        $this->assertEquals($result, $this->locationBlock->getSelectedLocations());
    }

    public function testGetHoliday()
    {
        $this->assertEquals($this->holidayMock, $this->locationBlock->getHoliday());
    }

    public function testGetGridUrl()
    {
        $holidayId = 1;

        $this->holidayMock
            ->expects($this->once())
            ->method('getId')->willReturn($holidayId);

        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*/locationsGrid', ['holiday_id' => $holidayId])->willReturn('locationsGrid/id/1');

        $this->assertEquals('locationsGrid/id/1', $this->locationBlock->getGridUrl());
    }

    public function testGetTabUrl()
    {
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('mpstorelocator/holiday/locations', ['_current' => true])->willReturn('tab_url');

        $this->assertEquals('tab_url', $this->locationBlock->getTabUrl());
    }

    protected function setUp()
    {
        $this->contextMock                   = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock             = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->urlBuilderMock                = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->fileSystemMock                = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $directoryMock                       = $this->getMockBuilder(WriteInterface::class)
            ->disableOriginalConstructor()->getMock();
        $objectManagerHelper                 = new ObjectManager($this);

        $this->holidayMock = $this->getMockBuilder(Holiday::class)
            ->disableOriginalConstructor()->getMock();

        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->contextMock->method('getFilesystem')->willReturn($this->fileSystemMock);

        $this->fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($directoryMock);

        $this->_coreRegistryMock
            ->method('registry')
            ->with('mageplaza_storelocator_holiday')->willReturn($this->holidayMock);

        $this->locationBlock = $objectManagerHelper->getObject(
            Location::class,
            [
                'context'                   => $this->contextMock,
                'coreRegistry'              => $this->_coreRegistryMock,
                'locationCollectionFactory' => $this->locationCollectionFactoryMock
            ]
        );
    }
}
