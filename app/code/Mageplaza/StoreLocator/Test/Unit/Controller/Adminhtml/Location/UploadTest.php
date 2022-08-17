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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location\Upload;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UploadTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location
 */
class UploadTest extends TestCase
{
    /**
     * @var Upload
     */
    private $object;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * @var Filesystem|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var Image|MockObject
     */
    private $imageHelperMock;

    /**
     * @var RawFactory|MockObject
     */
    private $resultRawFactoryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    public function testExecuteThrowException()
    {
        $uploader = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->expects($this->once())->method('create')
            ->with(Uploader::class, ['fileId' => 'image'])
            ->willReturn($uploader);
        $uploader->expects($this->once())->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png'])->willReturnSelf();
        $uploader->expects($this->once())->method('setAllowRenameFiles')
            ->with(true)->willReturnSelf();
        $uploader->expects($this->once())->method('setFilesDispersion')
            ->with(true)->willReturnSelf();
        $mediaDirectory = $this->getMockForAbstractClass(ReadInterface::class);
        $this->mediaDirectoryMock->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectory);
        $this->imageHelperMock->expects($this->once())->method('getBaseTmpMediaPath')
            ->willReturn('mageplaza/store_locator/tmp');

        $mediaDirectory->expects($this->once())->method('getAbsolutePath')
            ->with('mageplaza/store_locator/tmp')
            ->willReturn('pub/media/mageplaza/store_locator/tmp');

        $uploader->expects($this->once())->method('save')
            ->with('pub/media/mageplaza/store_locator/tmp')
            ->willReturn([
                'name'     => 'image.png',
                'type'     => 'image/png',
                'tmp_name' => '/tmp/phpPmRwGZ',
                'error'    => 0,
                'size'     => 598,
                'path'     => 'pub/media/mageplaza/store_locator/tmp',
                'file'     => '/i/m/image.png'
            ]);

        $this->imageHelperMock->expects($this->once())->method('getTmpMediaUrl')
            ->with('/i/m/image.png')->willThrowException(new Exception('Exception', 10));

        $response = $this->getMockBuilder(Raw::class)->disableOriginalConstructor()->getMock();
        $this->resultRawFactoryMock->expects($this->once())->method('create')->willReturn($response);
        $response->expects($this->once())->method('setHeader')
            ->with('Content-type', 'text/plain')->willReturnSelf();
        $this->helperDataMock->expects($this->once())->method('jsEncode')
            ->with([
                'error'     => 'Exception',
                'errorcode' => 10
            ])->willReturn('encoded_result');
        $response->expects($this->once())->method('setContents')
            ->with('encoded_result')->willReturnSelf();

        $this->assertEquals($response, $this->object->execute());
    }

    public function testExecute()
    {
        $uploader = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()->getMock();
        $this->objectManagerMock->expects($this->once())->method('create')
            ->with(Uploader::class, ['fileId' => 'image'])
            ->willReturn($uploader);
        $uploader->expects($this->once())->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png'])->willReturnSelf();
        $uploader->expects($this->once())->method('setAllowRenameFiles')
            ->with(true)->willReturnSelf();
        $uploader->expects($this->once())->method('setFilesDispersion')
            ->with(true)->willReturnSelf();
        $mediaDirectory = $this->getMockForAbstractClass(ReadInterface::class);
        $this->mediaDirectoryMock->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectory);
        $this->imageHelperMock->expects($this->once())->method('getBaseTmpMediaPath')
            ->willReturn('mageplaza/store_locator/tmp');

        $mediaDirectory->expects($this->once())->method('getAbsolutePath')
            ->with('mageplaza/store_locator/tmp')
            ->willReturn('pub/media/mageplaza/store_locator/tmp');

        $uploader->expects($this->once())->method('save')
            ->with('pub/media/mageplaza/store_locator/tmp')
            ->willReturn([
                'name'     => 'image.png',
                'type'     => 'image/png',
                'tmp_name' => '/tmp/phpPmRwGZ',
                'error'    => 0,
                'size'     => 598,
                'path'     => 'pub/media/mageplaza/store_locator/tmp',
                'file'     => '/i/m/image.png'
            ]);

        $this->imageHelperMock->expects($this->once())->method('getTmpMediaUrl')
            ->with('/i/m/image.png')
            ->willReturn('image_url');

        $response = $this->getMockBuilder(Raw::class)->disableOriginalConstructor()->getMock();
        $this->resultRawFactoryMock->expects($this->once())->method('create')->willReturn($response);
        $response->expects($this->once())->method('setHeader')
            ->with('Content-type', 'text/plain')->willReturnSelf();
        $this->helperDataMock->expects($this->once())->method('jsEncode')
            ->with([
                'error' => 0,
                'name'  => 'image.png',
                'type'  => 'image/png',
                'size'  => 598,
                'file'  => '/i/m/image.png.tmp',
                'url'   => 'image_url'
            ])->willReturn('encoded_result');
        $response->expects($this->once())->method('setContents')
            ->with('encoded_result')->willReturnSelf();

        $this->assertEquals($response, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock             = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerMock = $this->getMockForAbstractClass(ObjectManagerInterface::class);

        $this->mediaDirectoryMock   = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $this->imageHelperMock      = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRawFactoryMock = $this->getMockBuilder(RawFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock       = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $contextMock->method('getObjectManager')->willReturn($this->objectManagerMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Upload::class,
            [
                'context'          => $contextMock,
                '_mediaDirectory'  => $this->mediaDirectoryMock,
                '_imageHelper'     => $this->imageHelperMock,
                'resultRawFactory' => $this->resultRawFactoryMock,
                'helperData'       => $this->helperDataMock
            ]
        );
    }
}
