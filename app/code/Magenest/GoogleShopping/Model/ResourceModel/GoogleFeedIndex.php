<?php
namespace Magenest\GoogleShopping\Model\ResourceModel;

/**
 * Class GoogleFeedIndex
 * @package Magenest\GoogleShopping\Model\ResourceModel
 */
class GoogleFeedIndex extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_connection;

    public function _construct()
    {
        $this->_init('magenest_google_feed_index', 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function insertData($data)
    {
        try {
            if (is_array($data)) {
                $this->_connection->insertOnDuplicate(
                    $this->getMainTable(),
                    $data,
                    ['feed_id','template_id', 'product_ids']
                );
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
