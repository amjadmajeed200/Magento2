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

namespace Cpl\SocialConnect\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Helper\Data as MageHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Url;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreRepository;
use Magento\Framework\UrlFactory;

/**
 * Class Data
 * @package Cpl\SocialConnect\Helper
 */
class Data extends AbstractHelper
{
    const REFERER_STORE_PARAM_NAME = 'cpl_social_connect_referer_store';
    const REFERER_QUERY_PARAM_NAME = 'cpl_social_connect_referer';

    /**
     * @var string
     */
    protected $configSectionId = 'cpl_social_connect';

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var MageHelper
     */
    protected $magHelper;
    
    /**
     * @var Session
     */
    protected $customerSession;
    
    /**
     * @var Customer
     */
    protected $customer;
    
    /**
     * @var Url
     */
    protected $customerUrl;
    
    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;
    
    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param MageHelper $magHelper
     * @param Session $customerSession
     * @param Customer $customer
     * @param Url $customerUrl
     * @param CookieManagerInterface $cookieManager
     * @param StoreRepository $storeRepository
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        MageHelper $magHelper,
        Session $customerSession,
        Customer $customer,
        Url $customerUrl,
        CookieManagerInterface $cookieManager,
        StoreRepository $storeRepository,
        UrlFactory $urlFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->magHelper = $magHelper;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->customerUrl = $customerUrl;
        $this->cookieManager = $cookieManager;
        $this->storeRepository = $storeRepository;
        $this->urlFactory = $urlFactory;
    }

    /**
     * @param $provider
     * @param bool $byRequest
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCallbackUri($provider, $byRequest = false)
    {
        $request = $this->_getRequest();
        $websiteCode = $request->getParam('website');
        $displayedWebsite = $this->storeManager->getWebsite($byRequest ? $websiteCode : null);
        if (!$displayedWebsite->getId()) {
            $websites = $this->storeManager->getWebsites(true);
            foreach ($websites as $website) {
                $defaultStoreId = $website->getDefaultGroup()->getDefaultStoreId();
                if ($defaultStoreId) {
                    $displayedWebsite = $website;
                    break;
                }
            }
        }
        $storeIds = [];
        $groups = $displayedWebsite->getGroups();
        foreach ($groups as $group) {
            $stores = $group->getStores();
            foreach ($stores as $storeView) {
                $storeIds[] = $storeView->getCode();
            }
        }
        $stores = $this->storeRepository->getList();
        $showStoreCode = $this->scopeConfig->getValue('web/url/use_store');
        if ($showStoreCode) {
            $storeIds = [];
            foreach ($stores as $store) {
                if ($store->getCode() != 'admin') {
                    $storeIds[] = $store->getId();
                }
            }
        }
        $urlArr = [];
        foreach ($storeIds as $storeId) {
            $url = $this->storeManager->getStore($storeId)->getBaseUrl() . 'sociallogin/account/login/type/' . $provider . '/';
            if (false !== ($length = stripos($url, '?'))) {
                $url = substr($url, 0, $length);
            }
            $url = preg_replace('~(\?|/)key/[^&]*~', '$1', $url);
            //$url = str_replace('http://', 'https://', $url);
            if ($byRequest) {
                if ($this->getConfig('web/seo/use_rewrites')) {
                    $url = str_replace('index.php/', '', $url);
                }
            }
            $urlArr[] = $url;
        }
        $urlArr = array_unique($urlArr);
        return $urlArr;
    }

    /**
     * @param bool $value
     * @return mixed
     */
    public function refererStore($referer = false)
    {
        $sessionData = $this->customerSession->getData(self::REFERER_STORE_PARAM_NAME);
        if ($referer) {
            $this->customerSession->setData(self::REFERER_STORE_PARAM_NAME, $referer);
        } elseif ($referer === null) {
            $this->customerSession->unsetData(self::REFERER_STORE_PARAM_NAME);
        }

        return $sessionData;
    }

    /**
     * @param $path
     * @param null $store
     * @param null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }
    
    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getGeneralConfig('enabled', $storeId?:$this->getStoreId());
    }

    /**
     * Get general configurations
     * 
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->scopeConfig->getValue('cpl_social_connect/general/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId?:$this->getStoreId());
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }    

    /**
     * @return array
     */
    public function getSkipModulesReferer()
    {
        return ['customer/account', 'sociallogin/account'];
    }

    /**
     * @return mixed
     */
    public function isSecure()
    {
        $isSecure = $this->scopeConfig->getValue('web/secure/use_in_frontend');

        return $isSecure;
    }

    /**
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }    

    /**
     * @return bool
     */
    public function isGuestCheckoutEnabled()
    {
        return $this->scopeConfig->getValue('checkout/options/guest_checkout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Get social configuratioins
     * 
     * @param $type
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getSocialConfig($type, $field, $storeId = null)
    {
        return $this->scopeConfig->getValue('cpl_social_connect/' . $type . '/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId?:$this->getStoreId());
    }
    
    /**
     * @param $provider
     * @param bool $byRequest
     * @return bool|mixed|string
     */
    public function getCallback($provider, $byRequest = false)
    {
        $url = $this->storeManager
            ->getStore()
            ->getUrl('sociallogin/account/login', ['type' => $provider, 'key' => null, '_nosid' => true]);

        $url = str_replace(
            '/' . $this->magHelper->getAreaFrontName() . '/',
            '/',
            $url
        );

        if (false !== ($length = stripos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        if ($byRequest) {
            if ($this->getConfig('web/seo/use_rewrites')) {
                $url = str_replace('index.php/', '', $url);
            }
        }

        return $url;
    }

    /**
     * @return null|string
     */
    public function getCookieRefererLink()
    {
        return $this->cookieManager->getCookie(self::REFERER_QUERY_PARAM_NAME);
    }

    /**
     * Receive config section id
     *
     * @return string
     */
    public function getConfigSectionId()
    {
        return $this->configSectionId;
    }
    
    /**
     * @return bool
     */
    public function isGlobalScope()
    {
        return $this->customer->getSharingConfig()->isGlobalScope();
    }

    /**
     * @param bool $email
     * @return bool
     */
    public function isTempMail($email = false)
    {
        if (!$email) {
            if ($this->customerSession->isLoggedIn()) {
                $email = $this->customerSession->getCustomer()->getEmail();
            }
        }
        return (bool)(strpos($email, self::TEMP_EMAIL_PREFIX) === 0);
    }    
    
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        $redirectUrl = '';
        $links = [];
        if ($referer = $this->_getRequest()->getParam(\Magento\Customer\Model\Url::REFERER_QUERY_PARAM_NAME)) {
            $links[] = $this->urlDecoder->decode($referer);
        }
        if ($referer = $this->getReferer()) {
            $links[] = $referer;
        }
        foreach ($links as $url) {
            $redirectUrl = $this->_createUrl()->getRebuiltUrl($url);
        }

        if (!$redirectUrl) {
            $redirectUrl = $this->customerUrl->getDashboardUrl();
        }

        return $redirectUrl;
    }
    
    /**
     * @param bool $referer
     * @return mixed
     */
    public function getReferer($referer = false)
    {
        $customerReferer = $this->customerSession->getData(self::REFERER_QUERY_PARAM_NAME);
        if ($referer) {
            $this->customerSession->setData(self::REFERER_QUERY_PARAM_NAME, $referer);
        } elseif ($referer === null) {
            $this->customerSession->unsetData(self::REFERER_QUERY_PARAM_NAME);
        }

        return $customerReferer;
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }        
}
