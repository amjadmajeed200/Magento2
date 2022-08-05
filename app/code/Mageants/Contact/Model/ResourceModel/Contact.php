<?php
namespace Mageants\Contact\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Contact extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('mageants_contact_message','message_id');
    }
}
