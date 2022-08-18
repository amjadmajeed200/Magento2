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

namespace Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\Images as RendererImages;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location;

/**
 * Class Images
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab
 */
class Images extends Generic implements TabInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Images constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->addChild('content', RendererImages::class);

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        /* @var $content Content */
        $content = $this->getChildBlock('content');
        $content->setId('media_gallery_content')->setElement($this);
        $content->setFormName('edit_form');

        return $content->toHtml();
    }

    /**
     * Retrieve data object related with form
     *
     * @return Location
     */
    public function getDataObject()
    {
        return $this->_coreRegistry->registry('mageplaza_storelocator_location');
    }

    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        $images = $this->getDataObject()->getImages();
        if ($images) {
            try {
                $images = $this->helperData->jsDecode($images);
            } catch (Exception $e) {
                $images = [];
            }
        }

        return $images;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'images';
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Images');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Images');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
