<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    Nmi
 * @version    1.3.1
 * @copyright  Copyright (c) 2022 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Nmi\Model;

use Aheadworks\Nmi\Model\Adminhtml\Source\AuthorizationMode;
use Aheadworks\Nmi\Model\Adminhtml\Source\Environment;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Aheadworks\Nmi\Model
 */
class Config
{
    /**#@+
    * Constants for config path
    */
    const XML_PATH_AW_NMI_CC_VAULT_ACTIVE = 'payment/aw_nmi_cc_vault/active';
    const XML_PATH_AW_NMI_ACTIVE = 'payment/aw_nmi/active';
    const XML_PATH_AW_NMI_API_ENDPOINT_URL = 'payment/aw_nmi/api_endpoint_url';
    const XML_PATH_AW_NMI_ENVIRONMENT = 'payment/aw_nmi/environment';
    const XML_PATH_AW_NMI_TOKENIZATION_KEY = 'payment/aw_nmi/tokenization_key';
    const XML_PATH_AW_NMI_API_USERNAME = 'payment/aw_nmi/api_username';
    const XML_PATH_AW_NMI_API_PASSWORD = 'payment/aw_nmi/api_password';
    const XML_PATH_AW_NMI_SANDBOX_API_USERNAME = 'payment/aw_nmi/sandbox_api_username';
    const XML_PATH_AW_NMI_SANDBOX_API_PASSWORD = 'payment/aw_nmi/sandbox_api_password';
    const XML_PATH_AW_NMI_SECURITY_KEY = 'payment/aw_nmi/security_key';
    const XML_PATH_AW_NMI_SANDBOX_SECURITY_KEY = 'payment/aw_nmi/sandbox_security_key';
    const XML_PATH_AW_NMI_AUTHORIZATION_MODE = 'payment/aw_nmi/authorization_mode';
    const XML_PATH_AW_NMI_IS_3D_SECURE_ENABLED = 'payment/aw_nmi/is_3d_secure_enabled';
    const XML_PATH_AW_NMI_CHECKOUT_PUBLIC_KEY = 'payment/aw_nmi/checkout_public_key';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if aw nmi cc vault active
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isAwNmiCcVaultActive($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_CC_VAULT_ACTIVE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if payment is active
     *
     * @param int|null $websiteId
     * @return bool
     */
   public function isActive($websiteId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_ACTIVE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve api endpoint url
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getApiEndpointUrl($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_API_ENDPOINT_URL,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve environment mode - production or sandbox
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getEnvironment($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_ENVIRONMENT,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve tokenization key
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getTokenizationKey($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_TOKENIZATION_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve user name
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getUserName($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_API_USERNAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve password
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getPassword($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_API_PASSWORD,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve sandbox user name
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSandboxUserName($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_SANDBOX_API_USERNAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve sandbox password
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSandboxPassword($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_SANDBOX_API_PASSWORD,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve Live security key
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSecurityKey($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_SECURITY_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve Sandbox security key
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSandboxSecurityKey($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_SANDBOX_SECURITY_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve Authorization mode - key or login & password
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getAuthorizationMode($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_AUTHORIZATION_MODE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Checks if 3DSecure field is enabled.
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function is3DSecureEnabled($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AW_NMI_IS_3D_SECURE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Checks if 3DSecure field is enabled.
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getCheckoutPublicKey($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AW_NMI_CHECKOUT_PUBLIC_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve 3DSecure Availability Map Per Store
     *
     * @return array
     */
    public function getThreeDSecureAvailabilityMapPerStore()
    {
        $config = [];
        foreach ($this->storeManager->getStores() as $storeId => $store) {
            $config[$storeId] = $this->is3DSecureEnabled($store->getWebsiteId());
        }

        return $config;
    }

    /**
     * Retrieve Checkout Public Key Availability Map Per Store
     *
     * @return array
     */
    public function getCheckoutPublicKeyAvailabilityMapPerStore()
    {
        $config = [];
        foreach ($this->storeManager->getStores() as $storeId => $store) {
            $config[$storeId] = $this->getCheckoutPublicKey($store->getWebsiteId());
        }

        return $config;
    }

    /**
     * Check if sandbox mode
     *
     * @param int|null $websiteId
     * @return string
     */
    public function isSandboxMode($websiteId = null)
    {
        return $this->getEnvironment($websiteId) === Environment::ENVIRONMENT_SANDBOX;
    }

    /**
     * Check if is security key mode
     *
     * @param int|null $websiteId
     * @return string
     */
    public function isSecurityKeyMode($websiteId = null)
    {
        return $this->getAuthorizationMode($websiteId) === AuthorizationMode::AUTHORIZATION_MODE_SECURITY_KEY;
    }
}

