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
namespace Aheadworks\Nmi\Model\Ui;

use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\Nmi\Gateway\Request\CreditCardDataBuilder;

/**
 * Class TokenUiComponentProvider
 * @package Aheadworks\Nmi\Model\Ui
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
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param Json|null $serializer
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        Json $serializer
    ) {
        $this->componentFactory = $componentFactory;
        $this->serializer = $serializer;
    }

    /**
     * Retrieve UI component for token
     *
     * @param PaymentTokenInterface $paymentToken
     * @return TokenUiComponentInterface
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $jsonDetails = $this->serializer->unserialize($paymentToken->getTokenDetails() ?: '{}');
        $component = $this->componentFactory->create(
            [
                'config' => [
                    'code' => ConfigProvider::CC_VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $jsonDetails,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash(),
                    CreditCardDataBuilder::CUSTOMER_VAULT_ID => $paymentToken->getGatewayToken()
                ],
                'name' => 'Aheadworks_Nmi/js/view/payment/method-renderer/vault'
            ]
        );

        return $component;
    }
}
