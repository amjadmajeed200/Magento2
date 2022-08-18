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

namespace Mageplaza\StoreLocator\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class LocationNum
 * @package Mageplaza\StoreLocator\Ui\Component\Listing\Columns
 */
class LocationNum extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    if ((int) $item[$this->getData('name')] === 0) {
                        $item[$this->getData('name')] = __('No stores');
                    } elseif ((int) $item[$this->getData('name')] === 1) {
                        $item[$this->getData('name')] = __('Applied %1 store', $item[$this->getData('name')]);
                    } else {
                        $item[$this->getData('name')] = __('Applied %1 stores', $item[$this->getData('name')]);
                    }
                }
            }
        }

        return $dataSource;
    }
}
