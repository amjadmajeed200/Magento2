<?php

namespace Magenest\GoogleShopping\Model;

class Accounts extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(\Magenest\GoogleShopping\Model\ResourceModel\Accounts::class);
    }
}
