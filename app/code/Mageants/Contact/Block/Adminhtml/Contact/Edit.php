<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */
namespace Mageants\Contact\Block\Adminhtml\Contact;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Department edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'message_id';
        $this->_blockGroup = 'Mageants_Contact';
        $this->_controller = 'adminhtml_contact';

        parent::_construct();
        
        $this->buttonList->remove('save');
		$this->buttonList->remove('reset');


        if ($this->_isAllowedAction('Mageants_Contact::contact_manage')) {
           
          $this->addButton(
            'delete',
            [
                'label' => __('Delete'),
                'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                    . ','
                    . json_encode($this->getDeleteUrl()
                    )
                    . ')',
                'class' => 'scalable delete',
                'level' => -1
            ]
        );
        } 

    }
    
    public function getDeleteUrl(){	
			return $this->getUrl('mageants_contact/contact/delete', ['_current' => true]);
		}

    /**
     * Get header 
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('mageants_contact')->getId()) {
            return __("View Contact '%1'", $this->escapeHtml($this->_coreRegistry->registry('mageants_contact')->getName()));
        } else {
            return __('New Contact');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        //return $this->getUrl('mageants_contact/*/save', ['_current' => true, 'back' => 'edit']);
    }
}
