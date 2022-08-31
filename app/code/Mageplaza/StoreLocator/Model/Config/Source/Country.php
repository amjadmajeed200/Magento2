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

use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Country
 * @package Mageplaza\StoreLocator\Model\Config\Source
 */
class Country implements ArrayInterface
{
    /**
     * @var CountryCollection
     */
    protected $_countryCollection;

    /**
     * Country constructor.
     *
     * @param CountryCollection $countryCollection
     */
    public function __construct(
        CountryCollection $countryCollection
    ) {
        $this->_countryCollection = $countryCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Collection $countryCollection */
        $countryCollection = $this->_countryCollection->create()->loadByStore();
        $options           = $countryCollection->toOptionArray();
        array_shift($options);

        return $options;
    }
}
