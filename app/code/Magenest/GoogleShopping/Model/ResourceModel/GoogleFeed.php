<?php
namespace Magenest\GoogleShopping\Model\ResourceModel;

class GoogleFeed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init('magenest_google_feed', 'id');
    }
}
