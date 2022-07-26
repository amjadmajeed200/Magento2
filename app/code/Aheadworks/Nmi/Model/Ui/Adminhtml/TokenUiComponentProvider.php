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

use Aheadworks\Nmi\Gateway\Request\CreditCardDataBuilder;
use Aheadworks\Nmi\Model\Config;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

/**
 * Class TokenUiComponentProvider
 *
 * @package Aheadworks\Nmi\Model\Ui\Adminhtml
 */
class TokenUiComponentProvider implements TokenUiComponentProviderInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param Json|null $serializer
     * @param Config $config
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        Json $serializer,
        Config $config
    ) {
        $this->componentFactory = $componentFactory;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = $this->serializer->unserialize($paymentToken->getTokenDetails() ?: '{}');
        $threeDSecureAvailabilityMapPerStore = $this->config->getThreeDSecureAvailabilityMapPerStore();
        $checkoutPublicKeyAvailabilityMapPerStore = $this->config->getCheckoutPublicKeyAvailabilityMapPerStore();

        $component = $this->componentFactory->create(
            [
                'config' => [
                    'code' => ConfigProvider::CC_VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $jsonDetails,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
                    CreditCardDataBuilder::CUSTOMER_VAULT_ID => $paymentToken->getGatewayToken(),
                    'threeDSecureAvailabilityMapPerStore' => $this->serializer->serialize($threeDSecureAvailabilityMapPerStore),
                    'checkoutPublicKeyAvailabilityMapPerStore' => $this->serializer->serialize($checkoutPublicKeyAvailabilityMapPerStore),
                    'template' => 'Aheadworks_Nmi::form/vault.phtml'
                ],
                'name' => Template::class
            ]
        );

        return $component;
    }
}
