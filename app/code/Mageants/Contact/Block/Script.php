<?php 
namespace Mageants\Contact\Block;
use Magento\Framework\View\Element\Template;
class Script extends Template
{
    public function getApiKey()
    {
        return $this->_scopeConfig->getValue('mageants_contact/contact/api');
    }
}
