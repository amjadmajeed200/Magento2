<?php
namespace Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex;

/**
 * Class Collection
 * @package Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init(\Magenest\GoogleShopping\Model\GoogleFeedIndex::class, \Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex::class);
    }
}
