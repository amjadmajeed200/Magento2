<?php
namespace Cooperative\Customwork\Block;
use Rokanthemes\BestsellerProduct\Block\Bestseller as Bestseller;

class Disquantity extends Bestseller
{

    public function getPro()
    {
        $storeId    = $this->storeManager->getStore()->getId();
        $products = $this->productCollectionFactory->create()->setStoreId($storeId);
        $select = $this->connection->select()
            ->from($this->resource->getTableName('sales_order_item'), 'product_id')
            ->order('sum(`qty_ordered`) Desc')
            ->group('product_id')
            ->limit(2);
        $producIds = array();
        foreach ($this->connection->query($select)->fetchAll() as $row) {
            $producIds[] = $row['product_id'];
        }
        $products
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToFilter('entity_id', array('in'=>$producIds))
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $products->setPageSize($this->getConfig('qty'))->setCurPage(1);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $products]
        );
        return $products;
    }
}
