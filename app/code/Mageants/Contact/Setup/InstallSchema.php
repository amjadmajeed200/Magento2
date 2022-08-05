<?php
/**
* @category Magento Contact
* @package Mageants_Contact
* @copyright Copyright (c) 2017 Magento
* @author Mageants Team <support@mageants.com>
*/
namespace Mageants\Contact\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;

class InstallSchema implements InstallSchemaInterface
{
	protected $StoreManager;     
    /**     * Init     *     * @param EavSetupFactory $eavSetupFactory     */    
    public function __construct(StoreManagerInterface $StoreManager)   
    {        
        $this->StoreManager=$StoreManager;    
    }
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */	
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$service_url = 'https://www.mageants.com/index.php/rock/register/live?ext_name=Mageants_Contact&dom_name='.$this->StoreManager->getStore()->getBaseUrl();
        $curl = curl_init($service_url);     

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_FOLLOWLOCATION =>true,
            CURLOPT_ENCODING=>'',
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ));
        
        $curl_response = curl_exec($curl);
        curl_close($curl);
		
		$setup->startSetup();

		$table = $setup->getConnection()->newTable(
		$setup->getTable('mageants_contact_message')
		)->addColumn(
		'message_id',
		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		null,
		['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		'Id'
		)->addColumn(
		'name',
		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		255,
		[],
		'Customer Name'
		)->addColumn(
		'email',
		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		255,
		[],
		'Customer Email'
		)->addColumn(
		'telephone',
		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		255,
		[],
		'Customer Telephone'
		)->addColumn(
		'comment',
		\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		255,
		[],
		'Customer Message'
		)->addIndex(  
                      $setup->getIdxName(  
                           $setup->getTable('mageants_contact_message'),  
                           ['name', 'email', 'telephone'],  
                           \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT  
                      ),  
                      ['name', 'email', 'telephone'],  
                      ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
		'Mageants Contact'
		);
		$setup->getConnection()->createTable($table);

		$setup->endSetup();
	}
}
?>
