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

namespace Cpl\SocialConnect\Block\Account;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Cpl\SocialConnect\Model\SocialloginFactory;

/**
 * Class SocialAccounts
 * @package Cpl\SocialConnect\Block\Account
 */
class SocialAccounts extends Template
{
    /**
     * @var Session 
     */
    protected $customerSession;
    
    /**
     * @var Http
     */
    protected $response;

    /**
     * @var SocialloginFactory
     */
    protected $socialloginFactory;

    /**
     * Data constructor.
     * 
     * @param Template\Context      $context
     * @param Session               $customerSession
     * @param Http                  $response
     * @param SocialloginFactory    $socialloginFactory
     * @param array                 $data
     */    
    public function __construct(
        Template\Context    $context,
        Session             $customerSession,
        Http                $response,
        SocialloginFactory  $socialloginFactory,
        array               $data = []
    )
    {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->response = $response;
        $this->socialloginFactory = $socialloginFactory;
    }

    /**
     * @return bool
     */
    public function getSocialAccounts()
    {
        $customer = $this->getCustomer();
        if(!$customer->getId()) {
            $this->response->setRedirect('customer/account/');
        }
        $socialAccountsCollection = $this->socialloginFactory->create()->getUsersByCustomerId($customer->getId());
        if($socialAccountsCollection->getSize() < 1) {
            return false;
        }
        return $socialAccountsCollection;
    }

    /**
     * @return mixed
     */
    public function getCustomer() {

        return $this->customerSession->getCustomer();
    }

    /**
     * @param $userId
     * @return string
     */
    public function getUnlinkUrl($userId) {
        return $this->getUrl('sociallogin/account/unlink/', ['id' => $userId]);
    }
}