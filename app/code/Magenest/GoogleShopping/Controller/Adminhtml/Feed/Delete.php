<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Delete
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Delete extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $googleFeedModel = $this->_googleFeedFactory->create();
                $this->_googleFeedResource->load($googleFeedModel, $id);
                if (!$googleFeedModel->getId()) {
                    throw new \Exception(__("Feed with Id %1 doesn't exit.", $id));
                }
                $this->_googleFeedResource->delete($googleFeedModel);
                $this->messageManager->addSuccessMessage(__("Feed with Id is %1 was deleted.", $id));
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
        }
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
