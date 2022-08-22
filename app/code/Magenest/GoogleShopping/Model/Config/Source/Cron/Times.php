<?php
namespace Magenest\GoogleShopping\Model\Config\Source\Cron;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Times
 * @package Magenest\GoogleShopping\Model\Config\Source\Cron
 */
class Times implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $stTime = strtotime(date('Y-m-d'));
        $times = [];
        /**
         * 1440 is minutes of a day
         * 30 is range time
         * 60 is minutes of an hour
         */
        for ($time = 0; $time < 1440; $time += 30) {
            $times[$time] = ['label' => date('g:i A', $stTime + ($time * 60)), 'value' => $time];
        }
        return $times;
    }
}
