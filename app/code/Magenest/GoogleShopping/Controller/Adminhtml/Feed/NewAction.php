<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class NewAction
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class NewAction extends AbstractFeedAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
