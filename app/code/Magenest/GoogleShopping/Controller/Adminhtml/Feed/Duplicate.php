<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

use Magenest\GoogleShopping\Model\GoogleFeed;

/**
 * Class Duplicate
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Duplicate extends AbstractFeedAction
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
                $googleFeedModel->unsetData('id');
                $googleFeedModel->setStatus(GoogleFeed::STATUS_DISABLE);
                $feedName = $googleFeedModel->getName();
                $googleFeedModel->setName($feedName . " Duplicate");
                $googleFeedModel->setStatus(GoogleFeed::STATUS_DISABLE);
                $this->_googleFeedResource->save($googleFeedModel);
                $this->messageManager->addSuccessMessage(__("Feed data has been successfully duplicated."));
                $redirectResult->setPath('*/*/edit', ['id' => $googleFeedModel->getId()]);
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
                $redirectResult->setPath('*/*/index');
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $redirectResult->setPath('*/*/index');
            $this->_logger->debug($exception->getMessage());
        }
        return $redirectResult;
    }
}
