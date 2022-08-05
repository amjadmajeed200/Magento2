<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */

namespace Mageants\Contact\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
	/**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $_storeManager;
	
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */     
    protected $scopeConfig;
	
	/**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
		$this->scopeConfig = $context->getScopeConfig();
    }
	
    /**
     * Get Store Config Value
     * @return string
     */	
	public function getContactConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
