<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Adapter\DuplicateException;

/**
 * Class Save
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class Save extends AbstractTemplateAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $params = $this->getRequest()->getParams();
            $templateModel = $this->_templateFactory->create();
            $id = $params['id'] ?? null;
            if ($id) {
                $this->_templateResource->load($templateModel, $id);
                unset($params['id']);
            }
            $templateContent = $params['template_mapping'] ?? [];
            $templateName = "Template";
            if (isset($params['template_name']) && $params['template_name'] != "") {
                $templateName = $params['template_name'];
            }
            $templateModel->setName($templateName);
            $templateModel->setContentTemplate($this->_jsonFramework->serialize($templateContent));
            $this->_templateResource->save($templateModel);
            $urlRedirect = $this->getUrl(
                'googleshopping/template/edit',
                [
                    'id' => $templateModel->getId()
                ]
            );
            try {
                $attributesMapped = $this->_templateResource->getAllMagentoMappedFields($id);
                $attributesNew = [];
                if ($templateContent != '') {
                    foreach ($templateContent as $content) {
                        $content['template_id'] = $templateModel->getId();
                        $this->_templateResource->saveTemplateContent($content);
                        $attributesNew[] = $content['magento_attribute'];
                    }
                }
                $diffArray = array_diff($attributesMapped, $attributesNew);
                $this->_templateResource->deleteTemplateContent($id, $diffArray);
                $result = [
                    'flag' => true,
                    'message' => __("You saved the Template"),
                    'url' => $urlRedirect
                ];
                $this->messageManager->addSuccessMessage(__("You saved the Template"));
            } catch (DuplicateException $duplicateException) {
                $this->messageManager->addErrorMessage($duplicateException->getMessage());
                $result = [
                    'flag' => false,
                    'message' => $duplicateException->getMessage(),
                    'url' => $urlRedirect
                ];
                $controllerResult->setData($result);
                return $controllerResult;
            }
        } catch (\Exception $exception) {
            $result = [
                'flag' => false,
                'message' => $exception->getMessage()
            ];
            $this->_logger->debug($exception->getMessage());
        }
        $controllerResult->setData($result);
        return $controllerResult;
    }
}
