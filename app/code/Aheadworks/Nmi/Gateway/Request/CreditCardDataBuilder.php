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

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Aheadworks\Nmi\Gateway\SubjectReader;
use Aheadworks\Nmi\Observer\DataAssignObserver;
use Magento\Vault\Api\PaymentTokenManagementInterface;

/**
 * Class CreditCardDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class CreditCardDataBuilder implements BuilderInterface
{
    /**#@+
     * Credit card block names
     */
    const PAYMENT_TOKEN = 'payment_token';
    const CUSTOMER_VAULT_ID = 'customer_vault_id';
    /**#@-*/

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var PaymentTokenManagementInterface
     */
    private $tokenManagement;

    /**
     * @param PaymentTokenManagementInterface $tokenManagement
     * @param BooleanUtils $booleanUtils
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader,
        BooleanUtils $booleanUtils,
        PaymentTokenManagementInterface $tokenManagement
    ) {
        $this->subjectReader = $subjectReader;
        $this->booleanUtils = $booleanUtils;
        $this->tokenManagement = $tokenManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();

        $token = $payment->getAdditionalInformation(DataAssignObserver::PAYMENT_METHOD_TOKEN);
        $isVaultProcessed = $payment->getAdditionalInformation(DataAssignObserver::IS_VAULT);
        if ($isVaultProcessed && $this->booleanUtils->toBoolean($isVaultProcessed)) {
            $publicHash = $token;
            $paymentToken = $this->tokenManagement->getByPublicHash($publicHash, $order->getCustomerId());
            if (!$paymentToken) {
                throw new \Exception('No available payment tokens');
            }
            $creditCardData[self::CUSTOMER_VAULT_ID] = $paymentToken->getGatewayToken();
        } else {
            // one time token
            $creditCardData[self::PAYMENT_TOKEN] = $token;
        }

        return $creditCardData;
    }
}
