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

namespace Mageplaza\StoreLocator\Test\Unit\Plugin\Block\Order;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\ImportExport\Controller\Adminhtml\Import\Download as ObserveDownload;
use Mageplaza\StoreLocator\Plugin\Controller\Import\Download;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DownloadTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Block\Order
 */
class DownloadTest extends TestCase
{
    /**
     * @var Download
     */
    private $object;

    /**
     * @var FileFactory|MockObject
     */
    private $fileFactoryMock;

    /**
     * @var RawFactory|MockObject
     */
    private $resultRawFactoryMock;

    /**
     * @var ReadFactory|MockObject
     */
    private $readFactoryMock;

    /**
     * @var ComponentRegistrar|MockObject
     */
    private $componentRegistrarMock;

    /**
     * @var Http|MockObject
     */
    private $requestMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Callable
     */
    private $proceedMock;

    /**
     * @var ObserveDownload|MockObject
     */
    private $subjectMock;

    /**
     * @var Raw|MockObject
     */
    private $resultRaw;

    public function testBeforeToHtmlWithProceedCall()
    {
        $this->checkMethodExits();
        $this->requestMock->expects($this->once())->method('getParam')->with('filename')->willReturn('123');

        $this->assertEquals($this->resultRaw, $this->object->aroundExecute($this->subjectMock, $this->proceedMock));
    }

    private function checkMethodExits()
    {
        $subjectMethods = get_class_methods(ObserveDownload::class);
        if (!in_array('execute', $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
    }

    public function testBeforeToHtmlWithoutProceedCallAndFileDoesNotExits()
    {
        $this->checkMethodExits();
        $fileName = Download::IMPORT_FILE;
        $this->requestMock->expects($this->once())->method('getParam')
            ->with('filename')->willReturn(Download::IMPORT_FILE);
        $fileName .= '.csv';

        $moduleDir = '';
        $this->componentRegistrarMock->expects($this->once())->method('getPath')
            ->with(ComponentRegistrar::MODULE, Download::SAMPLE_FILES_MODULE)
            ->willReturn($moduleDir);

        $directoryRead = $this->getMockBuilder(ReadInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->readFactoryMock->expects($this->once())->method('create')->with($moduleDir)
            ->willReturn($directoryRead);
        $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
        $filePath         = '';
        $directoryRead->expects($this->once())->method('getRelativePath')
            ->with($fileAbsolutePath)->willReturn($filePath);
        $directoryRead->expects($this->once())->method('isFile')
            ->with($filePath)->willReturn(false);

        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with(__('There is no sample file for this entity.'))->willReturnSelf();
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')->willReturn($resultRedirect);
        $resultRedirect->expects($this->once())->method('setPath')
            ->with('*/import')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->aroundExecute($this->subjectMock, $this->proceedMock));
    }

    public function testBeforeToHtmlWithoutProceedCall()
    {
        $this->checkMethodExits();
        $fileName = Download::IMPORT_FILE;
        $this->requestMock->expects($this->once())->method('getParam')
            ->with('filename')->willReturn(Download::IMPORT_FILE);
        $fileName .= '.csv';

        $moduleDir = '';
        $this->componentRegistrarMock->expects($this->once())->method('getPath')
            ->with(ComponentRegistrar::MODULE, Download::SAMPLE_FILES_MODULE)
            ->willReturn($moduleDir);

        $directoryRead = $this->getMockBuilder(ReadInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->readFactoryMock->expects($this->once())->method('create')->with($moduleDir)
            ->willReturn($directoryRead);
        $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
        $filePath         = '';
        $directoryRead->expects($this->once())->method('getRelativePath')
            ->with($fileAbsolutePath)->willReturn($filePath);
        $directoryRead->expects($this->once())->method('isFile')
            ->with($filePath)->willReturn(true);

        $fileStat = [];
        $directoryRead->expects($this->once())->method('stat')->with($filePath)->willReturn($fileStat);
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->fileFactoryMock->expects($this->once())->method('create')
            ->with(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                null
            )->willReturn($response);

        $resultRaw = $this->getMockBuilder(Raw::class)->disableOriginalConstructor()->getMock();
        $this->resultRawFactoryMock->expects($this->once())->method('create')->willReturn($resultRaw);
        $directoryRead->expects($this->once())->method('readFile')->with($filePath)->willReturn('');
        $resultRaw->expects($this->once())->method('setContents')->with('')->willReturnSelf();

        $this->assertEquals($resultRaw, $this->object->aroundExecute($this->subjectMock, $this->proceedMock));
    }

    protected function setUp()
    {
        $this->fileFactoryMock           = $this->getMockBuilder(FileFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRawFactoryMock      = $this->getMockBuilder(RawFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->readFactoryMock           = $this->getMockBuilder(ReadFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->componentRegistrarMock    = $this->getMockBuilder(ComponentRegistrar::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock               = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock        = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->subjectMock               = $this->getMockBuilder(ObserveDownload::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRaw                 = $this->getMockBuilder(Raw::class)
            ->disableOriginalConstructor()->getMock();
        $this->proceedMock               = function () {
            return $this->resultRaw;
        };

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            Download::class,
            [
                '_fileFactory'           => $this->fileFactoryMock,
                '_resultRawFactory'      => $this->resultRawFactoryMock,
                '_readFactory'           => $this->readFactoryMock,
                '_componentRegistrar'    => $this->componentRegistrarMock,
                '_request'               => $this->requestMock,
                '_resultRedirectFactory' => $this->resultRedirectFactoryMock,
                '_messageManager'        => $this->messageManagerMock,
            ]
        );
    }
}
