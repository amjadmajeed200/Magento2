<?php
namespace Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\GoogleShopping\Model\GoogleFeed', 'Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed');
    }
}
