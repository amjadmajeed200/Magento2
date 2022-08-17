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

namespace Mageplaza\StoreLocator\Model\ResourceModel\Holiday\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\StoreLocator\Model\ResourceModel\Holiday\Grid
 */
class Collection extends SearchResult
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->_getLocationNum();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'location_num') {
            $condition = str_replace('%', '', $condition['like']);
            if (is_numeric($condition)) {
                $this->getSelect()->having("COUNT(`mpslh`.`holiday_id`) = {$condition}");
            } else {
                $this->getSelect()->having("COUNT(`mpslh`.`holiday_id`) LIKE '{$condition}'");
            }

            return $this;
        }

        if ($field === 'holiday_id') {
            $field = 'main_table.holiday_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add location num to category grid
     *
     * @return $this
     */
    protected function _getLocationNum()
    {
        $this->getSelect()->joinLeft(
            ['mpslh' => $this->getTable('mageplaza_storelocator_location_holiday')],
            'main_table.holiday_id = mpslh.holiday_id',
            []
        )->columns([
            'location_num' => new Zend_Db_Expr('COUNT(`mpslh`.`holiday_id`)')
        ])->group('main_table.holiday_id');

        return $this;
    }
}
