<?php
namespace Magenest\GoogleShopping\Model\Config\Source\Cron;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Locale\ListsInterface;

/**
 * Class Days
 * @package Magenest\GoogleShopping\Model\Config\Source\Cron
 */
class Days implements OptionSourceInterface
{
    /** @var ListsInterface  */
    protected $_localeList;

    /**
     * Days constructor.
     * @param ListsInterface $localList
     */
    public function __construct(
        ListsInterface $localList
    ) {
        $this->_localeList = $localList;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_localeList->getOptionWeekdays();
    }
}
