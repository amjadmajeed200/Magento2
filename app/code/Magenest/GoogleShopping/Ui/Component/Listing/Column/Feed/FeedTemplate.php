<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed;

use Magenest\GoogleShopping\Model\ResourceModel\Template\CollectionFactory as FeedTemplateCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FeedTemplate
 * @package Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed
 */
class FeedTemplate implements OptionSourceInterface
{
    /** @var FeedTemplateCollectionFactory  */
    protected $_collectionFactory;

    /**
     * FeedTemplate constructor.
     * @param FeedTemplateCollectionFactory $collectionFactory
     */
    public function __construct(
        FeedTemplateCollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_collectionFactory->create()->toOptionArray();
    }
}
