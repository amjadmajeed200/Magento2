<?php
namespace Magenest\GoogleShopping\Model;

/**
 * Class GoogleProduct
 * @package Magenest\GoogleShopping\Model
 */
class GoogleProduct extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct::class);
    }
}
