<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class CategoriesMapping extends Generic implements TabInterface
{
    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __("Categories Mapping");
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __("Categories Mapping");
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
    public function getJsConfig()
    {
        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'dynamic_category' => [
                            'component' => 'Magenest_ProductFeed/js/dynamic/category',
                            'config' => [
                                'mapping' => "",
                                'templateType' => ""
                            ]
                        ],
                        'dynamic_category_search' => [
                            'component' => 'Magenest_ProductFeed/js/dynamic/category/search',
                            'config' => []
                        ]
                    ]
                ]
            ]
        ];
    }
}
