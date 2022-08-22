<?php
namespace Magenest\GoogleShopping\Model\ResourceModel\Template;

/**
 * Class Collection
 * @package Magenest\GoogleShopping\Model\ResourceModel\Template
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init('Magenest\GoogleShopping\Model\Template', 'Magenest\GoogleShopping\Model\ResourceModel\Template');
    }

    /**
     * Get collection data as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'name');
    }
}
