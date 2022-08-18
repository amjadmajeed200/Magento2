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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit;
use Mageplaza\StoreLocator\Model\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EditTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location
 */
class EditTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $_coreRegistryMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var Edit
     */
    private $editBlock;

    public function testGetHeaderTextWithEditLocation()
    {
        $locationId   = 1;
        $locationName = 'Location';
        $result       = new Phrase("Edit Location '%1'", ['Location']);
        $locationMock = $this->getMockBuilder(Location::class)
            ->setMethods(['getName', 'getId'])
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('mageplaza_storelocator_location')
            ->willReturn($locationMock);
        $locationMock->expects($this->once())->method('getId')->willReturn($locationId);
        $locationMock->expects($this->once())->method('getName')->willReturn($locationName);
        $this->escaperMock->expects($this->once())->method('escapeHtml')
            ->with($locationName)->willReturn($locationName);

        $this->assertEquals($result, $this->editBlock->getHeaderText());
    }

    public function testGetHeaderTextWithNewLocation()
    {
        $locationMock = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('mageplaza_storelocator_location')
            ->willReturn($locationMock);

        $this->assertEquals(new Phrase('New Location'), $this->editBlock->getHeaderText());
    }

    public function testGetFormActionUrl()
    {
        $locationMock = $this->getMockBuilder(Location::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('mageplaza_storelocator_location')
            ->willReturn($locationMock);

        $locationMock->expects($this->once())->method('getId')->willReturn(null);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/save')->willReturn('save_url');

        $this->assertEquals('save_url', $this->editBlock->getFormActionUrl());
    }

    public function testGetFormActionUrlWithId()
    {
        $locationId   = 1;
        $locationMock = $this->getMockBuilder(Location::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()->getMock();
        $this->_coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('mageplaza_storelocator_location')
            ->willReturn($locationMock);

        $locationMock->expects($this->once())->method('getId')->willReturn($locationId);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/save', ['id' => $locationId])->willReturn('save_url/id/1');

        $this->assertEquals('save_url/id/1', $this->editBlock->getFormActionUrl());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock          = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)->getMock();
        $buttonListMock       = $this->getMockBuilder(ButtonList::class)->disableOriginalConstructor()->getMock();
        $this->escaperMock    = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()->getMock();
        $requestMock          = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()->getMock();

        $contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $contextMock->method('getButtonList')->willReturn($buttonListMock);
        $contextMock->method('getEscaper')->willReturn($this->escaperMock);
        $contextMock->method('getRequest')->willReturn($requestMock);

        $this->_coreRegistryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->editBlock         = new Edit(
            $this->_coreRegistryMock,
            $contextMock
        );
    }
}
