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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\Images;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;
use Mageplaza\StoreLocator\Model\Location;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ImagesTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab\Renderer
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
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var Location|MockObject
     */
    private $locationMock;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystemMock;

    /**
     * @var Image|MockObject
     */
    private $imageHelperMock;

    public function testGetImagesJson()
    {
        $images = [
            [
                'position' => '2',
                'file'     => '/i/m/image2.png',
                'label'    => '',
                'url'      => '',
            ],
            [
                'position' => '1',
                'file'     => '/i/m/image1.png',
                'label'    => '',
                'url'      => '',
            ],
            [
                'position' => '3',
                'file'     => '/i/m/image3.png',
                'label'    => '',
                'url'      => '',
            ],
        ];
        $this->locationMock->expects($this->once())->method('getImages')->willReturn($images);

        $mediaDirectoryMock = $this->getMockBuilder(WriteInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystemMock
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $images = $this->sortImagesByPosition($images);

        $baseMediaUrl = '/var/www/html';
        $this->imageHelperMock
            ->method('getBaseMediaUrl')
            ->willReturn($baseMediaUrl);
        $mediaPath1 = 'pub/media/i/m/image1.png';
        $mediaPath2 = 'pub/media/i/m/image2.png';
        $mediaPath3 = 'pub/media/i/m/image3.png';
        $this->imageHelperMock->expects($this->exactly(3))
            ->method('getMediaPath')
            ->withConsecutive(['/i/m/image1.png'], ['/i/m/image2.png'], ['/i/m/image3.png'])
            ->willReturnOnConsecutiveCalls(
                $mediaPath1,
                $mediaPath2,
                $mediaPath3
            );

        $images[0]['url'] = $baseMediaUrl . '/' . $mediaPath1;
        $images[1]['url'] = $baseMediaUrl . '/' . $mediaPath2;
        $images[2]['url'] = $baseMediaUrl . '/' . $mediaPath3;
        $mediaDirectoryMock->expects($this->exactly(3))
            ->method('stat')
            ->withConsecutive([$mediaPath1], [$mediaPath2], [$mediaPath3])
            ->willReturnOnConsecutiveCalls(['size' => 10], ['size' => 20], ['size' => 30]);

        $images[0]['size'] = 10;
        $images[1]['size'] = 20;
        $images[2]['size'] = 30;

        $result = '[{"position":"1","file":"\/i\/m\/image1.png","label":"","url":"\/var\/www\/html\/pub\/media\/i\/m\/image1.png","size":10},{"position":"2","file":"\/i\/m\/image2.png","label":"","url":"\/var\/www\/html\/pub\/media\/i\/m\/image2.png","size":20},{"position":"3","file":"\/i\/m\/image3.png","label":"","url":"\/var\/www\/html\/pub\/media\/i\/m\/image3.png","size":30}]';
        $this->helperDataMock
            ->method('jsEncode')
            ->with($images)
            ->willReturn($result);

        $this->assertEquals($result, $this->imagesBlock->getImagesJson());
    }

    /**
     * Sort images array by position key
     *
     * @param array $images
     *
     * @return array
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
        }

        return $images;
    }

    protected function setUp()
    {
        $this->contextMock     = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock  = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->imageHelperMock = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationMock    = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->filesystemMock  = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();

        $this->contextMock->method('getFilesystem')->willReturn($this->filesystemMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->imagesBlock   = $objectManagerHelper->getObject(
            Images::class,
            [
                'context'      => $this->contextMock,
                '_imageHelper' => $this->imageHelperMock,
                'helperData'   => $this->helperDataMock,
                'data'         => [
                    'element' => $this->locationMock
                ]
            ]
        );
    }
}
