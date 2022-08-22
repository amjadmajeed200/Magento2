<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Sync
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Sync extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            /** @var \Magenest\GoogleShopping\Model\GoogleFeed $googleFeedModel */
            $googleFeedModel = $this->getFeedModel();
            $feedId = $googleFeedModel->getId();
            $storeId = $googleFeedModel->getStoreId();
            $accounts = $googleFeedModel->getAccounts() ? $this->_jsonFramework->unserialize($googleFeedModel->getAccounts()) : [];
            $this->_cronModel->syncByFeedId($feedId, $storeId, $accounts);
            $records = $this->_googleProduct->getOfferIdsByFeedId($feedId);
            if (!count($records)) {
                throw new \Exception(__("Can not find any product. Please sync again"));
            }
            $this->_cronModel->productStatusBath($records, $feedId, $accounts);
            $this->messageManager->addSuccessMessage(__("Synced to Google Merchant success!"));
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
