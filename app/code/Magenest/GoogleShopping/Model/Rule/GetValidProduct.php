<?php
namespace Magenest\GoogleShopping\Model\Rule;

use Magenest\GoogleShopping\Model\GoogleFeed;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class GetValidProduct
{
    /** @var ProductCollectionFactory  */
    protected $_productCollectionFactory;

    /** @var RuleFactory  */
    protected $_ruleFactory;

    /** @var Config  */
    protected $_config;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        RuleFactory $ruleFactory,
        Config $config
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_ruleFactory = $ruleFactory;
        $this->_config = $config;
    }

    public function execute(\Magenest\GoogleShopping\Model\GoogleFeed $googleFeed)
    {
        $itemsPerPage = "500";
        $page = 0;
        $validProducts = [];
        /** @var \Magento\CatalogRule\Model\Rule $ruleModel */
        $ruleModel = $this->_ruleFactory->create();
        $ruleModel->setConditionsSerialized($googleFeed->getConditionsSerialized());
        $ruleModel->setStoreId($googleFeed->getStoreId());

        $lastProductPage = false;
        do {
            $productCollection = $this->prepareCollection($googleFeed, ++$page, $itemsPerPage);
            $ruleModel->getConditions()->collectValidatedAttributes($productCollection);
            if ($productCollection->getCurPage() >= $productCollection->getLastPageNumber()) {
                $lastProductPage = true;
            }
            $products = $productCollection->getItems();
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            foreach ($products as $product) {
                if ($this->validateProduct($ruleModel, $googleFeed, $product)) {
                    $validProducts[] = $product->getId();
                }
            }
        } while (!$lastProductPage);
        return $validProducts;
    }

    public function validateProduct($ruleModel, $googleFeed, $product)
    {
        $product->setStoreId($googleFeed->getStoreId());
        return $ruleModel->getConditions()->validate($product);
    }

    /**
     * @param GoogleFeed $googleFeedModel
     * @param $page
     * @param $itemsPerPage
     * @param array $ids
     */
    private function prepareCollection($googleFeedModel, $page, $itemsPerPage, $ids = [])
    {
        $productCollection = $this->_productCollectionFactory->create()
            ->addFieldToFilter(ProductInterface::STATUS, Status::STATUS_ENABLED);
        $productCollection->addStoreFilter($googleFeedModel->getStoreId());
        if ($ids) {
            $productCollection->addAttributeToFilter('entity_id', ['in' => $ids]);
        }
        $productCollection->setPage($page, $itemsPerPage);
        return $productCollection;
    }
}
