<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;

/**
 * Class ProductId
 * @package Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\Renderer
 */
class ProductId extends AbstractRenderer
{
    /** @var ProductRepositoryInterface  */
    protected $_productRepository;

    /**
     * ProductId constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Context $context,
        array $data = []
    ) {
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function render(DataObject $row)
    {
        $html = '';
        $sku = $row->getData($this->getColumn()->getIndex());
        if ($sku) {
            $product = $this->_productRepository->get($sku);
            if ($product->getId()) {
                $productId = $product->getId();
                $url = $this->getUrlProduct($productId);
                $html = "<a href='" . $url . "' target='_blank'>$productId</a>";
            }
        }
        return $html;
    }

    /**
     * @param $productId
     * @return string
     */
    private function getUrlProduct($productId)
    {
        return $this->getUrl(
            'catalog/product/edit',
            ['id' => $productId]
        );
    }
}
