<?php
namespace Magenest\GoogleShopping\Model\ResourceModel\Accounts;

/**
 * Class Collection
 * @package Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(\Magenest\GoogleShopping\Model\Accounts::class, \Magenest\GoogleShopping\Model\ResourceModel\Accounts::class);
    }
}
