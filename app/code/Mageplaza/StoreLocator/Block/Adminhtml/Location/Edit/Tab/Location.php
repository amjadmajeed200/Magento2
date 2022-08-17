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
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Model\Config\Source\Country;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab
 */
class Location extends Generic implements TabInterface
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
     * @var Country
     */
    protected $_countries;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param Country $country
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        Store $systemStore,
        Country $country,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_yesNo      = $yesNo;
        $this->systemStore = $systemStore;
        $this->_countries  = $country;
        $this->_helperData = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\StoreLocator\Model\Location $location */
        $location = $this->_coreRegistry->registry('mageplaza_storelocator_location');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('location_');
        $form->setFieldNameSuffix('location');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Address Information'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('street', 'text', [
            'name'     => 'street',
            'label'    => __('Street'),
            'title'    => __('Street'),
            'required' => true
        ]);

        $fieldset->addField('city', 'text', [
            'name'     => 'city',
            'label'    => __('City'),
            'title'    => __('City'),
            'required' => true
        ]);

        $fieldset->addField('state_province', 'text', [
            'name'     => 'state_province',
            'label'    => __('State/Province'),
            'title'    => __('State/Province'),
            'required' => true
        ]);

        $fieldset->addField('postal_code', 'text', [
            'name'     => 'postal_code',
            'label'    => __('Zip/Postal Code'),
            'title'    => __('Zip/Postal Code'),
            'required' => true
        ]);

        $fieldset->addField('country', 'select', [
            'name'   => 'country',
            'label'  => __('Country'),
            'title'  => __('Country'),
            'values' => $this->_countries->toOptionArray()
        ])->setAfterElementHtml(
            '<button type="button" id="sl_load_review_map"><span>' . __('Get GPS coordinates') . '</span></button>'
        );

        $mapFieldset = $form->addFieldset('map_fieldset', [
            'legend' => __('Review map'),
            'class'  => 'fieldset-wide'
        ]);
        $mapFieldset->addField('latitude', 'text', [
            'name'  => 'latitude',
            'label' => __('Latitude'),
            'title' => __('Latitude')
        ]);
        if (!$location->hasData('latitude')) {
            $location->setLatitude(20.9790643);
        }

        $mapFieldset->addField('longitude', 'text', [
            'name'  => 'longitude',
            'label' => __('Longitude'),
            'title' => __('Longitude')
        ]);
        if (!$location->hasData('longitude')) {
            $location->setLongitude(105.7854772);
        }

        $form->addValues($location->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Location');
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
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
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
     * @return HelperData
     */
    public function getHelper()
    {
        return $this->_helperData;
    }
}
