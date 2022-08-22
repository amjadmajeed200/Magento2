<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

/**
 * Class NewAction
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class NewAction extends AbstractTemplateAction
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
