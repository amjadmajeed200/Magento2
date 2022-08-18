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

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday\CollectionFactory;

/**
 * Class Holidays
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer
 */
class Holidays extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_holidayCollectionFactory;

    /**
     * @var Yesno
     */
    public $yesNo;

    /**
     * Holidays constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param Yesno $yesno
     * @param CollectionFactory $holidayCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Yesno $yesno,
        CollectionFactory $holidayCollectionFactory,
        array $data = []
    ) {
        $this->yesNo                     = $yesno;
        $this->_holidayCollectionFactory = $holidayCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('holidaysGrid');
        $this->setDefaultSort('holiday_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getLocation()->getId()) {
            $this->setDefaultFilter('["in_holidays" => 1]');
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->_holidayCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_holidays', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_holiday',
            'values'           => $this->_getSelectedHolidays(),
            'align'            => 'center',
            'index'            => 'holiday_id'
        ]);
        $this->addColumn('holiday_id', [
            'header'           => __('ID'),
            'type'             => 'number',
            'index'            => 'holiday_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);
        $this->addColumn('name', [
            'header'           => __('Name'),
            'index'            => 'name',
            'width'            => '50px',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name',
        ]);
        $this->addColumn('created_at', [
            'header'           => __('Created At'),
            'index'            => 'created_at',
            'type'             => 'date',
            'header_css_class' => 'col-created',
            'column_css_class' => 'col-created',
        ]);
        $this->addColumn('position', [
            'header'           => __('Position'),
            'name'             => 'position',
            'header_css_class' => 'hidden',
            'column_css_class' => 'hidden',
            'validate_class'   => 'validate-number',
            'index'            => 'position',
            'editable'         => true,
        ]);

        return $this;
    }

    /**
     * Get selected holidays
     *
     * @return array
     * @throws LocalizedException
     */
    protected function _getSelectedHolidays()
    {
        $holidays = $this->getRequest()->getPost('location_holidays', null);
        if (!is_array($holidays)) {
            $holidays = $this->getLocation()->getHolidayIds();

            return array_combine($holidays, $holidays);
        }

        return $holidays;
    }

    /**
     * Get selected holidays. This is callback function when clicking filter holiday
     *
     * @return array
     * @throws LocalizedException
     */
    public function getSelectedHolidays()
    {
        $selected = $this->getLocation()->getHolidayIds();
        if (!is_array($selected)) {
            $selected = [];
        }

        return array_combine($selected, $selected);
    }

    /**
     * @param object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/holidays');
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->_backendSession->getData('mageplaza_storelocator_location_model');
    }

    /**
     * @param Column $column
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_holidays') {
            $holidayIds = $this->_getSelectedHolidays();
            if (empty($holidayIds)) {
                $holidayIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('holiday_id', ['in' => $holidayIds]);
            } elseif ($holidayIds) {
                $this->getCollection()->addFieldToFilter('holiday_id', ['nin' => $holidayIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
}
