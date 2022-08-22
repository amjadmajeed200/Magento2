<?php
namespace Magenest\GoogleShopping\Ui\DataProvider;

use Magenest\GoogleShopping\Model\ResourceModel\Template\CollectionFactory;
use Magenest\GoogleShopping\Model\Template;

/**
 * Class TemplateDataProvider
 * @package Magenest\GoogleShopping\Ui\DataProvider
 */
class TemplateDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;

    /**
     * TemplateDataProvider constructor.
     * @param CollectionFactory $collection
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collection,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $this->setCollection($collection);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @param $collectionFactory
     * @return mixed
     */
    private function setCollection($collectionFactory)
    {
        return $collectionFactory->create()->addFieldToFilter('type', Template::PRODUCT_TEMPLATE);
    }
}
