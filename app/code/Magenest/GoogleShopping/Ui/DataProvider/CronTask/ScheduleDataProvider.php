<?php
namespace Magenest\GoogleShopping\Ui\DataProvider\CronTask;

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as CronScheduleCollection;

/**
 * Class ScheduleDataProvider
 * @package Magenest\GoogleShopping\Ui\DataProvider\CronTask
 */
class ScheduleDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;

    /**
     * ScheduleDataProvider constructor.
     * @param CronScheduleCollection $collectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CronScheduleCollection $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $this->setCollection($collectionFactory);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    /**
     * @param $collectionFactory
     * @return mixed
     */
    private function setCollection($collectionFactory)
    {
        return $collectionFactory->create()
            ->addFieldToFilter(
                'job_code',
                [
                    'in' => [
                        'magenest_google_shopping_sync',
                        'magenest_google_shopping_generate'
                    ]
                ]
            )
            ->setOrder('schedule_id', 'DESC');
    }
}
