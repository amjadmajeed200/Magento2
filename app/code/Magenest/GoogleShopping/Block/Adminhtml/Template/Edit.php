<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Template;

use Magenest\GoogleShopping\Helper\Data as HelperData;
use Magenest\GoogleShopping\Helper\GoogleShoppingAttribute;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

/**
 * Class Edit
 * @package Magenest\GoogleShopping\Block\Adminhtml\Template
 */
class Edit extends Template
{
    protected $_templateFeedModel = null;

    /** @var HelperData  */
    protected $_helperData;

    /** @var GoogleShoppingAttribute  */
    protected $_googleShoppingAttribute;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var Registry  */
    protected $_coreRegistry;

    public function __construct(
        HelperData $helperData,
        GoogleShoppingAttribute $googleShoppingAttribute,
        Json $jsonFramework,
        Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_googleShoppingAttribute = $googleShoppingAttribute;
        $this->_jsonFramework = $jsonFramework;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    private function getTemplateModel()
    {
        if ($this->_templateFeedModel == null) {
            $this->_templateFeedModel = $this->_coreRegistry->registry('template_feed_model');
        }
        return $this->_templateFeedModel;
    }

    /**
     * @return bool|string
     */
    public function getTemplateData()
    {
        $feedModel = $this->getTemplateModel();
        $feedId = $feedModel->getId();
        $this->jsLayout['components']['template_attributes']['component'] = "Magenest_GoogleShopping/js/feed/templates";
        $this->jsLayout['components']['template_attributes']['displayArea'] = "template_attributes";
        $this->jsLayout['components']['template_attributes']['template']  = "Magenest_GoogleShopping/view/feed/template";
        $contentTemplate = null;
        $templateName = "";
        if ($feedModel->getData('name')) {
            $templateName = $feedModel->getData('name');
        }
        $this->jsLayout['components']['template_attributes']['config']['attributes']['google_attribute'] = $this->_googleShoppingAttribute->getGoogleShoppingAttribute();
        $this->jsLayout['components']['template_attributes']['config']['attributes']['magento_attribute'] = $this->_helperData->getProductAttribute();
        $this->jsLayout['components']['template_attributes']['config']['attributes']['mapped_fields'] = $this->_helperData->getFieldsMappedByTemplateId($feedId);
        $this->jsLayout['components']['template_attributes']['config']['attributes']['save_mapping_url'] = $this->_urlBuilder->getUrl("*/*/save");
        $this->jsLayout['components']['template_attributes']['config']['attributes']['get_mapping_url'] = $this->_urlBuilder->getUrl("*/*/getFields");
        $this->jsLayout['components']['template_attributes']['config']['attributes']['template_listing_url'] = $this->_urlBuilder->getUrl("*/*/index");
        $this->jsLayout['components']['template_attributes']['config']['attributes']['template_name'] = $templateName;

        return $this->_jsonFramework->serialize($this->jsLayout);
    }

    /**
     * @return |null
     */
    public function getTemplateId()
    {
        $templateModel = $this->getTemplateModel();
        return $templateModel->getId() ?? null;
    }

    protected function getFieldMapped($templateId)
    {
        return null;
    }
}
