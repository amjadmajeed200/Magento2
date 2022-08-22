<?php
namespace Magenest\GoogleShopping\Model;

use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed\CollectionFactory as GoogleFeedCollectionFactory;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex as GoogleFeedIndexResourceModel;
use Magenest\GoogleShopping\Model\Rule\GetValidProduct;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class GenerateCron
 * @package Magenest\GoogleShopping\Model
 */
class GenerateCron
{

    /** @var GoogleFeedCollectionFactory  */
    protected $_googleFeedCollectionFactory;

    /** @var GetValidProduct  */
    protected $_ruleValidProduct;

    /** @var GoogleFeedIndexResourceModel  */
    protected $_googleFeedIndexResource;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var Json  */
    protected $_jsonFramework;

    /**
     * GenerateCron constructor.
     * @param GoogleFeedCollectionFactory $googleFeedCollectionFactory
     * @param GetValidProduct $ruleValidProduct
     * @param GoogleFeedIndexResourceModel $googleFeedIndex
     * @param LoggerInterface $logger
     * @param Json $jsonFramework
     */
    public function __construct(
        GoogleFeedCollectionFactory $googleFeedCollectionFactory,
        GetValidProduct $ruleValidProduct,
        GoogleFeedIndexResourceModel $googleFeedIndex,
        LoggerInterface $logger,
        Json $jsonFramework
    ) {
        $this->_googleFeedCollectionFactory = $googleFeedCollectionFactory;
        $this->_ruleValidProduct = $ruleValidProduct;
        $this->_googleFeedIndexResource = $googleFeedIndex;
        $this->_logger = $logger;
        $this->_jsonFramework = $jsonFramework;
    }

    public function execute()
    {
        try {
            $collections = $this->_googleFeedCollectionFactory->create()
                ->addFieldToFilter('status', GoogleFeed::STATUS_ENABLE);

            /** @var GoogleFeed $collection */
            foreach ($collections as $collection) {
                if ($collection->getId()) {
                    $productIds = $this->_ruleValidProduct->execute($collection);
                    $templateId = $collection->getTemplateId();
                    $data = [
                        'feed_id' => $collection->getId(),
                        'template_id' => $templateId,
                        'product_ids' => $this->_jsonFramework->serialize($productIds)
                    ];
                    $this->_googleFeedIndexResource->insertData($data);
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
