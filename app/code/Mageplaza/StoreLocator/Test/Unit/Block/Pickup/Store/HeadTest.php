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

namespace Mageplaza\StoreLocator\Test\Unit\Block\Product\View;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\StoreLocator\Block\Store\Head;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class HeadTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Product\View
 */
class HeadTest extends TestCase
{
    /**
     * @var Head
     */
    private $headBlock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Repository|MockObject
     */
    private $assetRepoMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var Image|MockObject
     */
    private $helperImageMock;

    public function testGetBackgroundImageWithDefaultImg()
    {
        $fileId = 'Mageplaza_StoreLocator::media/default-background.png';
        $this->requestMock->expects($this->once())->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->once())
            ->method('getUrlWithParams')
            ->with($fileId, ['_secure' => 'false'])
            ->willReturn('default-background-url');
        $this->helperDataMock->expects($this->once())->method('getConfigGeneral')
            ->with('upload_head_image')->willReturn('');

        $this->assertEquals('default-background-url', $this->headBlock->getBackgroundImage());
    }

    public function testGetBackgroundImageWithUploadImg()
    {
        $fileId = 'Mageplaza_StoreLocator::media/default-background.png';
        $this->requestMock->expects($this->once())->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->once())
            ->method('getUrlWithParams')
            ->with($fileId, ['_secure' => 'false'])
            ->willReturn('default-background-url');
        $this->helperDataMock->expects($this->once())->method('getConfigGeneral')
            ->with('upload_head_image')->willReturn('i/m/image.png');

        $this->helperImageMock->expects($this->once())->method('getBaseMediaUrl')->willReturn('base-url');
        $this->helperImageMock->expects($this->once())->method('getMediaPath')
            ->with('i/m/image.png', 'image')
            ->willReturn('pub/media/i/m/image.png');

        $this->assertEquals('base-url/pub/media/i/m/image.png', $this->headBlock->getBackgroundImage());
    }

    public function testGetLogoImageWithDefaultImg()
    {
        $fileId = 'Mageplaza_StoreLocator::media/default-logo.png';
        $this->requestMock->expects($this->once())->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->once())
            ->method('getUrlWithParams')
            ->with($fileId, ['_secure' => 'false'])
            ->willReturn('default-logo-url');
        $this->helperDataMock->expects($this->once())->method('getConfigGeneral')
            ->with('upload_head_icon')->willReturn('');

        $this->assertEquals('default-logo-url', $this->headBlock->getLogoImage());
    }

    public function testGetLogoImageWithUploadImg()
    {
        $fileId = 'Mageplaza_StoreLocator::media/default-logo.png';
        $this->requestMock->expects($this->once())->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->once())
            ->method('getUrlWithParams')
            ->with($fileId, ['_secure' => 'false'])
            ->willReturn('default-logo-url');
        $this->helperDataMock->expects($this->once())->method('getConfigGeneral')
            ->with('upload_head_icon')->willReturn('i/m/image.png');

        $this->helperImageMock->expects($this->once())->method('getBaseMediaUrl')->willReturn('base-url');
        $this->helperImageMock->expects($this->once())->method('getMediaPath')
            ->with('i/m/image.png', 'image')
            ->willReturn('pub/media/i/m/image.png');

        $this->assertEquals('base-url/pub/media/i/m/image.png', $this->headBlock->getLogoImage());
    }

    public function testGetStoreTitleWithNoConfig()
    {
        $this->helperDataMock->expects($this->once())
            ->method('getConfigGeneral')->with('title')->willReturn('');

        $this->assertEquals(__('Find a stores'), $this->headBlock->getStoreTitle());
    }

    public function testGetStoreTitleWithConfig()
    {
        $this->helperDataMock->expects($this->once())
            ->method('getConfigGeneral')->with('title')->willReturn('Store title');

        $this->assertEquals('Store title', $this->headBlock->getStoreTitle());
    }

    public function testGetStoreDescriptionWithNoConfig()
    {
        $this->helperDataMock->expects($this->once())
            ->method('getConfigGeneral')->with('description')->willReturn('');

        $this->assertEquals(
            __('Do more of what you love. Discover inspiring programs happening every day at Apple.'),
            $this->headBlock->getStoreDescription()
        );
    }

    public function testGetStoreDescriptionWithConfig()
    {
        $this->helperDataMock->expects($this->once())
            ->method('getConfigGeneral')->with('description')->willReturn('Store description');

        $this->assertEquals('Store description', $this->headBlock->getStoreDescription());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock           = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock     = $this->getMockForAbstractClass(RequestInterface::class);
        $this->assetRepoMock   = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock  = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperImageMock = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()->getMock();

        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getAssetRepository')->willReturn($this->assetRepoMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->headBlock = $objectManagerHelper->getObject(
            Head::class,
            [
                'context'      => $contextMock,
                '_helperData'  => $this->helperDataMock,
                '_helperImage' => $this->helperImageMock
            ]
        );
    }
}
