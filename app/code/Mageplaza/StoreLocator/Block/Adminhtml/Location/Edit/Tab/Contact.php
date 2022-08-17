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
use Mageplaza\StoreLocator\Helper\Data;

/**
 * Class Contact
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab
 */
class Contact extends Generic implements TabInterface
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
     * @var Data
     */
    protected $_helperData;

    /**
     * Contact constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        Store $systemStore,
        Data $helperData,
        array $data = []
    ) {
        $this->_yesNo      = $yesNo;
        $this->systemStore = $systemStore;
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

        $form->setHtmlIdPrefix('contact_');
        $form->setFieldNameSuffix('contact');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Contact'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('phone_one', 'text', [
            'name'  => 'phone_one',
            'label' => __('Phone #1'),
            'title' => __('Phone #1'),
            'class' => 'validate-digits',
            'style' => 'width: 80%;'
        ]);

        $fieldset->addField('phone_two', 'text', [
            'name'  => 'phone_two',
            'label' => __('Phone #2'),
            'title' => __('Phone #2'),
            'class' => 'validate-digits',
            'style' => 'width: 80%;'
        ]);

        if (!$location->hasData('website')) {
            $location->setWebsite($this->_helperData->getConfigGeneral('website'));
        }
        if (!$location->hasData('is_config_website')) {
            $location->setIsConfigWebsite(1);
        }
        $isConfigWebsite = $location->getIsConfigWebsite() ? 'checked' : '';
        $fieldset->addField('website', 'text', [
            'name'  => 'website[value]',
            'label' => __('Website'),
            'title' => __('Website'),
            'note'  => __('Please enter the full URL. (For example https://mageplaza.com)'),
            'style' => 'width: 80%;margin-right: 20px;',
            'class' => 'validate-url'
        ])->setAfterElementHtml(
            '<input class="mp-website-useconfig" id="mp-website-useconfig"
            name="contact[website][use_system_config]"
            value="0" type="checkbox" ' . $isConfigWebsite . '>
            <label class="mp-website-useconfig-label" for="mp-website-useconfig">Use Config</label>'
        );

        $fieldset->addField('facebook', 'text', [
            'name'  => 'facebook',
            'label' => __('Facebook'),
            'title' => __('Facebook'),
            'note'  => __('Please enter the full URL. (For example https://www.facebook.com)'),
            'style' => 'width: 80%;margin-right: 20px;',
            'class' => 'validate-url'
        ]);

        $fieldset->addField('twitter', 'text', [
            'name'  => 'twitter',
            'label' => __('Twitter'),
            'title' => __('Twitter'),
            'note'  => __('Please enter the full URL. (For example https://www.twitter.com)'),
            'style' => 'width: 80%;margin-right: 20px;',
            'class' => 'validate-url'
        ]);

        $fieldset->addField('fax', 'text', [
            'name'  => 'fax',
            'label' => __('Fax'),
            'title' => __('Fax'),
            'class' => 'validate-fax',
            'style' => 'width: 80%;'
        ]);

        $fieldset->addField('email', 'text', [
            'name'  => 'email',
            'label' => __('Email'),
            'title' => __('Email'),
            'class' => 'validate-email',
            'style' => 'width: 80%;'
        ]);

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
        return __('Contact');
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
}
