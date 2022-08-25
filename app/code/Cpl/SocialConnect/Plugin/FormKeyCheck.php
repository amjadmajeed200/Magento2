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

namespace Cpl\SocialConnect\Plugin;

use Magento\Framework\Data\Form\FormKey;

/**
 * Class FormKeyCheck
 * @package Cpl\SocialConnect\Plugin
 */
class FormKeyCheck
{

    /**
     * @var \Magento\Customer\Model\Session
     */   
    protected $customerSession;

    /**
     * ResultPage constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @param FormKey $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetFormKey(FormKey $subject, callable $proceed)
    {
        $result = $proceed();
        $slSession = $this->customerSession->getSociallogin();
        if(is_array($slSession) && !empty($slSession)) {
            if(isset($slSession['sl_form_key'])) {
                $result = $slSession['sl_form_key'];
                unset($slSession['sl_form_key']);
                $this->customerSession->setData('sociallogin', $slSession);
            }
        }
        return $result;
    }
}