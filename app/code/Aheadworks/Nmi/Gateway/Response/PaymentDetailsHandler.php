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
namespace Aheadworks\Nmi\Gateway\Response;

use Aheadworks\Nmi\Gateway\SubjectReader;
use Aheadworks\Nmi\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class PaymentDetailsHandler
 * @package Aheadworks\Nmi\Gateway\Response
 */
class PaymentDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles fraud messages
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $transactionResponse = $this->subjectReader->readTransactionResponse($response);

        $transactionId = $transactionResponse->getTransactionId();
        $payment->setCcTransId($transactionId);
        $payment->setLastTransId($transactionId);

        $payment->unsAdditionalInformation(DataAssignObserver::PAYMENT_METHOD_TOKEN);
    }
}
