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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model\Config\Source;

use Magento\Payment\Model\Config;

/**
 * Class Payment
 * @package Mageplaza\StoreLocator\Model\Config\Source
 */
class Payment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Config
     */
    protected $paymentConfig;

    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Payment constructor.
     *
     * @param Config $paymentConfig
     */
    public function __construct(
        Config $paymentConfig
    ) {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Return options array
     *
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        $payments = $this->paymentConfig->getActiveMethods();
        if (!$this->_options) {
            foreach ($payments as $payment) {
                if ($payment->getCode() !== 'free') {
                    $this->_options[] = [
                        'value' => $payment->getCode(),
                        'label' => $payment->getTitle(),
                    ];
                }
            }
        }

        return $this->_options;
    }
}
