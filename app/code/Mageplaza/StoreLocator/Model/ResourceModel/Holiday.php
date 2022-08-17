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

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Holiday
 * @package Mageplaza\StoreLocator\Model\ResourceModel
 */
class Holiday extends AbstractDb
{
    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var string
     */
    protected $_locationHolidayTable;

    /**
     * Holiday constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        $connectionName = null
    ) {
        $this->_eventManager = $eventManager;

        parent::__construct($context, $connectionName);

        $this->_locationHolidayTable = $this->getTable('mageplaza_storelocator_location_holiday');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_storelocator_holiday', 'holiday_id');
    }

    /**
     * @inheritdoc
     *
     * @param \Mageplaza\StoreLocator\Model\Holiday $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveLocationRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Holiday $holiday
     *
     * @return array
     */
    public function getLocationIds(\Mageplaza\StoreLocator\Model\Holiday $holiday)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->_locationHolidayTable,
            'location_id'
        )
            ->where(
                'holiday_id = ?',
                (int) $holiday->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Holiday $holiday
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveLocationRelation(\Mageplaza\StoreLocator\Model\Holiday $holiday)
    {
        $holiday->setIsChangedLocationList(false);
        $id        = $holiday->getId();
        $locations = $holiday->getLocationsIds();

        if ($locations === null) {
            if ($holiday->getIsLocationGrid()) {
                $locations = [];
            } else {
                return $this;
            }
        }

        $locations    = array_keys($locations);
        $oldLocations = $holiday->getLocationIds();
        $insert       = array_diff($locations, $oldLocations);
        $delete       = array_diff($oldLocations, $locations);
        $adapter      = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['location_id IN(?)' => $delete, 'holiday_id=?' => $id];
            $adapter->delete($this->_locationHolidayTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $locationId) {
                $data[] = [
                    'holiday_id'  => (int) $id,
                    'location_id' => (int) $locationId
                ];
            }
            $adapter->insertMultiple($this->_locationHolidayTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $locationIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'mageplaza_storelocator_holiday_change_locations',
                ['holiday' => $holiday, 'location_ids' => $locationIds]
            );

            $holiday->setIsChangedLocationList(true);
            $locationIds = array_keys(array_merge($insert, $delete));
            $holiday->setAffectedLocationIds($locationIds);
        }

        return $this;
    }
}
