<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Category;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\ToolbarInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Edit extends Template
{
    protected $_templateModel = null;

    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /** @var ButtonList  */
    protected $buttonList;

    /** @var ToolbarInterface  */
    protected $toolbar;

    /** @var Json  */
    protected $_jsonFramework;

    public function __construct(
        Registry $coreRegistry,
        ButtonList $buttonList,
        ToolbarInterface $toolbar,
        Json $jsonFramework,
        Template\Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->buttonList = $buttonList;
        $this->toolbar = $toolbar;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    private function getTemplateModel()
    {
        if ($this->_templateModel == null) {
            $this->_templateModel = $this->_coreRegistry->registry('template_feed_model');
        }
        return $this->_templateModel;
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $arr = [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'dynamic_category' => [
                            'component' => 'Magenest_GoogleShopping/js/dynamic/category',
                            'config' => [
                                'mapping' => $this->getTemplateModel()->getMapping(),
                                'saveMappingUrl' => $this->getUrl("googleshopping/category/save")
                            ]
                        ],
                        'dynamic_category_search' => [
                            'component' => 'Magenest_GoogleShopping/js/dynamic/category/search',
                            'config' => []
                        ]
                    ]
                ]
            ]
        ];
        return $this->_jsonFramework->serialize($arr);
    }
    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('googleshopping/category/save', ['_current' => true]);
    }
}
