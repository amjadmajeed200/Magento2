<?php
namespace Magenest\GoogleShopping\Model\Block;

use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed\CollectionFactory as GoogleFeedCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var GoogleFeedCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Json
     */
    protected $json;

    /**
     * DataProvider constructor.
     * @param GoogleFeedCollectionFactory $googleFeedCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        GoogleFeedCollectionFactory $googleFeedCollectionFactory,
        DataPersistorInterface $dataPersistor,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ScopeConfigInterface $scopeConfig,
        Json $json,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $googleFeedCollectionFactory->create();
        $this->collectionFactory = $googleFeedCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            if ($item->getData('accounts')) {
                $item->setData('accounts', $this->json->unserialize($item->getData('accounts')));
            }
            $this->loadedData[$item->getId()]['general']  = $item->getData();
            $this->loadedData[$item->getId()]['ga']  = $item->getData();
        }
        return $this->loadedData;
    }
}
