<?php
namespace Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct;

/**
 * Class Collection
 * @package Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init("Magenest\GoogleShopping\Model\GoogleProduct", "Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct");
    }
}
