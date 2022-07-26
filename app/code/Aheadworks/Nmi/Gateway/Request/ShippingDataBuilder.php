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
 * Class ShippingDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class ShippingDataBuilder implements BuilderInterface
{
    /**
     * Billing variable name
     */
    const FIRSTNAME = 'shipping_firstname';
    const LASTNAME = 'shipping_lastname';
    const COMPANY = 'shipping_company';
    const ADDRESS1 = 'shipping_address1';
    const ADDRESS2 = 'shipping_address2';
    const CITY = 'shipping_city';
    const STATE = 'shipping_state';
    const ZIP = 'shipping_zip';
    const COUNTRY = 'shipping_country';
    const PHONE = 'shipping_phone';
    const EMAIL = 'shipping_email';

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
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress) {
            return [];
        }

        return [
            self::FIRSTNAME => $shippingAddress->getFirstname(),
            self::LASTNAME => $shippingAddress->getLastname(),
            self::COMPANY => $shippingAddress->getCompany(),
            self::ADDRESS1 => $shippingAddress->getStreetLine1(),
            self::ADDRESS2 => $shippingAddress->getStreetLine2(),
            self::CITY => $shippingAddress->getCity(),
            self::STATE => $shippingAddress->getRegionCode(),
            self::ZIP => $shippingAddress->getPostcode(),
            self::COUNTRY => $shippingAddress->getCountryId(),
            self::PHONE => $shippingAddress->getTelephone(),
            self::EMAIL => $shippingAddress->getEmail()
        ];
    }
}
