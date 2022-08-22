<?php
namespace Magenest\GoogleShopping\Model\Config\Source;

use Magenest\GoogleShopping\Helper\Data;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class SyncMode
 * @package Magenest\GoogleShopping\Model\Config\Source
 */
class SyncMode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Data::GENERATE_MANUALLY => __('Manually'),
            Data::GENERATE_SCHEDULE => __('By Schedule')
        ];
    }
}
