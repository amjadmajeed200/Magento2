<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\SessionFactory;

/**
 * Class Shipping
 * @package Mageplaza\StoreLocator\Model\Carrier
 */
class Shipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = 'mpstorepickup';

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var CoreSession
     */
    protected $_coreSession;

    /**
     * @var SessionFactory
     */
    protected $customerSession;

    /**
     * Shipping constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param SessionManagerInterface $coreSession
     * @param SessionFactory $customerSession
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        SessionManagerInterface $coreSession,
        SessionFactory $customerSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_coreSession       = $coreSession;
        $this->customerSession    = $customerSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');

        return $this->getFinalPriceWithHandlingFee($configPrice);
    }

    /**
     * @param RateRequest $request
     *
     * @return bool|DataObject|Result|null
     * @SuppressWarnings(Unused)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->isActive()) {
            return false;
        }

        /**  check enabled/disabled the extension  */
        $this->_coreSession->start();
        $checkEnabled = $this->_coreSession->getEnabled();

        if (isset($checkEnabled) && !$checkEnabled) {
            $this->_coreSession->unsEnabled();

            return false;
        }

        if (!$this->getConfigData('allow_guest') && !$this->customerSession->create()->isLoggedIn()) {
            return false;
        }

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();


        /** @var Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingPrice = $this->getShippingPrice();
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        $result->append($method);

        return $result;
    }
}
