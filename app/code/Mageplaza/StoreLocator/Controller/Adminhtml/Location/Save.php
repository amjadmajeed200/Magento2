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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Helper\Image;
use Mageplaza\StoreLocator\Model\LocationFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class Save extends Location
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var File
     */
    protected $_file;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Js $jsHelper
     * @param DateTime $date
     * @param LocationFactory $locationFactory
     * @param Image $imageHelper
     * @param HelperData $helperData
     * @param Session $backendSession
     * @param ProductCollectionFactory $collectionFactory
     * @param File $file
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        DateTime $date,
        LocationFactory $locationFactory,
        Image $imageHelper,
        HelperData $helperData,
        Session $backendSession,
        ProductCollectionFactory $collectionFactory,
        File $file
    ) {
        $this->jsHelper                 = $jsHelper;
        $this->date                     = $date;
        $this->_imageHelper             = $imageHelper;
        $this->_helperData              = $helperData;
        $this->mediaDirectory           = $imageHelper->getMediaDirectory();
        $this->backendSession           = $backendSession;
        $this->productCollectionFactory = $collectionFactory;
        $this->_file                    = $file;

        parent::__construct($locationFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $this->_uploadImages($data);

            if (isset($data['images'])) {
                $images         = $data['images'];
                $data['images'] = ['images' => $images];
            }

            /** @var \Mageplaza\StoreLocator\Model\Location $location */
            $location = $this->initLocation();
            if (!$location) {
                $resultRedirect->setPath('mpstorelocator/*/');

                return $resultRedirect;
            }
            $this->prepareData($location, $data);

            $this->_eventManager->dispatch(
                'mageplaza_storelocator_location_prepare_save',
                ['location' => $location, 'request' => $this->getRequest()]
            );

            try {
                $location->save();
                $this->messageManager->addSuccessMessage(__('The location has been saved.'));
                $this->_getSession()->setData('mageplaza_storelocator_location_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpstorelocator/*/edit', ['id' => $location->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mpstorelocator/*/');
                }

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Location.'));
            }

            $this->_getSession()->setData('mageplaza_storelocator_location_data', $data);
            $resultRedirect->setPath('mpstorelocator/*/edit', ['id' => $location->getId(), '_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('mpstorelocator/*/');

        return $resultRedirect;
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Location $location
     * @param array $data
     *
     * @return $this
     * @throws Exception
     */
    protected function prepareData($location, $data = [])
    {
        if (!isset($data['general']['is_default_store'])) {
            $data['general']['is_default_store'] = 0;
        }

        $location->addData($data['general']);
        $location->addData($data['location']);
        $helper = $this->_helperData;

        if (isset($data['contact'])) {
            if (isset($data['contact']['website']['use_system_config'])) {
                $data['contact']['website']           = $helper->getConfigGeneral('website');
                $data['contact']['is_config_website'] = 1;
            } else {
                $data['contact']['website']           = $data['contact']['website']['value'];
                $data['contact']['is_config_website'] = 0;
            }

            $location->addData($data['contact']);
        }

        if (isset($data['images'])) {
            $location->addData($data['images']);
        }

        if (isset($data['time'])) {
            $data['time']['operation_mon'] = isset($data['time']['operation_mon']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_mon']);
            $data['time']['operation_tue'] = isset($data['time']['operation_tue']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_tue']);
            $data['time']['operation_wed'] = isset($data['time']['operation_wed']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_wed']);
            $data['time']['operation_thu'] = isset($data['time']['operation_thu']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_thu']);
            $data['time']['operation_fri'] = isset($data['time']['operation_fri']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_fri']);
            $data['time']['operation_sat'] = isset($data['time']['operation_sat']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_sat']);
            $data['time']['operation_sun'] = isset($data['time']['operation_sun']['use_system_config']) ?
                'use_config' :
                $helper->jsEncode($data['time']['operation_sun']);

            if (isset($data['time']['time_zone']['use_system_config'])) {
                $data['time']['time_zone']           = 'use_config';
                $data['time']['is_config_time_zone'] = 1;
            } else {
                $data['time']['time_zone']           = $data['time']['time_zone']['value'];
                $data['time']['is_config_time_zone'] = 0;
            }

            $location->addData($data['time']);
        }

        if ($data['available_products']['is_selected_all_product']) {
            $productIds = $this->getAllProductIdsToString();
            $this->backendSession->setProductIds($productIds);
        }

        if (isset($data['holidays'])) {
            $location->setIsHolidayGrid(true);
            $location->setHolidaysIds(
                $this->jsHelper->decodeGridSerializedInput($data['holidays'])
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAllProductIdsToString()
    {
        $collection = $this->productCollectionFactory->create();

        return implode('&', $collection->getAllIds());
    }

    /**
     * @param $data
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _uploadImages(&$data)
    {
        if (isset($data['images']) && count($data['images'])) {
            $data['images'] = $this->_helperData->jsEncode($this->processImagesGallery($data['images']));
        }

        return $this;
    }

    /**
     * @param array $imageEntries
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function processImagesGallery($imageEntries)
    {
        foreach ($imageEntries as $key => &$image) {
            if (!isset($image['file']) || !$image['file']) {
                unset($imageEntries[$key]);
                continue;
            }

            $fileName = $image['file'];
            $pos      = strpos($fileName, '.tmp');

            if (isset($image['removed']) && $image['removed']) {
                /** Remove image */
                unset($imageEntries[$key]);

                if ($pos === false) {
                    $filePath = $this->_imageHelper->getMediaPath($image['file']);
                    $file     = $this->mediaDirectory->getRelativePath($filePath);
                    if ($this->mediaDirectory->isFile($file)) {
                        $this->mediaDirectory->delete($filePath);
                    }
                }
            } elseif ($pos !== false) {
                /** Move image from tmp folder */
                $fileName = substr($fileName, 0, $pos);
                $filePath = $this->_imageHelper->getTmpMediaPath($fileName);
                $file     = $this->mediaDirectory->getRelativePath($filePath);
                if (!$this->mediaDirectory->isFile($file)) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $pathInfo = $this->_file->getPathInfo($file);
                if (!isset($pathInfo['extension']) ||
                    !in_array(strtolower($pathInfo['extension']), ['jpg', 'jpeg', 'gif', 'png'])) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $fileName       = Uploader::getCorrectFileName($pathInfo['basename']);
                $dispretionPath = Uploader::getDispretionPath($fileName);
                $fileName       = $dispretionPath . '/' . $fileName;

                $fileName        = $this->_imageHelper->getNotDuplicatedFilename($fileName, $dispretionPath);
                $destinationFile = $this->_imageHelper->getMediaPath($fileName);

                try {
                    $this->mediaDirectory->renameFile($file, $destinationFile);
                    $image['file'] = str_replace('\\', '/', $fileName);
                } catch (Exception $e) {
                    throw new LocalizedException(__('We couldn\'t move this file: %1.', $e->getMessage()));
                }
            }

            if (isset($image['removed'])) {
                unset($image['removed']);
            }
        }

        return array_values($imageEntries);
    }
}
