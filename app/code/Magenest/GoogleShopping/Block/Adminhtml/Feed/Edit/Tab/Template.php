<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Template extends \Magento\Backend\Block\Template implements TabInterface
{
    protected $_template = "Magenest_GoogleShopping::tab/view/template_attribute.phtml";
    /** @var Registry  */
    protected $_coreRegistry;

    protected $_feedModel = null;

    public function __construct(
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getFeedModel()
    {
        if ($this->_feedModel == null) {
            $this->_feedModel = $this->_coreRegistry->registry('feed_model');
        }
        return $this->_feedModel;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __("Template Attributes");
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __("Template Attributes");
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    public function getTemplateData()
    {
        /** @var JsonHelper $jsonHelper */
        $jsonHelper = $this->getData('jsonHelper');
        $feedModel = $this->getFeedModel();
        $this->jsLayout['components']['template_attributes']['component'] = "Magenest_GoogleShopping/js/feed/attributes";
        $this->jsLayout['components']['template_attributes']['template']  = "Magenest_GoogleShopping/view/feed/template-attributes";
        $contentTemplate = null;
        if ($feedModel->getData('content_template')) {
            $contentTemplate = $jsonHelper->jsonDecode($feedModel->getData('content_template'));
        }
        $this->jsLayout['components']['template_attributes']['config']['about_event'] = $contentTemplate;

        return $jsonHelper->jsonEncode($this->jsLayout);
    }
}
