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

use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * Class VoidHandler
 * @package Aheadworks\Nmi\Gateway\Response
 */
class VoidHandler extends TransactionIdHandler
{
    /**
     * {@inheritdoc}
     */
    protected function setTransactionId($payment, $transactionResponse)
    {
        $type = Transaction::TYPE_VOID;
        $transactionId = $transactionResponse->getTransactionId();
        $payment->setTransactionId("{$transactionId}-{$type}");
    }

    /**
     * {@inheritdoc}
     */
    protected function shouldCloseTransaction()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function shouldCloseParentTransaction($payment)
    {
        return true;
    }
}
