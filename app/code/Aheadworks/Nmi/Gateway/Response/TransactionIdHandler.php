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
use Aheadworks\Nmi\Model\Api\Result\Response;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class TransactionIdHandler
 * @package Aheadworks\Nmi\Gateway\Response
 */
class TransactionIdHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
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

        $this->setTransactionId($payment, $transactionResponse);
        $payment->setIsTransactionClosed($this->shouldCloseTransaction());
        $payment->setShouldCloseParentTransaction($this->shouldCloseParentTransaction($payment));
    }

    /**
     * Set transaction id
     *
     * @param Payment $payment
     * @param Response $transactionResponse
     * @return void
     */
    protected function setTransactionId($payment, $transactionResponse)
    {
        $transactionId = $transactionResponse->getTransactionId();
        $payment->setTransactionId($transactionId);
    }

    /**
     * Retrieve transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return false;
    }

    /**
     * Retrieve parent transaction should be closed
     *
     * @param Payment $payment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction($payment)
    {
        return false;
    }
}
