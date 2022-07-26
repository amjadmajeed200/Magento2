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
namespace Aheadworks\Nmi\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Aheadworks\Nmi\Gateway\Config\Config as GatewayConfig;
use Aheadworks\Nmi\Gateway\SubjectReader;
use Aheadworks\Nmi\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SecurityDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class SecurityDataBuilder implements BuilderInterface
{
    /**#@+
     * Security block names
     */
    const SECURITY = 'Security';
    const USER_NAME = 'username';
    const PASSWORD = 'password';
    const SECURITY_KEY = 'security_key';
    /**#@-*/

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param GatewayConfig $gatewayConfig
     * @param Config $config
     * @param SubjectReader $subjectReader
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GatewayConfig $gatewayConfig,
        Config $config,
        SubjectReader $subjectReader,
        StoreManagerInterface $storeManager
    ) {
        $this->gatewayConfig = $gatewayConfig;
        $this->config = $config;
        $this->subjectReader = $subjectReader;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();
        $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
        $isSandbox = $this->config->isSandboxMode($websiteId);

        if ($this->config->isSecurityKeyMode($websiteId)) {
            $result = [
                self::SECURITY_KEY => $isSandbox ? $this->config->getSandboxSecurityKey($websiteId) : $this->config->getSecurityKey($websiteId)
            ];
        } else {
            $result = [
                self::USER_NAME => $isSandbox ? $this->config->getSandboxUserName($websiteId) : $this->config->getUserName($websiteId),
                self::PASSWORD => $isSandbox ? $this->config->getSandboxPassword($websiteId) : $this->config->getPassword($websiteId)
            ];
        }

        return $result;
    }
}
