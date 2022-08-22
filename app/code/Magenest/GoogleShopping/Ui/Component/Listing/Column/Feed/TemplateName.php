<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed;

use Magenest\GoogleShopping\Model\ResourceModel\Template;
use Magenest\GoogleShopping\Model\TemplateFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class TemplateName
 * @package Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed
 */
class TemplateName extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var TemplateFactory  */
    protected $_templateFactory;

    /** @var Template  */
    protected $_templateResource;

    public function __construct(
        TemplateFactory $templateFactory,
        Template $templateResource,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_templateResource = $templateResource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $templateId = $item['template_id'];
                $item['template_id'] = $this->_templateResource->getNameById($templateId);
            }
        }
        return $dataSource;
    }
}
