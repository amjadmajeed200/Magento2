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
namespace Aheadworks\Nmi\Model\Ui\Adminhtml;

use Aheadworks\Nmi\Gateway\Config\Config as GatewayConfig;
use Aheadworks\Nmi\Model\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProvider
 * @package Aheadworks\Nmi\Model\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Payment code
     */
    const CODE = 'aw_nmi';

    /**
     * Credit card vault code
     */
    const CC_VAULT_CODE = 'aw_nmi_cc_vault';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param GatewayConfig $gatewayConfig
     * @param SessionManagerInterface $session
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        GatewayConfig $gatewayConfig,
        SessionManagerInterface $session,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->gatewayConfig = $gatewayConfig;
        $this->session = $session;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->session->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        return [
            'payment' => [
                self::CODE => [
                    'isSandboxMode' => $this->config->isSandboxMode($websiteId),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                    'useCvv' => $this->gatewayConfig->isCvvEnabled($storeId),
                    'threeDSecureAvailabilityMapPerStore' => $this->config->getThreeDSecureAvailabilityMapPerStore(),
                    'checkoutPublicKeyAvailabilityMapPerStore' => $this->config->getCheckoutPublicKeyAvailabilityMapPerStore()
                ]
            ]
        ];
    }
}
