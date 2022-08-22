<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Generate
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Generate extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $googleFeed = $this->getFeedModel();
                $productIds = $this->_ruleValidProduct->execute($googleFeed);
                $templateId = $googleFeed->getTemplateId();
                $data = [
                    'feed_id' => $id,
                    'template_id' => $templateId,
                    'product_ids' => $this->_jsonFramework->serialize($productIds)
                ];
                $this->_googleFeedIndexResource->insertData($data);
                $this->messageManager->addSuccessMessage(__("Generate Success!"));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addSuccessMessage(__("Something when wrong.Please try again!"));
            $this->_logger->debug($exception->getMessage());
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
