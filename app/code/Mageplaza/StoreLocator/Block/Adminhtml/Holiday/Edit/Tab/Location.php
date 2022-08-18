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

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Holiday\Edit\Tab
 */
class Location extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    public $locationCollectionFactory;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param CollectionFactory $locationCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        CollectionFactory $locationCollectionFactory,
        array $data = []
    ) {
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->coreRegistry              = $coreRegistry;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('location_grid');
        $this->setDefaultSort('location_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getHoliday()->getId()) {
            $this->setDefaultFilter('["in_locations" => 1]');
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->locationCollectionFactory->create();
        $collection->addFieldToSelect('*');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_locations', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_location',
            'values'           => $this->_getSelectedLocations(),
            'align'            => 'center',
            'index'            => 'location_id'
        ]);
        $this->addColumn('location_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'location_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);
        $this->addColumn('name', [
            'header'           => __('Name'),
            'index'            => 'name',
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
        $this->addColumn('updated_at', [
            'header'           => __('Updated At'),
            'index'            => 'updated_at',
            'type'             => 'date',
            'header_css_class' => 'col-updated',
            'column_css_class' => 'col-updated',
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
     * Get selected location
     *
     * @return array
     * @throws LocalizedException
     */
    protected function _getSelectedLocations()
    {
        $locations = $this->getRequest()->getPost('holiday_locations', null);
        if (!is_array($locations)) {
            $locations = $this->getHoliday()->getLocationIds();

            return array_combine($locations, $locations);
        }

        return $locations;
    }

    /**
     * Get selected locations. This is callback function when clicking filter location
     *
     * @return array
     * @throws LocalizedException
     */
    public function getSelectedLocations()
    {
        $selected = $this->getHoliday()->getLocationIds();
        if (!is_array($selected)) {
            $selected = [];
        }

        return array_combine($selected, $selected);
    }

    /**
     * @param Product|DataObject $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/locationsGrid', ['holiday_id' => $this->getHoliday()->getId()]);
    }

    /**
     * @return Holiday
     */
    public function getHoliday()
    {
        return $this->coreRegistry->registry('mageplaza_storelocator_holiday');
    }

    /**
     * @param Column $column
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_locations') {
            $locationIds = $this->_getSelectedLocations();
            if (empty($locationIds)) {
                $locationIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('location_id', ['in' => $locationIds]);
            } elseif ($locationIds) {
                $this->getCollection()->addFieldToFilter('location_id', ['nin' => $locationIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Location');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mpstorelocator/holiday/locations', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
