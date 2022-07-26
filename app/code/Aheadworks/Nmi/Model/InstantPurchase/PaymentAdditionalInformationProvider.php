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
namespace Aheadworks\Nmi\Model\InstantPurchase;

use Magento\InstantPurchase\PaymentMethodIntegration\PaymentAdditionalInformationProviderInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Aheadworks\Nmi\Observer\DataAssignObserver;

/**
 * Class PaymentAdditionalInformationProvider
 * @package Aheadworks\Nmi\Model\InstantPurchase
 */
class PaymentAdditionalInformationProvider implements PaymentAdditionalInformationProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation(PaymentTokenInterface $paymentToken): array
    {
        return [
            DataAssignObserver::PAYMENT_METHOD_TOKEN => $paymentToken->getPublicHash(),
            DataAssignObserver::IS_VAULT => 1,
        ];
    }
}
