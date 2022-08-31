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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Images
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab
 */
class AvailableProducts extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        Store $systemStore,
        array $data = []
    ) {
        $this->_yesNo      = $yesNo;
        $this->systemStore = $systemStore;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\StoreLocator\Model\Location $location */
        $location = $this->_coreRegistry->registry('mageplaza_storelocator_location');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('available_products_');
        $form->setFieldNameSuffix('available_products');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Available Products'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('is_show_product_page', 'select', [
            'name'   => 'is_show_product_page',
            'label'  => __('Show on Product Page'),
            'title'  => __('Show on Product Page'),
            'values' => $this->_yesNo->toOptionArray()
        ]);

        $fieldset->addField('is_selected_all_product', 'select', [
            'name'   => 'is_selected_all_product',
            'label'  => __('Select all product'),
            'title'  => __('Select all product'),
            'values' => $this->_yesNo->toOptionArray()
        ])->setAfterElementHtml($this->selectAllJs());

        $form->setValues($location->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get transaction grid url
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mpstorelocator/location/productsgrid');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Available Products');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        $formHtml  = parent::getFormHtml();
        $childHtml = $this->getChildHtml();

        return $formHtml . $childHtml;
    }

    /**
     * @return string
     */
    public function selectAllJs()
    {
        $gridUrl    = $this->getProductGridUrl();
        $locationId = $this->_request->getParam('id');

        return '<script type="text/x-magento-init">
                {
                    "#available_products_is_selected_all_product": {
                        "Mageplaza_StoreLocator/js/is_selected": {
                            "gridUrl": "' . $gridUrl . '",
                            "locationId": "' . $locationId . '"
                        }
                    }
                }
            </script>';
    }

    /**
     * @return string
     */
    public function getProductGridUrl()
    {
        return $this->getUrl('mpstorelocator/location/productsgrid', [
            'form_key' => $this->getFormKey(),
            'loadGrid' => 1
        ]);
    }
}
