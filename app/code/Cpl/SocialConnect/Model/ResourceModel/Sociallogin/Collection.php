<?php
/**
 * Cpl
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Cpl
 * @package    Cpl_SocialConnect
 * @copyright  Copyright (c) 2022 Cpl (https://www.magento.com/)
 */

namespace Cpl\SocialConnect\Model\ResourceModel\Sociallogin;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Cpl\SocialConnect\Model\ResourceModel\Sociallogin
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritdoc
     */    
    protected function _construct()
    {
        $this->_init('Cpl\SocialConnect\Model\Sociallogin', 'Cpl\SocialConnect\Model\ResourceModel\Sociallogin');
    }

    /**
     * @inheritdoc
     */        
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['cex' => $this->getTable('customer_entity')],
            'cex.entity_id = main_table.customer_id',
            ['firstname','lastname','email']
        );
        $this->addFilterToMap('created_at', 'main_table.created_at');
    }
}
