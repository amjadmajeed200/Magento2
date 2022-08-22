<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Edit
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Edit extends AbstractFeedAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $saleRuleModel = $this->_ruleFactory->create();
            $googleFeed = $this->initCurrentFeed();
            if ($googleFeed->getId()) {
                $saleRuleModel->setData('conditions_serialized', $googleFeed->getData('conditions_serialized'));
            }
            $this->_coreRegistry->register('current_promo_sale_rule', $saleRuleModel);
            // 5. Build edit form
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $title = $googleFeed->getId() ? $googleFeed->getTitle() : __('New Google Shopping');
            $resultPage->setActiveMenu('Magenest_GoogleShopping::google_feed')
                ->addBreadcrumb($title, $title)
                ->addBreadcrumb($title, $title);
//            $resultPage->getConfig()->getTitle()->prepend(__('Edit Feed'));
//            $resultPage->getConfig()->getTitle()->prepend($googleFeed->getId() ? $googleFeed->getTitle() : __('New Feed'));
            return $resultPage;
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
