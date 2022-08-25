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

namespace Cpl\SocialConnect\Controller\Account;

/**
 * Class LoginPost
 * @package Cpl\SocialConnect\Controller\Account
 */
class LoginPost extends \Magento\Framework\App\Action\Action
{

    /**
     * @return void
     */    
    public function execute()
    {
        $checkoutParam = $this->getRequest()->getParam('sociallogin-checkout');
        $redirectUrl = $this->getRequest()->getParam(\Magento\Customer\Model\Url::REFERER_QUERY_PARAM_NAME);
        if ($redirectUrl && !$checkoutParam) {
            $redirectUrl = base64_decode($redirectUrl);
            $this->getResponse()->setRedirect($redirectUrl);
        } elseif($checkoutParam){
            $this->getResponse()->setRedirect($checkoutParam);
        } else {
            $this->_redirect('/');
        }
    }
}