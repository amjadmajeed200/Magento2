<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Category;

use Magenest\GoogleShopping\Model\ResourceModel\Template as GoogleShoppingTemplate;
use Magenest\GoogleShopping\Model\Template;
use Magenest\GoogleShopping\Model\TemplateFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Save
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Category
 */
class Save extends \Magento\Backend\App\Action
{
    /** @var TemplateFactory  */
    protected $_templateFactory;

    /** @var GoogleShoppingTemplate  */
    protected $_templateResource;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var LoggerInterface  */
    protected $_logger;

    public function __construct(
        TemplateFactory $templateFactory,
        GoogleShoppingTemplate $templateResource,
        Json $jsonFramework,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_templateResource = $templateResource;
        $this->_jsonFramework = $jsonFramework;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $flag = true;
        try {
            $params = $this->getRequest()->getParams();
            $templateModel = $this->_templateFactory->create();
            $this->_templateResource->load($templateModel, Template::CATEGORY_TEMPLATE, 'type');
            $contentTemplate = $params['content_template'] ?? [];
            $categoryMapped = $templateModel->handleDataMapping($contentTemplate);
            $templateModel->setContentTemplate($this->_jsonFramework->serialize($categoryMapped));
            $templateModel->setType(Template::CATEGORY_TEMPLATE);
            $this->_templateResource->save($templateModel);
        } catch (\Exception $exception) {
            $flag = false;
            $this->_logger->debug($exception->getMessage());
        }
        $controllerResult->setData($flag);
        return $controllerResult;
    }
}
