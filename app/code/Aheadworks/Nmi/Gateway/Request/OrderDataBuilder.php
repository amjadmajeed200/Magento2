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

/**
 * Class OrderDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class OrderDataBuilder implements BuilderInterface
{
    /**
     * Order variable name
     */
    const IP_ADDRESS = 'ipaddress';
    const ORDER_ID = 'orderid';
    const PO_NUMBER = 'ponumber';

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
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();

        return [
            self::IP_ADDRESS => $order->getRemoteIp(),
            self::ORDER_ID => $order->getOrderIncrementId(),
            self::PO_NUMBER => $order->getId(),
        ];
    }
}
