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
namespace Aheadworks\Nmi\Helper;

use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Class NmiPayment
 * @package Aheadworks\Nmi\Helper
 */
class NmiPayment
{
    const NMI_PAYMENT_CODE = 'aw_nmi';

    /**
     * Returns vault payment token
     *
     * @param OrderPaymentInterface $payment
     * @return string|null
     */
    public function getVaultPaymentToken(OrderPaymentInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();

        return !empty($extensionAttributes)
            ? $extensionAttributes->getVaultPaymentToken()
            : null;
    }

    /**
     * Checks if payment is nmi
     *
     * @param OrderPaymentInterface $payment
     * @return bool
     */
    public function isNmiPayment(OrderPaymentInterface $payment)
    {
        $paymentMethod = $payment->getMethod();
        return $paymentMethod == self::NMI_PAYMENT_CODE;
    }
}

