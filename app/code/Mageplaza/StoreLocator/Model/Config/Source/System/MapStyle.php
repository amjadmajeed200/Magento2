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
 * Class MapStyle
 * @package Mageplaza\StoreLocator\Model\Config\Source\System
 */
class MapStyle implements ArrayInterface
{
    const STYLE_DEFAULT      = 'default';
    const STYLE_BLUE_ESSENCE = 'blue-essence';
    const STYLE_DARK_GREY    = 'dark-grey';
    const STYLE_LIGHT_GREY   = 'light-grey';
    const STYLE_MID_NIGHT    = 'mid-night';
    const STYLE_CUSTOM       = 'custom';

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
            self::STYLE_DEFAULT      => __('Default'),
            self::STYLE_BLUE_ESSENCE => __('Blue Essence'),
            self::STYLE_DARK_GREY    => __('Dark Grey'),
            self::STYLE_LIGHT_GREY   => __('Light Grey'),
            self::STYLE_MID_NIGHT    => __('Mid Night'),
            self::STYLE_CUSTOM       => __('Custom')
        ];
    }
}
