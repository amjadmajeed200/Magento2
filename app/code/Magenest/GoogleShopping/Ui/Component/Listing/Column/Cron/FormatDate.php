<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Cron;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class FormatDate
 * @package Magenest\GoogleShopping\Ui\Component\Listing\Column\Cron
 */
class FormatDate extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var TimezoneInterface  */
    protected $_localeDate;

    public function __construct(
        TimezoneInterface $localeDate,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_localeDate = $localeDate;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $dateFieldName = $this->getData('name');
                $item[$dateFieldName] = $this->formatDate(
                    $item[$dateFieldName],
                    \IntlDateFormatter::MEDIUM,
                    true
                );
            }
        }
        return $dataSource;
    }

    /**
     * @param null $date
     * @param int $format
     * @param false $showTime
     * @param null $timezone
     * @return string
     * @throws \Exception
     */
    protected function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date ?? 'now');
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }
}
