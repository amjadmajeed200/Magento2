<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */

namespace Mageants\Contact\Block\Adminhtml\Contact\Edit;

/**
 * Adminhtml attachment edit form block
 *
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{	
	/**
	 * @var  $_systemStore
	 */
	protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
	 public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) 
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
	protected function _prepareForm()
	{
		$model = $this->_coreRegistry->registry('mageants_contact');
		$form = $this->_formFactory->create(
			['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
		);
		$form->setHtmlIdPrefix('contact_');		
		$fieldset = $form->addFieldset(
			'base_fieldset',
			['legend' => __('General Information'), 'class' => 'fieldset-wide']
		);
		if($model->getId()) {
			$fieldset->addField('message_id', 'hidden', ['name' => 'message_id']);
		}
		$fieldset->addField(
			'name',
			'text',
			['name' => 'name', 'label' => __('Customer Name'), 'title' => __('Customer Name'), 'disabled' => true]
		);	
		$fieldset->addField(
			'telephone',
			'text',
			['name' => 'telephone', 'label' => __('Customer Telephone'), 'title' => __('Customer Telephone'), 'disabled' => true]
		);
		$fieldset->addField(
			'comment',
			'textarea',
			['name' => 'description', 'label' => __('Customer Message'), 'title' => __('Customer Message'), 'disabled' => true]
			);
		$form->setValues($model->getData());
		$form->setUseContainer(true);		
		$this->setForm($form);
		return parent::_prepareForm();
	}
}
