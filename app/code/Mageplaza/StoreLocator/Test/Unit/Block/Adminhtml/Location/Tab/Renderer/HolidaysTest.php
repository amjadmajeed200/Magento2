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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\Holidays;
use Mageplaza\StoreLocator\Model\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class HolidaysTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab\Renderer
 */
class HolidaysTest extends TestCase
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
     * @var Holidays
     */
    private $holidaysBlock;

    /**
     * @var Session|MockObject
     */
    private $backendSessionMock;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    /**
     * @var Location|MockObject
     */
    private $locationMock;

    public function testGetLocation()
    {
        $this->assertEquals($this->locationMock, $this->holidaysBlock->getLocation());
    }

    /**
     * @return array
     */
    public function providerSelected()
    {
        return [
            [
                [1, 2],
                ['1' => 1, '2' => 2]
            ],
            [null, []],
            ['abc', []]
        ];
    }

    /**
     * @param $selected
     * @param $result
     *
     * @dataProvider providerSelected
     *
     * @throws LocalizedException
     */
    public function testGetSelectedHolidays($selected, $result)
    {
        $this->locationMock->expects($this->once())->method('getHolidayIds')->willReturn($selected);

        $this->assertEquals($result, $this->holidaysBlock->getSelectedHolidays());
    }

    public function testGetGridUrl()
    {
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('*/*/holidays')
            ->willReturn('holidays_url');

        $this->assertEquals('holidays_url', $this->holidaysBlock->getGridUrl());
    }

    protected function setUp()
    {
        $this->contextMock        = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->backendSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystem         = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $this->urlBuilderMock     = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->contextMock->method('getBackendSession')->willReturn($this->backendSessionMock);
        $this->contextMock->method('getFilesystem')->willReturn($this->filesystem);
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);

        $directoryMock = $this->getMockBuilder(WriteInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($directoryMock);

        $this->locationMock = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();

        $this->backendSessionMock
            ->method('getData')
            ->with('mageplaza_storelocator_location_model')
            ->willReturn($this->locationMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->holidaysBlock = $objectManagerHelper->getObject(
            Holidays::class,
            [
                'context' => $this->contextMock,
            ]
        );
    }
}
