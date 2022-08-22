<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Template
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Template extends AbstractFeedAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        try {
            $googleFeedModel = $this->initCurrentFeed();
            $resultRaw = $this->resultRawFactory->create();
            return $resultRaw->setContents(
                $this->layoutFactory->create()->createBlock(
                    \Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\FeedLog::class,
                    'googlefeed_edit_tab_log'
                )->toHtml()
            );
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
