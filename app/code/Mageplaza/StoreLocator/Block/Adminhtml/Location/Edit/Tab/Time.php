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
use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer\Time as RendererTime;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Config\Source\System\OpenClose;

/**
 * Class Time
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab
 */
class Time extends Generic implements TabInterface
{
    /**
     * @var OpenClose
     */
    protected $_openClose;

    /**
     * @var Timezone
     */
    protected $_timeZones;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Time constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param OpenClose $openClose
     * @param Timezone $timezone
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        OpenClose $openClose,
        Timezone $timezone,
        Data $helperData,
        array $data = []
    ) {
        $this->_openClose  = $openClose;
        $this->_timeZones  = $timezone;
        $this->_helperData = $helperData;

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

        $form->setHtmlIdPrefix('time_');
        $form->setFieldNameSuffix('time');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Open Hours'),
            'class'  => 'fieldset-wide'
        ]);

        if (!$location->hasData('operation_mon')) {
            $location->setOperationMon($this->_helperData->getConfigOpenTime(Data::MONDAY));
        }
        $fieldset->addField('operation_mon', 'select', [
            'name'   => 'operation_mon[value]',
            'label'  => __('Monday'),
            'title'  => __('Monday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_mon', $location));

        if (!$location->hasData('operation_tue')) {
            $location->setOperationTue($this->_helperData->getConfigOpenTime(Data::TUESDAY));
        }
        $fieldset->addField('operation_tue', 'select', [
            'name'   => 'operation_tue[value]',
            'label'  => __('Tuesday'),
            'title'  => __('Tuesday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_tue', $location));

        if (!$location->hasData('operation_wed')) {
            $location->setOperationWed($this->_helperData->getConfigOpenTime(Data::WEDNESDAY));
        }
        $fieldset->addField('operation_wed', 'select', [
            'name'   => 'operation_wed[value]',
            'label'  => __('Wednesday'),
            'title'  => __('Wednesday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_wed', $location));

        if (!$location->hasData('operation_thu')) {
            $location->setOperationThu($this->_helperData->getConfigOpenTime(Data::THURSDAY));
        }
        $fieldset->addField('operation_thu', 'select', [
            'name'   => 'operation_thu[value]',
            'label'  => __('Thursday'),
            'title'  => __('Thursday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_thu', $location));

        if (!$location->hasData('operation_fri')) {
            $location->setOperationFri($this->_helperData->getConfigOpenTime(Data::FRIDAY));
        }
        $fieldset->addField('operation_fri', 'select', [
            'name'   => 'operation_fri[value]',
            'label'  => __('Friday'),
            'title'  => __('Friday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_fri', $location));

        if (!$location->hasData('operation_sat')) {
            $location->setOperationSat($this->_helperData->getConfigOpenTime(Data::SATURDAY));
        }
        $fieldset->addField('operation_sat', 'select', [
            'name'   => 'operation_sat[value]',
            'label'  => __('Saturday'),
            'title'  => __('Saturday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_sat', $location));

        if (!$location->hasData('operation_sun')) {
            $location->setOperationSun($this->_helperData->getConfigOpenTime(Data::SUNDAY));
        }
        $fieldset->addField('operation_sun', 'select', [
            'name'   => 'operation_sun[value]',
            'label'  => __('Sunday'),
            'title'  => __('Sunday'),
            'values' => $this->_openClose->toOptionArray()
        ])->setAfterElementHtml($this->getTimeHtml('operation_sun', $location));

        //set Timezone if use system config
        $location->setTimeZone($location->getTimeZone());

        if (is_null($location->getData('time_zone'))) {
            $location->setTimeZone($this->_helperData->getStoreTimeSetting('time_zone'));
        }
        if (!$location->hasData('is_config_time_zone')) {
            $location->setIsConfigTimeZone(1);
        }
        $isConfigTimeZone = $location->getIsConfigTimeZone() ? 'checked' : '';
        $fieldset->addField('time_zone', 'select', [
            'name'   => 'time_zone[value]',
            'label'  => __('Timezone'),
            'title'  => __('Timezone'),
            'values' => $this->_timeZones->toOptionArray()
        ])->setAfterElementHtml(
            '<input class="mp-time-zone-useconfig" id="mp-time-zone-useconfig"
            name="time[time_zone][use_system_config]" value="0" type="checkbox" ' . $isConfigTimeZone . '>
            <label class="mp-time-zone-useconfig-label" for="mp-time-zone-useconfig">Use Config</label>'
        );

        $form->addFieldset('holiday_fieldset', [
            'legend' => __('Holiday'),
            'class'  => 'fieldset-wide'
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
        return __('Time');
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
     * Get open/close time html
     *
     * @param $name
     * @param $location
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getTimeHtml($name, $location)
    {
        return $this->getLayout()
            ->createBlock(RendererTime::class)
            ->setName($name)
            ->setLocationObject($location)
            ->toHtml();
    }
}
