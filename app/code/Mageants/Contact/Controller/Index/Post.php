<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */	
namespace Mageants\Contact\Controller\Index;

use Magento\Framework\App\Request\DataPersistorInterface;

class Post extends \Magento\Contact\Controller\Index\Post
{
    /**
     * Post action
     */	
    public function execute()
    {
		
		$isEnabled = $this->_objectManager->create('Mageants\Contact\Helper\Data')->getContactConfig('mageants_contact/contact/enabled');
		if($isEnabled)
		{		
			$data = $this->getRequest()->getPostValue(); 
			$model = $this->_objectManager->create('Mageants\Contact\Model\Contact');
			$model->setData($data);			
			if($model->save())
			{        		
				try {
					/* Send Email to Customer*/
					$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
					$to = $data['email'];
					$templateVars = array(
										'name' => $data['name'],
										'message' => 'Thanks for the inquiry we will contact you  soon.',
										);
					$from = $this->scopeConfig->getValue('contact/email/sender_email_identity', $storeScope);
					
					$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
					$transport = $this->_transportBuilder
					->setTemplateIdentifier('send_email_email_template')
					->setTemplateOptions($templateOptions)
					->setTemplateVars($templateVars)
					->setFrom($from)
					->addTo($data['email'],$data['name'])
					->setReplyTo($this->scopeConfig->getValue('contact/email/recipient_email', $storeScope))
					->getTransport();
					$transport->sendMessage(); 
					/* Email Sent  to Customer */
					
					/* Email Send to Admin */					
					$templateVars = array(
										'name' => $data['name'],
										'email' => $data['email'],
										'telephone' => $data['telephone'],
										'comment' => $data['comment'],
										);
					$from = $this->scopeConfig->getValue('contact/email/sender_email_identity', $storeScope);	
					$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());						 
					$transport = $this->_transportBuilder
					->setTemplateIdentifier('send_email_admin_template')
					->setTemplateOptions($templateOptions)
					->setTemplateVars($templateVars)
					->setFrom($from)
					->addTo($this->scopeConfig->getValue('contact/email/recipient_email', $storeScope))
					->setReplyTo($this->scopeConfig->getValue('contact/email/recipient_email', $storeScope))
					->getTransport();
					$transport->sendMessage(); 					
					/* Email Sent to Admin */
					
					$this->messageManager->addSuccess(
						__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
					);
					$this->_redirect('contact/index');
					return;				
				}
				catch (\Exception $e) {				
					$this->inlineTranslation->resume();
					$this->messageManager->addSuccess(
						__('Thanks for contacting us with your comments and questions. There are some mailing isseu so contact on '.$this->scopeConfig->getValue('contact/email/recipient_email'))
					);
					
					$this->_redirect('contact/index');
					return;
				}
			}
		}
		else
		{
			return parent::execute();
		}
	} 
}
