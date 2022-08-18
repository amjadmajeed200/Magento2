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

namespace Mageplaza\StoreLocator\Model\ResourceModel\Holiday;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\StoreLocator\Model\Holiday;

/**
 * Class Collection
 * @package Mageplaza\StoreLocator\Model\ResourceModel\Holiday
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'holiday_id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Holiday::class, \Mageplaza\StoreLocator\Model\ResourceModel\Holiday::class);
    }
}
