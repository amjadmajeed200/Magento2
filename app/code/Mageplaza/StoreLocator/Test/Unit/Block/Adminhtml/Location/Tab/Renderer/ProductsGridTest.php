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

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\ProductsGrid;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ProductsGridTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab\Renderer
 */
class ProductsGridTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var ProductsGrid
     */
    private $productsGridBlock;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var LocationFactory|MockObject
     */
    private $locationFactoryMock;

    public function testGetSelectedProductsWithData()
    {
        $this->requestMock->expects($this->once())->method('getPost')->with('pickup_products')->willReturn([]);

        $this->assertEquals([], $this->productsGridBlock->getSelectedProducts());
    }

    /**
     * @return array
     */
    public function providerSelected()
    {
        return [
            [
                '1&2',
                ['1', '2']
            ],
            [null, ['']],
            ['abc', ['abc']],
            ['', ['']]
        ];
    }

    /**
     * @param $productIds
     * @param $result
     *
     * * @dataProvider providerSelected
     *
     * @throws Exception
     */
    public function testGetSelectedProductsWithNoData($productIds, $result)
    {
        $this->requestMock->expects($this->once())->method('getPost')->with('pickup_products')->willReturn(null);

        $locationId = 1;
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn($locationId);

        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        $this->locationFactoryMock->method('create')->willReturn($location);

        $location->expects($this->once())->method('load')->willReturnSelf();

        $location->expects($this->once())->method('getId')->willReturn($locationId);

        $locationCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getCollection')->willReturn($locationCollection);

        $locationCollection->expects($this->once())->method('addFieldToFilter')->with('location_id', $locationId)
            ->willReturnSelf();
        $locationCollection->expects($this->once())->method('getFirstItem')
            ->willReturn($location);

        $location->expects($this->once())->method('getData')->with('product_ids')->willReturn($productIds);

        $this->assertEquals($result, $this->productsGridBlock->getSelectedProducts());
    }

    protected function setUp()
    {
        $this->contextMock         = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystem          = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock = $this->getMockBuilder(LocationFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock         = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(['getPost'])
            ->getMockForAbstractClass();

        $this->contextMock->method('getFilesystem')->willReturn($this->filesystem);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);

        $directoryMock = $this->getMockBuilder(WriteInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($directoryMock);

        $objectManagerHelper     = new ObjectManager($this);
        $this->productsGridBlock = $objectManagerHelper->getObject(
            ProductsGrid::class,
            [
                'context'          => $this->contextMock,
                '_locationFactory' => $this->locationFactoryMock
            ]
        );
    }
}
