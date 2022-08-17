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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model\ResourceModel;

use Exception;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\Location as LocationModel;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Model\ResourceModel
 */
class Location extends AbstractDb
{
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Product
     */
    protected $_productResource;

    /**
     * @var MessageInterface
     */
    protected $_messageManager;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param ManagerInterface $eventManager
     * @param Data $helperData
     * @param RequestInterface $request
     * @param Product $productResource
     * @param MessageInterface $messageManager
     * @param CollectionFactory $productCollectionFactory
     * @param Session $backendSession
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        ManagerInterface $eventManager,
        Data $helperData,
        RequestInterface $request,
        Product $productResource,
        MessageInterface $messageManager,
        CollectionFactory $productCollectionFactory,
        Session $backendSession,
        $connectionName = null
    ) {
        $this->_dateTime                 = $dateTime;
        $this->_eventManager             = $eventManager;
        $this->_helperData               = $helperData;
        $this->_request                  = $request;
        $this->_productResource          = $productResource;
        $this->_messageManager           = $messageManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->backendSession            = $backendSession;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_storelocator_location', 'location_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->_dateTime->date());
        }

        $object->setUpdatedAt($this->_dateTime->date());
        $object->setUrlKey(
            $this->_helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param LocationModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->updatePickupLocationAttribute($object);
        $productIds       = $this->_request->getParam('product_ids');
        $availableProduct = $this->_request->getParam('available_products');

        if ($availableProduct) {
            $selectAllProduct = $availableProduct['is_selected_all_product'];
            $showProductPage  = $availableProduct['is_show_product_page'];
            $object->setData('product_ids', $productIds)
                ->setData('is_selected_all_product', $selectAllProduct)
                ->setData('is_show_product_page', $showProductPage);
            $adapter = $this->getConnection();

            $where = ['location_id = ?' => (int) $object->getId()];
            $adapter->update(
                $this->getMainTable(),
                [
                    'product_ids'             => $productIds,
                    'is_selected_all_product' => $selectAllProduct,
                    'is_show_product_page'    => $showProductPage
                ],
                $where
            );
        }

        if ($object->getIsDefaultStore()) {
            $adapter            = $this->getConnection();
            $select             = $adapter->select()
                ->from($this->getMainTable(), 'location_id')
                ->where('location_id <> ?', (int) $object->getId());
            $currentLocationIds = $adapter->fetchCol($select);

            if ($currentLocationIds) {
                $where = ['location_id IN (?)' => $currentLocationIds];
                $adapter->update(
                    $this->getMainTable(),
                    ['is_default_store' => 0],
                    $where
                );
            }
        }

        $this->saveHolidayRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * Update pickup location product attribute
     *
     * @param LocationModel $object
     */
    public function updatePickupLocationAttribute($object)
    {
        $locationData    = $this->_request->getParam('product_ids');
        $oldLocationData = $object->getProductIds();
        $check           = true;

        if ($this->backendSession->getSaveConfig()) {
            $check = false;
            $this->backendSession->unsSaveConfig();
        }

        if ($productIds = $this->backendSession->getProductIds()) {
            $locationData = $productIds;
            $check        = false;
            $this->backendSession->unsProductIds();
        }

        if ($locationData || $oldLocationData) {
            $locationId        = $object->getLocationId();
            $key               = $object->getUrlKey() . '-' . $locationId;
            $newProductIds     = $locationData ? explode('&', $locationData) : [];
            $productCollection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => $newProductIds]);

            /** set data for mp_pickup_locations attribute when save */
            foreach ($productCollection->getItems() as $product) {
                $pickupLocations = explode(',', $product->getData('mp_pickup_locations'));

                if (!in_array($key, $pickupLocations, true)) {
                    $pickupLocations[] = $key;
                }

                $product->setData('mp_pickup_locations', implode(',', $pickupLocations));
                $this->saveAttribute($product, 'mp_pickup_locations');
            }

            if ($check !== false) {
                $this->uncheckProductGrid($object, $key);
            }
        }
    }

    /**
     * update pickup location product attribute when uncheck
     *
     * @param LocationModel $object
     * @param $key
     */
    public function uncheckProductGrid($object, $key)
    {
        $productIds    = $this->_request->getParam('product_ids');
        $newProductIds = $productIds ? explode('&', $productIds) : [];
        $collection    = $this->_productCollectionFactory->create();
        if ($object->getIsSelectedAllProduct() === '1') {
            $oldProductIds = $collection->getAllIds();
        } else {
            $oldProductIdsString = $object->getData('product_ids');
            $oldProductIds       = $oldProductIdsString ? explode('&', $oldProductIdsString) : [];
        }
        $diffProductIds = array_diff($oldProductIds, $newProductIds);

        /** unset pickup store when un checkbox */
        if ($diffProductIds && $this->isSaveAction()) {
            $productCollection = $collection->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', ['in' => $diffProductIds]);

            foreach ($productCollection->getItems() as $product) {
                $pickupLocations = explode(',', $product->getData('mp_pickup_locations'));
                unset($pickupLocations[array_search($key, $pickupLocations, true)]);
                $pickupLocations = array_values($pickupLocations);
                if (empty($pickupLocations) || $pickupLocations[0] === '') {
                    $pickupLocations = [0];
                }
                $product->setData('mp_pickup_locations', implode(',', $pickupLocations));
                $this->saveAttribute($product, 'mp_pickup_locations');
            }
        }
    }

    /**
     * @param $product
     * @param $attribute
     */
    public function saveAttribute($product, $attribute)
    {
        try {
            $this->_productResource->saveAttribute($product, $attribute);
        } catch (Exception $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function isSaveAction()
    {
        return !in_array(
            $this->_request->getFullActionName(),
            [
                'mpstorelocator_location_edit',
                'adminhtml_system_config_save'
            ]
        );
    }

    /**
     * @param LocationModel $location
     *
     * @return array
     */
    public function getHolidayIds(LocationModel $location)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getTable('mageplaza_storelocator_location_holiday'), 'holiday_id')
            ->where('location_id = ?', (int) $location->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * @param LocationModel $location
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveHolidayRelation(LocationModel $location)
    {
        $location->setIsChangedHolidayList(false);
        $id       = $location->getId();
        $holidays = $location->getHolidaysIds();

        if ($holidays === null) {
            if ($location->getIsHolidayGrid()) {
                $holidays = [];
            } else {
                return $this;
            }
        }

        $holidays    = array_keys($holidays);
        $oldHolidays = $location->getHolidayIds();
        $insert      = array_diff($holidays, $oldHolidays);
        $delete      = array_diff($oldHolidays, $holidays);
        $adapter     = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['holiday_id IN(?)' => $delete, 'location_id=?' => $id];
            $adapter->delete($this->getTable('mageplaza_storelocator_location_holiday'), $condition);
        }

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $holidayId) {
                $data[] = [
                    'location_id' => (int) $id,
                    'holiday_id'  => (int) $holidayId
                ];
            }
            $adapter->insertMultiple($this->getTable('mageplaza_storelocator_location_holiday'), $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $holidayIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_storelocator_location_change_holidays',
                ['location' => $location, 'holiday_ids' => $holidayIds]
            );

            $location->setIsChangedHolidayList(true);
            $holidayIds = array_keys(array_merge($insert, $delete));
            $location->setAffectedHolidayIds($holidayIds);
        }

        return $this;
    }

    /**
     * @param $urlKey
     *
     * @return string
     * @throws LocalizedException
     */
    public function isDuplicateUrlKey($urlKey)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'location_id')
            ->where('url_key = :url_key');
        $binds   = ['url_key' => $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param $locationId
     *
     * @return array
     */
    public function getHolidayIdsByLocation($locationId)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getTable('mageplaza_storelocator_location_holiday'), 'holiday_id')
            ->where('location_id = ?', $locationId);

        return $adapter->fetchCol($select);
    }
}
