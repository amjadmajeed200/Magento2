<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Template\Edit\Tab\Renderer;

use Magento\Framework\Registry;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class Action extends AbstractRenderer
{
    protected $_feedModel = null;

    /**
     * Array to store all options data
     *
     * @var array
     */
    protected $_actions = [];

    /** @var Registry  */
    protected $_coreRegistry;

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
        return  $this->_feedModel;
    }

    /**
     * Render actions
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_actions = [];
        $feedModel = $this->getFeedModel();
        $params = [
            'id' => $row->getId()
        ];
        if ($feedModel->getId()) {
            $params += [
                'feedId' => $feedModel->getId()
            ];
        }
        $reorderAction = [
            '@' => [
                'href' => $this->getUrl(
                    'googleshopping/template/edit',
                    $params
                ),
            ],
            '#' => __('Edit'),
        ];
        $this->addToActions($reorderAction);
        return $this->_actionsToHtml();
    }
    /**
     * Get escaped value
     *
     * @param string $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return addcslashes($this->escapeHtml($value), '\\\'');
    }
    /**
     * Render options array as a HTML string
     *
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions = [])
    {
        $html = [];
        $attributesObject = new \Magento\Framework\DataObject();

        if (empty($actions)) {
            $actions = $this->_actions;
        }

        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('', $html);
    }
    /**
     * Add one action array to all options data storage
     *
     * @param array $actionArray
     * @return void
     */
    public function addToActions($actionArray)
    {
        $this->_actions[] = $actionArray;
    }
}
