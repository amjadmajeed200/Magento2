<?php
namespace Magenest\GoogleShopping\Model\ResourceModel;

class Accounts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_google_feed_merchants', 'id');
    }
}
