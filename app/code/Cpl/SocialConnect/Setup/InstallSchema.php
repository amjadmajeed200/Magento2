<?php
/**
 * Cpl
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Cpl
 * @package    Cpl_SocialConnect
 * @copyright  Copyright (c) 2022 Cpl (https://www.magento.com/)
 */

namespace Cpl\SocialConnect\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Cpl\SocialConnect\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        // Get main table
        $mainTableName = $installer->getTable('cpl_socialconnect_customer');
        if (!$installer->tableExists('cpl_socialconnect_customer')) {
            $table = $installer->getConnection()
                ->newTable($mainTableName)
                ->addColumn('id', Table::TYPE_INTEGER, 11, [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ], 'Social Connect User Id')
                ->addColumn('sociallogin_id', Table::TYPE_TEXT, 255, ['unsigned' => true, 'nullable => false'], 'Social Connect Id')
                ->addColumn('customer_id', Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable => false'], 'Magento Customer Id')
                ->addColumn('type', Table::TYPE_TEXT, 255, ['default' => ''], 'Type')
                ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Created At')
                ->addForeignKey(
                    $installer->getFkName('cpl_socialconnect_customer', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE)
                ->setComment('Social Connect Customer Table');
            $installer->getConnection()->createTable($table);
            //Add index 
            $installer->getConnection()->addIndex(
                $installer->getTable($mainTableName),
                $setup->getIdxName(
                    $installer->getTable($mainTableName),
                    ['type'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['type'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );            
        }
        $installer->endSetup();
    }
}
