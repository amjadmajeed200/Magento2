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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Aheadworks\Nmi\Gateway\SubjectReader;
use Magento\Sales\Model\Order\Payment;

/**
 * Class PaymentDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class PaymentDataBuilder implements BuilderInterface
{
    /**
     * Amount block name
     */
    const AMOUNT = 'amount';

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
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        try {
            $amount = $this->subjectReader->readAmount($buildSubject);
        } catch (\InvalidArgumentException $e) {
            $paymentDO = $this->subjectReader->readPayment($buildSubject);
            /** @var Payment $payment */
            $payment = $paymentDO->getPayment();
            $amount = $payment->getOrder()->getGrandTotal();
        }

        $result = [
            self::AMOUNT => (float)number_format($amount, 2, '.', ''),
        ];

        return $result;
    }
}
