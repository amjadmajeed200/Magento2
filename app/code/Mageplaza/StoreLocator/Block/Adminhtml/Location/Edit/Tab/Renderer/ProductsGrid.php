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
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationModel;

/**
 * Class ProductsGrid
 * @package Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\Renderer
 */
class ProductsGrid extends Extended
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LocationFactory
     */
    protected $_locationFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var LocationModel
     */
    protected $_locationModel;

    /**
     * ProductsGrid constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param ProductFactory $productFactory
     * @param Registry $coreRegistry
     * @param LocationFactory $locationFactory
     * @param ManagerInterface $messageManager
     * @param LocationModel $locationModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ProductFactory $productFactory,
        Registry $coreRegistry,
        LocationFactory $locationFactory,
        ManagerInterface $messageManager,
        LocationModel $locationModel,
        array $data = []
    ) {
        $this->_productFactory  = $productFactory;
        $this->_coreRegistry    = $coreRegistry;
        $this->_locationFactory = $locationFactory;
        $this->messageManager   = $messageManager;
        $this->_locationModel   = $locationModel;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter('["in_product" => 1]');
        }
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->getSelect()->joinLeft(
            ['cpsl' => $collection->getTable('catalog_product_super_link')],
            'e.entity_id = cpsl.product_id'
        )->where('cpsl.product_id IS NULL');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'in_product',
                'align'            => 'center',
                'index'            => 'entity_id',
                'values'           => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header'           => __('Product ID'),
                'type'             => 'number',
                'index'            => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header'   => __('Name'),
                'index'    => 'name',
                'type'     => 'text',
                'sortable' => true
            ]
        );

        $this->addColumn(
            'price',
            [
                'header'           => __('Price'),
                'column_css_class' => 'price',
                'type'             => 'currency',
                'currency_code'    => $this->_storeManager->getStore()->getBaseCurrencyCode(),
                'index'            => 'price',
                'renderer'         => Price::class
            ]
        );

        $this->addColumn('position', [
            'header'           => __('Position'),
            'name'             => 'position',
            'header_css_class' => 'hidden',
            'column_css_class' => 'hidden',
            'validate_class'   => 'validate-number',
            'index'            => 'position',
            'editable'         => true,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/location/productsgrid', ['_current' => true]);
    }

    /**
     * @param Product|DataObject $row
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * @param Column $column
     *
     * @return $this|Extended
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_product') {
            try {
                $productIds = $this->_getSelectedProducts();
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return Location
     */
    protected function getLocation()
    {
        $locationId = $this->getRequest()->getParam('id');
        $location   = $this->_locationFactory->create();
        if ($locationId) {
            $location->load($locationId);

            return $location;
        }

        return null;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSelectedProducts()
    {
        $selected = $this->_getSelectedProducts();

        if (!is_array($selected)) {
            $selected = [];
        }

        return $selected;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getSelectedProducts()
    {
        $location = $this->getRequest()->getPost('pickup_products');

        if (is_array($location)) {
            return $location;
        }

        $productIds = [];
        $location   = $this->getLocation();

        if ($location) {
            $locationId     = $location->getId();
            $locationData   = $this->_locationFactory->create()->getCollection()
                ->addFieldToFilter('location_id', $locationId)
                ->getFirstItem();
            $selectProducts = explode('&', $locationData->getData('product_ids'));
            if ($selectProducts && count($selectProducts) > 0) {
                foreach ($selectProducts as $productId) {
                    $productIds[] = $productId;
                }
            }
        }

        return $productIds;
    }
}
