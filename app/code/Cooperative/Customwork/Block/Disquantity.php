<?php
namespace Cooperative\Customwork\Block;
use Rokanthemes\BestsellerProduct\Block\Bestseller as Bestseller;

class Disquantity extends Bestseller
{

    public function getPro()
    {
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();

        $visibleProducts = $objectManager->create('\Magento\Catalog\Model\Product\Visibility')->getVisibleInCatalogIds();
        $collection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')->setVisibility($visibleProducts);

        $collection = $this->_addProductAttributesAndPrices($collection);

        $stockFilter = $objectManager->create('\Magento\CatalogInventory\Helper\Stock');
        $stockFilter->addInStockFilterToCollection($collection);

        $collection
            ->addAttributeToFilter(
                'special_price',
                ['gt'=>0], 'left'
            )->addAttributeToFilter(
                'special_from_date',['or' => [ 0 => ['date' => true,
                'to' => date('Y-m-d',time()).' 23:59:59'],
                1 => ['is' => new \Zend_Db_Expr(
                    'null'
                )],]], 'left'
            )->addAttributeToFilter(
                'special_to_date',  ['or' => [ 0 => ['date' => true,
                'from' => date('Y-m-d',time()).' 00:00:00'],
                1 => ['is' => new \Zend_Db_Expr(
                    'null'
                )],]], 'left'
            );
        return $collection;
    }
}
