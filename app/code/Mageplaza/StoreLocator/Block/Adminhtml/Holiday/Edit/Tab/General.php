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

namespace Mageplaza\StoreLocator\Block\Adminhtml\Holiday\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\StoreLocator\Model\Holiday;

/**
 * Class General
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Holiday\Edit\Tab
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
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Yesno $yesNo,
        Store $systemStore,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->_yesNo      = $yesNo;
        $this->systemStore = $systemStore;
        $this->_dateTime   = $dateTime;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Holiday $holiday */
        $holiday = $this->_coreRegistry->registry('mageplaza_storelocator_holiday');

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
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_yesNo->toOptionArray()
        ]);
        if (!$holiday->hasData('status')) {
            $holiday->setStatus(1);
        }

        $fieldset->addField('from', 'date', [
            'name'        => 'from',
            'label'       => __('From Date'),
            'title'       => __('From Date'),
            'date_format' => 'M/d/yyyy',
            'timezone'    => false,
            'class'       => 'validate-date validate-date-range date-range-task_data-from'
        ]);

        $fieldset->addField('to', 'date', [
            'name'        => 'to',
            'label'       => __('To Date'),
            'title'       => __('To Date'),
            'date_format' => 'M/d/yyyy',
            'timezone'    => false,
            'class'       => 'validate-date validate-date-range date-range-task_data-to'
        ]);

        $form->addValues($holiday->getData());
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
