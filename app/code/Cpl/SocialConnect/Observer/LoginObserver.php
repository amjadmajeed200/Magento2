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

namespace Cpl\SocialConnect\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Cpl\SocialConnect\Helper\Data as SlHelper;

class LoginObserver implements ObserverInterface
{
    /**
     * @var SlHelper
     */
    protected $_slHelper;
    /**
     * @var Session
     */
    protected $_session;

    /**
     * LoginObserver constructor.
     * @param SlHelper $slHelper
     * @param Session $customerSession
     */
    public function __construct(
        SlHelper $slHelper,
        Session $customerSession
    ) {
        $this->_slHelper = $slHelper;
        $this->_session = $customerSession;
    }

    /**
     * Set before AuthUrl  
     *
     * @param \Magento\Framework\Event\Observer $observer
     */    
    public function execute(Observer $observer)
    {
        if(!$this->_slHelper->isEnabled()) {
            return;
        }
        $redirectUrl = $this->_slHelper->getRedirectUrl();
        $this->_session->setBeforeAuthUrl($redirectUrl);
    }
}
