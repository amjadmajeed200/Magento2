<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */	
namespace Mageants\Contact\Model\ResourceModel\Contact;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{    
    /**
     * @var string
     */	
	protected $_idFieldName = 'message_id';
	
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Mageants\Contact\Model\Contact',
            'Mageants\Contact\Model\ResourceModel\Contact'
        );
    }
}
