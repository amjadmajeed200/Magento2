<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

/**
 * Class Save
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
class Save extends AbstractFeedAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        try {
            $params = $this->getRequest()->getParams();
            $googleFeedModel = $this->_googleFeedFactory->create();
            $id =  $params['general']['id'] ?? null;
            if ($id) {
                $this->_googleFeedResource->load($googleFeedModel, $id);
                unset($params['id']);
            }
            $dataRaw = $params['general'];
            $ga = $params['ga'] ?? [];
            $dataRaw = array_merge($ga, $dataRaw);
            if (!isset($params['rule']) && isset($params['general']['conditions_serialized'])) {
                $dataRaw['conditions_serialized'] = $params['general']['conditions_serialized'];
            } else {
                $conditions = $params['rule'] ?? [];
                $dataRaw['conditions_serialized'] = $this->saveConditions($conditions);
            }
            if (isset($dataRaw['accounts']) && !empty($dataRaw['accounts'])) {
                $dataRaw['accounts'] = $this->_jsonFramework->serialize($dataRaw['accounts']);
            } else {
                $dataRaw['accounts'] = null;
            }
            $googleFeedModel->addData($dataRaw);
            $this->_googleFeedResource->save($googleFeedModel);
            $this->messageManager->addSuccessMessage(__("You saved the Feed."));
            if (isset($params['back']) && $params['back'] == 'edit') {
                $redirectResult->setPath('*/*/edit', ['id' => $googleFeedModel->getId()]);
            } else {
                $redirectResult->setPath('*/*/index');
            }
        } catch (\Exception $exception) {
            $redirectResult->setPath('*/*/index');
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
        }
        return $redirectResult;
    }

    /**
     * @param $conditions
     * @return bool|string
     */
    private function saveConditions($conditions)
    {
        $salesRuleModel = $this->_ruleFactory->create();
        $salesRuleModel->loadPost($conditions);
        $asArray = $salesRuleModel->getConditions()->asArray();
        return $this->_jsonFramework->serialize($asArray);
    }
}
