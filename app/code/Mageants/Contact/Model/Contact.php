<?php
namespace Mageants\Contact\Model;

use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Mageants\Contact\Model\ResourceModel\Contact');
    }
}
