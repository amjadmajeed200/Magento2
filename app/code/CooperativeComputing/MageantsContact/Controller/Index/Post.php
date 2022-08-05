<?php
/**
 * CooperativeComputing
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   CooperativeComputing
 * @package    CooperativeComputing_MageantsContact
 * @copyright  Copyright (c) 2022 CooperativeComputing (https://www.magento.com/)
 */

namespace CooperativeComputing\MageantsContact\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Post
 * @package CooperativeComputing\MageantsContact\Controller\Index
 */
class Post extends \Mageants\Contact\Controller\Index\Post
{

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
	 * Post Construct.
	 * 
     * @param Context 					$context
     * @param ConfigInterface 			$contactsConfig
     * @param MailInterface 			$mail
     * @param DataPersistorInterface	$dataPersistor
     * @param LoggerInterface 			$logger
	 * @param StateInterface 			$inlineTranslation
	 * @param ScopeConfigInterface		$scopeConfig
     */
    public function __construct(
        Context 				$context,
        ConfigInterface 		$contactsConfig,
        MailInterface 			$mail,
        DataPersistorInterface	$dataPersistor,
        LoggerInterface 		$logger = null,
		StateInterface 			$inlineTranslation,
		ScopeConfigInterface	$scopeConfig
    ) {
        parent::__construct($context, $contactsConfig, $mail, $dataPersistor, $logger);
		$this->inlineTranslation = $inlineTranslation;
		$this->scopeConfig = $scopeConfig;
    }

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
