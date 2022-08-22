<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class UpdateStatus
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class UpdateStatus extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        try {
            /** @var \Magenest\GoogleShopping\Model\GoogleFeed $googleFeedModel */
            $googleFeedModel = $this->getFeedModel();
            $feedId = $googleFeedModel->getId();
            $records = $this->_googleProduct->getOfferIdsByFeedId($feedId);
            $accounts = $googleFeedModel->getAccounts() ? $this->_jsonFramework->unserialize($googleFeedModel->getAccounts()) : [];

            if (!count($records)) {
                throw new \Exception(__("Can not find any product. Please sync again"));
            }
            $this->_cronModel->productStatusBath($records, $feedId, $accounts);
            $this->messageManager->addSuccessMessage(__("Update Google Product success."));
            $redirectResult->setPath(
                '*/*/edit',
                [
                    'id' => $feedId
                ]
            );
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
            $redirectResult->setPath("*/*/index");
        }
        return $redirectResult;
    }
}
