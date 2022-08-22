<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed;

use Magenest\GoogleShopping\Model\GoogleFeed;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed
 */
class Status implements OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => GoogleFeed::STATUS_ENABLE, 'label' => __('Active')],
            ['value' => GoogleFeed::STATUS_DISABLE, 'label' => __('Inactive')],
        ];
    }
}
