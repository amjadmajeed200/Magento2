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
namespace Aheadworks\Nmi\Model\InstantPurchase\CreditCard;

use Magento\InstantPurchase\PaymentMethodIntegration\PaymentTokenFormatterInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class TokenFormatter
 * @package Aheadworks\Nmi\Model\InstantPurchase\CreditCard
 */
class TokenFormatter implements PaymentTokenFormatterInterface
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Json|null $serializer
     */
    public function __construct(
        Json $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function formatPaymentToken(PaymentTokenInterface $paymentToken): string
    {
        $details = $this->serializer->unserialize($paymentToken->getTokenDetails() ?: '{}');
        if (!isset($details['type'], $details['lastCcNumber'], $details['expirationDate'])) {
            throw new \InvalidArgumentException('Invalid credit card token details.');
        }

        $formatted = sprintf(
            '%s: %s, %s: %s (%s: %s)',
            __('Credit Card Type'),
            $details['type'],
            __('ending'),
            $details['lastCcNumber'],
            __('expires'),
            $details['expirationDate']
        );

        return $formatted;
    }
}
