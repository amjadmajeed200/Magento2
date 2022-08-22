<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Log
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Log extends AbstractFeedAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        try {
            $feedModel = $this->getFeedModel();
            $this->_coreRegistry->register('feed_model', $feedModel);
            return $this->resultLayoutFactory->create();
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
