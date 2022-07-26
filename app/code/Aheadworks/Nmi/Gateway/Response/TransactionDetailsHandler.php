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
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Aheadworks\Nmi\Model\Api\Result\Response;
use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * Class TransactionDetailsHandler
 * @package Aheadworks\Nmi\Gateway\Response
 */
class TransactionDetailsHandler implements HandlerInterface
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
        $transactionDetails = [
            Response::AUTH_CODE => $transactionResponse->getAuthCode(),
            Response::CVV_RESPONSE => $transactionResponse->getCvvResponse(),
            Response::AVS_RESPONSE => $transactionResponse->getAvsResponse()
        ];

        $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $transactionDetails);
    }
}
