<?php
namespace Magenest\GoogleShopping\Model;

use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex as GoogleFeedIndexResourceModel;
use Magenest\GoogleShopping\Model\Rule\GetValidProduct;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json as JsonFramework;

/**
 * Class GoogleFeed
 * @package Magenest\GoogleShopping\Model
 */
class GoogleFeed extends AbstractModel
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    /**
     * @var string
     */
    protected $_eventPrefix = 'google_feed';

    /** @var GoogleFeedIndexResourceModel  */
    protected $_googleFeedIndexResource;

    /** @var GetValidProduct  */
    protected $_ruleValidProduct;

    /** @var JsonFramework  */
    protected $_jsonFramework;

    /**
     * GoogleFeed constructor.
     * @param GoogleFeedIndexResourceModel $googleFeedIndex
     * @param GetValidProduct $ruleValidProduct
     * @param JsonFramework $jsonFramework
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        GoogleFeedIndexResourceModel $googleFeedIndex,
        GetValidProduct $ruleValidProduct,
        JsonFramework $jsonFramework,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_googleFeedIndexResource = $googleFeedIndex;
        $this->_ruleValidProduct = $ruleValidProduct;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed');
    }

    /**
     * @return GoogleFeed
     * @throws \Exception
     */
    public function afterSave()
    {
        $this->generateProduct($this);
        return parent::afterSave();
    }

    /**
     * @param GoogleFeed $googleFeedModel
     * @throws \Exception
     */
    private function generateProduct(\Magenest\GoogleShopping\Model\GoogleFeed $googleFeedModel)
    {
        try {
            $id = $googleFeedModel->getId();
            $productIds = $this->_ruleValidProduct->execute($googleFeedModel);
            $templateId = $googleFeedModel->getTemplateId();
            $data = [
                'feed_id' => $id,
                'template_id' => $templateId,
                'product_ids' => $this->_jsonFramework->serialize($productIds)
            ];
            $this->_googleFeedIndexResource->insertData($data);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
