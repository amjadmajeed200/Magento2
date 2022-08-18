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

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class General
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab
 */
class General extends Generic implements TabInterface
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

        $form->setHtmlIdPrefix('general_');
        $form->setFieldNameSuffix('general');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Visibility'),
            'title'  => __('Visibility'),
            'values' => $this->_yesNo->toOptionArray()
        ]);
        if (!$location->hasData('status')) {
            $location->setStatus(1);
        }

        $fieldset->addField('description', 'textarea', [
            'name'  => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'note'  => __('For internal use, this field is visible to admins.'),
        ]);

        $fieldset->addField('url_key', 'text', [
            'name'  => 'url_key',
            'label' => __('URL Key'),
            'title' => __('URL Key')
        ]);

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()
                ->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$location->hasData('store_ids')) {
                $location->setStoreIds(0);
            }
        }

        $fieldset->addField('sort_order', 'text', [
            'name'  => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits',
            'value' => '0',
            'note'  => __('The sort order of the location.'),
        ]);

        $fieldset->addField('is_default_store', 'checkbox', [
            'name'     => 'is_default_store',
            'label'    => __('Is Default Location'),
            'title'    => __('Is Default Location'),
            'checked'  => $location->getIsDefaultStore() ? true : false,
            'onchange' => 'this.value = this.checked ? 1 : 0;',
            'value'    => $location->getIsDefaultStore()
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
        return __('General');
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
