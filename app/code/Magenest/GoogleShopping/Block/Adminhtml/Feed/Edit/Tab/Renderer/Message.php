<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;

class Message extends AbstractRenderer
{
    /** @var Json  */
    protected $_jsonFramework;

    public function __construct(
        Json $jsonFramework,
        Context $context,
        array $data = []
    ) {
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $messages = $row->getData($this->getColumn()->getIndex());
        $html             = "<table><tr><th>Issue</th><th>Desciption</th></tr>";
        if ($messages) {
            try {
                $messagesArr = $this->_jsonFramework->unserialize($messages);
                foreach ($messagesArr as $arr) {
                    if (is_array($arr) && isset($arr['description']) && isset($arr['detail'])) {
                        $html .= "<tr><td>" . $arr['description'] . "</td><td>" . $arr['detail'] . "</td></tr>";
                    }
                }
                return $html . "</table>";
            } catch (\Exception $exception) {
                return '';
            }
        }
    }
    /**
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        $html             = [];
        $attributesObject = new \Magento\Framework\DataObject();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;<br/>&nbsp;</span>', $html);
    }
}
