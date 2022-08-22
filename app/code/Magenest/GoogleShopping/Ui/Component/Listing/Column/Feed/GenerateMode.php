<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed;

use Magenest\GoogleShopping\Helper\Data;
use Magento\Framework\Data\OptionSourceInterface;

class GenerateMode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                "label" => __("Manually"),
                "value" => Data::GENERATE_MANUALLY
            ],
            [
                "label" => __("Schedule"),
                "value" => Data::GENERATE_SCHEDULE
            ]
        ];
    }
}
