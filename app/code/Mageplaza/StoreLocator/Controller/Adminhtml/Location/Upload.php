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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;

/**
 * Class Upload
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class Upload extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Image
     */
    protected $_imageHelper;

    protected $_mediaDirectory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Upload constructor.
     *
     * @param Action\Context $context
     * @param RawFactory $resultRawFactory
     * @param Image $imageHelper
     * @param Filesystem $mediaDirectory
     * @param Data $helperData
     */
    public function __construct(
        Action\Context $context,
        RawFactory $resultRawFactory,
        Image $imageHelper,
        Filesystem $mediaDirectory,
        Data $helperData
    ) {
        parent::__construct($context);

        $this->resultRawFactory = $resultRawFactory;
        $this->_imageHelper     = $imageHelper;
        $this->_mediaDirectory  = $mediaDirectory;
        $this->helperData       = $helperData;
    }

    /**
     * @return Raw
     */
    public function execute()
    {
        try {
            /** @var Uploader $uploader */
            $uploader = $this->_objectManager->create(
                Uploader::class,
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $mediaDirectory = $this->_mediaDirectory->getDirectoryRead(DirectoryList::MEDIA);
            $result         = $uploader->save(
                $mediaDirectory->getAbsolutePath($this->_imageHelper->getBaseTmpMediaPath())
            );

            unset($result['tmp_name'], $result['path']);

            $result['url']  = $this->_imageHelper->getTmpMediaUrl($result['file']);
            $result['file'] .= '.tmp';
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents($this->helperData->jsEncode($result));

        return $response;
    }
}
