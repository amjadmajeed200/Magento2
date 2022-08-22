<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Index
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Index extends AbstractFeedAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Google Feeds'));
            return $resultPage;
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
