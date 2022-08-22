<?php
namespace Magenest\GoogleShopping\Model;

/**
 * Class GoogleFeedIndex
 * @package Magenest\GoogleShopping\Model
 */
class GoogleFeedIndex extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'magenest_google_feed_index';

    public function _construct()
    {
        $this->_init('Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex');
    }
}
