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

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutInterface;
use Cpl\SocialConnect\Model\SocialloginFactory;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class Unlink
 * @package Cpl\SocialConnect\Controller\Account
 */
class Unlink extends \Cpl\SocialConnect\Controller\AbstractAccount
{

    /**
     * @var SocialloginFactory
     */
    protected $socialloginFactory;

    /**
     * Unlink constructor.
     * 
     * @param Context               $context
     * @param Session               $customerSession
     * @param StoreManager          $storeManager
     * @param RawFactory            $resultRawFactory
     * @param LayoutInterface       $layout
     * @param SocialloginFactory    $socialloginFactory
     * @param Data                  $slHelper
     */
    public function __construct(
        Context             $context,
        Session             $customerSession,
        StoreManager        $storeManager,
        RawFactory          $resultRawFactory,
        LayoutInterface     $layout,
        SocialloginFactory  $socialloginFactory,
        Data                $slHelper
    )
    {
        parent::__construct($context, $customerSession, $storeManager, $layout, $resultRawFactory, $slHelper);
        $this->socialloginFactory = $socialloginFactory;
    }

    /**
     * @return void
     */    
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $unlinkResponse = $this->socialloginFactory->create()->unlinkUser($id);
            if($unlinkResponse) {
                $this->messageManager->addSuccess(__('Account unlinked successfully.'));
            } else {
                $this->messageManager->addError(__('No link id provided.'));
            }
        } else {
            $this->messageManager->addError(__('An error occurred, please try again.'));
        }
        $redirectUrl = $this->_getUrl('sociallogin/account/socialaccounts');
        $this->getResponse()->setRedirect($redirectUrl);
    }
}