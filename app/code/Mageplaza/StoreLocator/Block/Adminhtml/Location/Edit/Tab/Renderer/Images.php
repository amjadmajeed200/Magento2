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

namespace Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\AbstractBlock;
use Mageplaza\StoreLocator\Block\Adminhtml\Media\Uploader as MediaUploader;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;

/**
 * Class Images
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Template\Edit\Tab\Renderer
 */
class Images extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'location/form/gallery.phtml';

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Images constructor.
     *
     * @param Context $context
     * @param Image $imageHelper
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        Data $helperData,
        array $data = []
    ) {
        $this->_imageHelper = $imageHelper;
        $this->helperData   = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('uploader', MediaUploader::class);

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('mpstorelocator/location/upload')
        )->setFileField(
            'image'
        )->setFilters([
            'images' => [
                'label' => __('Images (.gif, .jpg, .png)'),
                'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
            ],
        ]);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader block
     *
     * @return bool|AbstractBlock
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImagesJson()
    {
        $value = $this->getElement()->getImages();
        if (is_array($value) && count($value)) {
            $mediaDir     = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $images       = $this->sortImagesByPosition($value);
            $baseMediaUrl = $this->_imageHelper->getBaseMediaUrl();
            foreach ($images as $key => &$image) {
                $mediaPath     = $this->_imageHelper->getMediaPath($image['file']);
                $image['url']  = $baseMediaUrl . '/' . $mediaPath;
                $fileHandler   = $mediaDir->stat($mediaPath);
                $image['size'] = $fileHandler['size'];
            }

            return $this->helperData->jsEncode($images);
        }

        return '[]';
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

    /**
     * Get image types data
     *
     * @return array
     */
    public function getImageTypes()
    {
        return [
            'image' => [
                'code'  => 'images',
                'value' => $this->getElement()->getDataObject()->getImages(),
                'label' => 'Template Images',
                'scope' => 'Template Images',
                'name'  => 'template-images',
            ]
        ];
    }

    /**
     * Retrieve JSON data
     *
     * @return string
     */
    public function getImageTypesJson()
    {
        return '[]';
    }
}
