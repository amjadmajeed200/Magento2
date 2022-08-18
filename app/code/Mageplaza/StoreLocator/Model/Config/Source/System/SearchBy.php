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

namespace Mageplaza\StoreLocator\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowOn
 * @package Mageplaza\StoreLocator\Model\Config\Source\System
 */
class SearchBy implements ArrayInterface
{
    const STORE_NAME     = 1;
    const COUNTRY        = 2;
    const STATE_PROVINCE = 3;
    const CITY           = 4;
    const STREET         = 5;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::STORE_NAME     => __('Store Name'),
            self::COUNTRY        => __('Country'),
            self::STATE_PROVINCE => __('State/province'),
            self::CITY           => __('City'),
            self::STREET         => __('Street')
        ];
    }
}
