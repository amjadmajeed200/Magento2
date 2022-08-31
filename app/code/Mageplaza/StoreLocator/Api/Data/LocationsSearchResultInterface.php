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

namespace Mageplaza\StoreLocator\Api\Data;

/**
 * Interface AttributesSearchResultInterface
 * @package Mageplaza\OrderAttributes\Api\Data
 */
interface LocationsSearchResultInterface
{
    /**
     * @return \Mageplaza\StoreLocator\Api\Data\LocationsInterface[]
     */
    public function getItems();

    /**
     * @param \Mageplaza\StoreLocator\Api\Data\LocationsInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
