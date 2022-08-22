<?php
namespace Magenest\GoogleShopping\Model\GoogleFeed;

use Magenest\GoogleShopping\Model\GoogleFeedFactory;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed as GoogleFeedResourceModel;
use Psr\Log\LoggerInterface;

/**
 * Class FeedRepository
 * @package Magenest\GoogleShopping\Model\GoogleFeed
 */
class FeedRepository implements \Magenest\GoogleShopping\Api\FeedRepositoryInterface
{
    /** @var GoogleFeedFactory  */
    protected $_googleFeedFactory;

    /** @var GoogleFeedResourceModel  */
    protected $_googleFeedResource;

    /** @var LoggerInterface  */
    protected $_logger;

    /**
     * FeedRepository constructor.
     * @param GoogleFeedFactory $googleFeedFactory
     * @param GoogleFeedResourceModel $googleFeedResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        GoogleFeedFactory $googleFeedFactory,
        GoogleFeedResourceModel $googleFeedResource,
        LoggerInterface $logger
    ) {
        $this->_googleFeedFactory = $googleFeedFactory;
        $this->_googleFeedResource = $googleFeedResource;
        $this->_logger = $logger;
    }

    /**
     * @param string $feedId
     * @return \Magenest\GoogleShopping\Api\Data\FeedInterface|\Magenest\GoogleShopping\Model\GoogleFeed
     * @throws \Exception
     */
    public function getById($feedId)
    {
        $googleFeed = $this->_googleFeedFactory->create();
        $this->_googleFeedResource->load($googleFeed, $feedId);
        if (!$googleFeed->getId()) {
            throw new \Exception(__('The Feed with the "%1" ID doesn\'t exist.', $feedId));
        }
        return $googleFeed;
    }
}
