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

namespace Cpl\SocialConnect\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Encryption\EncryptorInterface;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class SocialLogin
 * @package Cpl\SocialConnect\Block
 */
class SocialLogin extends Template
{

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var EncryptorInterface
     */
    protected $encryptorInterface;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $socialMedia = [
        'fb'        => 'https://www.facebook.com/dialog/oauth/?',
        'google'    => 'https://accounts.google.com/o/oauth2/v2/auth?scope=email+profile+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile+openid&access_type=offline&include_granted_scopes=true&state=state_parameter_passthrough_value&',
        'amazon'    => 'https://www.amazon.com/ap/oa/?'
    ];

    /**
     * SocialLogin constructor.
     * 
     * @param Context               $context
     * @param Http                  $request
     * @param FormKey               $formKey
     * @param EncryptorInterface    $encryptorInterface
     * @param Data                  $helper
     * @param array                 $data
     */
    public function __construct(
        Context             $context,
        Http                $request,
        FormKey             $formKey,
        EncryptorInterface  $encryptorInterface,
        Data                $helper,
        array               $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_request = $request;
        $this->formKey = $formKey;
        $this->encryptorInterface = $encryptorInterface;
        $this->helper = $helper;
        if ($this->helper->isEnabled()) {
            $this->pageConfig->addBodyClass('cpl-sc');
        }
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
        if (!$this->helper->isEnabled()) {
            return;
        }
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getSkipModules()
    {
        $skip = $this->helper->getSkipModulesReferer();
        return json_encode($skip);
    }

    /**
     * @return bool|string
     */
    protected function isCheckoutPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($route == 'checkout' && $controller == 'index') {
            return $this->getUrl('checkout/index/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    protected function isCartPage() {
        $route      = $this->_request->getRouteName();
        $controller = $this->_request->getControllerName();
        if($controller == 'cart' && $route == 'checkout') {
            return $this->getUrl('checkout/cart/index', ['secure' => true]);
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    public function getCurrentPageRedirectUrl() {
        $url = false;
        if($cartUrl = $this->isCartPage()) {
            return $cartUrl;
        } elseif($checkoutUrl = $this->isCheckoutPage()) {
            return $checkoutUrl;
        } else {
            return $url;
        }
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn() {
        return $this->helper->isCustomerLoggedIn();
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
