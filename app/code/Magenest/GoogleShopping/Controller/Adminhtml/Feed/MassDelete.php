<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class MassDelete
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class MassDelete extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $count = 0;
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            foreach ($collection->getItems() as $item) {
                $this->_googleFeedResource->delete($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were deleted.', $count));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
