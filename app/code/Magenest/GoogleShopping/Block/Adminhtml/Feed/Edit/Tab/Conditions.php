<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Conditions
 * @package Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab
 */
class Conditions extends Generic implements TabInterface
{
    /** @var Fieldset  */
    protected $_rendererFieldset;

    /** @var \Magento\Rule\Block\Conditions  */
    protected $_conditions;

    /** @var \Magento\CatalogRule\Model\RuleFactory  */
    private $_ruleFactory;

    /** @var Json  */
    protected $_jsonFramework;

    protected $_feedModel = null;

    /**
     * Conditions constructor.
     * @param Fieldset $rendererFieldset
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param RuleFactory $ruleFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param Json $jsonFramework
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Json $jsonFramework,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->_ruleFactory = $ruleFactory;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_sale_rule');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'google_feed_form')
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $this->setForm($form);
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'catalog_rule/promo_catalog/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->_rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            ['legend' => __('Conditions (don\'t add conditions if you want to include all products in the feed)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions_serialized',
            'text',
            [
                'name' => 'conditions_serialized',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->_conditions);
        if ($this->getRequest()->getParam('id')) {
            $feedModel = $this->getFeedModel();
            $editData = $feedModel->getData();
            if ($editData['conditions_serialized']) {
                $editData['conditions_serialized'] = $this->_jsonFramework->unserialize($editData['conditions_serialized']);
            }
            $editData['id'] = $this->getRequest()->getParam('id');
            $form->setValues($editData);
        }
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);
        return $form;
    }

    /**
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
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
        return __("Conditions");
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __("Conditions");
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function canShowTab()
    {
        return $this->getFeedModel()->getId();
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
}
