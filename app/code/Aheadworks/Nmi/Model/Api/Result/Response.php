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
namespace Aheadworks\Nmi\Model\Api\Result;

use Magento\Framework\DataObject;

/**
 * Class Response
 * @package Aheadworks\Nmi\Model\Api\Result
 */
class Response extends DataObject
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const RESPONSE = 'response';
    const RESPONSE_TEXT = 'responsetext';
    const AUTH_CODE = 'authcode';
    const TRANSACTION_ID = 'transactionid';
    const AVS_RESPONSE = 'avsresponse';
    const CVV_RESPONSE = 'cvvresponse';
    const ORDER_ID = 'orderid';
    const TYPE = 'type';
    const RESPONSE_CODE = 'response_code';
    const CUSTOMER_VAULT_ID = 'customer_vault_id';
    /**#@-*/

    /**
     * Retrieve response
     * 1 = Transaction Approved
     * 2 = Transaction Declined
     * 3 = Error in transaction data or system error
     *
     * @return int
     */
    public function getResponse()
    {
        return (int)$this->getData(self::RESPONSE);
    }

    /**
     * Retrieve response text
     *
     * @return string
     */
    public function getResponseText()
    {
        return $this->getData(self::RESPONSE_TEXT);
    }

    /**
     * Retrieve auth code
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->getData(self::AUTH_CODE);
    }

    /**
     * Retrieve transaction id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * Retrieve avs response
     *
     * @return string
     */
    public function getAvsResponse()
    {
        return $this->getData(self::AVS_RESPONSE);
    }

    /**
     * Retrieve cvv response
     *
     * @return string
     */
    public function getCvvResponse()
    {
        return $this->getData(self::CVV_RESPONSE);
    }

    /**
     * Retrieve order id
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Retrieve type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Retrieve response code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return (int)$this->getData(self::RESPONSE_CODE);
    }

    /**
     * Retrieve customer vault id
     *
     * @return string|null
     */
    public function getCustomerVaultId()
    {
        return $this->getData(self::CUSTOMER_VAULT_ID);
    }
}
