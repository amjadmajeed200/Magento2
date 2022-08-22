<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Button;

use Magenest\GoogleShopping\Api\FeedRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Psr\Log\LoggerInterface;

/**
 * Class GenericButton
 * @package Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit
 */
class GenericButton
{
    /** @var FeedRepositoryInterface  */
    protected $_feedRepository;

    /** @var Context  */
    protected $context;

    /** @var LoggerInterface  */
    protected $_logger;

    /**
     * GenericButton constructor.
     * @param FeedRepositoryInterface $feedRepository
     * @param Context $context
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeedRepositoryInterface $feedRepository,
        Context $context,
        LoggerInterface $logger
    ) {
        $this->_feedRepository = $feedRepository;
        $this->context = $context;
        $this->_logger = $logger;
    }

    /**
     * @return int|null
     */
    public function getFeedId()
    {
        try {
            return $this->_feedRepository->getById(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (\Exception $exception) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
