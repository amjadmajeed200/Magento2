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
namespace Aheadworks\Nmi\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 * @package Aheadworks\Nmi\Observer
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * Payment method token
     */
    const PAYMENT_METHOD_TOKEN = 'payment_method_token';

    /**
     * Token as vault hash
     */
    const IS_VAULT = 'is_vault';

    /**
     * Card type
     */
    const CARD_TYPE = 'card_type';

    /**
     * Card number
     */
    const CARD_NUMBER = 'card_number';

    /**
     * Card expiration
     */
    const CARD_EXPIRATION = 'card_exp';

    /**
     * 3DSecure response xid
     */
    const XID = 'xid';

    /**
     * 3DSecure response cavv
     */
    const CAVV = 'cavv';

    /**
     * 3DSecure response eci
     */
    const ECI = 'eci';

    /**
     * 3DSecure response cardHolderAuth
     */
    const CARD_HOLDER_AUTH = 'cardHolderAuth';

    /**
     * 3DSecure response threeDsVersion
     */
    const THREE_DS_VERSION = 'threeDsVersion';

    /**
     * 3DSecure response directoryServerId
     */
    const DIRECTORY_SERVER_ID = 'directoryServerId';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::PAYMENT_METHOD_TOKEN,
        self::IS_VAULT,
        self::CARD_TYPE,
        self::CARD_NUMBER,
        self::CARD_EXPIRATION,
        self::XID,
        self::CAVV,
        self::ECI,
        self::CARD_HOLDER_AUTH,
        self::THREE_DS_VERSION,
        self::DIRECTORY_SERVER_ID
    ];

    /**
     * Assign data to payment
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);
        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
