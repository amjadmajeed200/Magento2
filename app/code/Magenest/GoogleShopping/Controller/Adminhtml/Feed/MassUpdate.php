<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class MassUpdate
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class MassUpdate extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $count = 0;
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $status = $this->getRequest()->getParam('status');
            foreach ($collection->getItems() as $item) {
                $item->setStatus($status);
                $this->_googleFeedResource->save($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were modified.', $count));
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
