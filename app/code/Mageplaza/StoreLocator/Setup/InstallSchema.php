<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Setup;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\StoreLocator\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var State
     */
    protected $state;

    /**
     * InstallSchema constructor.
     * @param State $appState
     */
    public function __construct(
        State $appState
    ) {
        $this->state = $appState;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            $this->state->getAreaCode();
        }
        catch (LocalizedException $ex) {
            $this->state->setAreaCode('global');
        }

        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_storelocator_location')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_storelocator_location'))
                ->addColumn('location_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Location Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Location Name')
                ->addColumn('status', Table::TYPE_SMALLINT, 1, [], 'Status')
                ->addColumn('description', Table::TYPE_TEXT, '64k', [], 'Location Description')
                ->addColumn('store_ids', Table::TYPE_TEXT, null, ['nullable' => false, 'unsigned' => true], 'Store Ids')
                ->addColumn('city', Table::TYPE_TEXT, 255, [], 'City')
                ->addColumn('country', Table::TYPE_TEXT, 255, [], 'Country')
                ->addColumn('street', Table::TYPE_TEXT, 255, [], 'Street')
                ->addColumn('state_province', Table::TYPE_TEXT, 255, [], 'State/Province')
                ->addColumn('postal_code', Table::TYPE_INTEGER, null, [], 'Zip/Postal Code')
                ->addColumn('url_key', Table::TYPE_TEXT, 255, [], 'Location URL Key')
                ->addColumn('latitude', Table::TYPE_TEXT, 255, [], 'Location Latitude')
                ->addColumn('longitude', Table::TYPE_TEXT, 255, [], 'Location Longitude')
                ->addColumn('phone_one', Table::TYPE_TEXT, 255, [], 'Phone #1')
                ->addColumn('phone_two', Table::TYPE_TEXT, 255, [], 'Phone #2')
                ->addColumn('website', Table::TYPE_TEXT, 255, [], 'Website')
                ->addColumn('is_config_website', Table::TYPE_SMALLINT, 1, [], 'Is Use Website Config')
                ->addColumn('fax', Table::TYPE_TEXT, 255, [], 'Fax')
                ->addColumn('email', Table::TYPE_TEXT, 255, [], 'Email')
                ->addColumn('images', Table::TYPE_TEXT, '2M', [], 'Images (Json)')
                ->addColumn('operation_mon', Table::TYPE_TEXT, '2M', [], 'Operation Hour Monday (Json)')
                ->addColumn('operation_tue', Table::TYPE_TEXT, '2M', [], 'Operation Hour Tuesday (Json)')
                ->addColumn('operation_wed', Table::TYPE_TEXT, '2M', [], 'Operation Hour Wednesday (Json)')
                ->addColumn('operation_thu', Table::TYPE_TEXT, '2M', [], 'Operation Hour Thursday (Json)')
                ->addColumn('operation_fri', Table::TYPE_TEXT, '2M', [], 'Operation Hour Friday (Json)')
                ->addColumn('operation_sat', Table::TYPE_TEXT, '2M', [], 'Operation Hour Saturday (Json)')
                ->addColumn('operation_sun', Table::TYPE_TEXT, '2M', [], 'Operation Hour Sunday (Json)')
                ->addColumn('is_default_store', Table::TYPE_SMALLINT, 1, [], 'Is Default Store')
                ->addColumn('time_zone', Table::TYPE_TEXT, 255, [], 'Time Zone')
                ->addColumn('is_config_time_zone', Table::TYPE_SMALLINT, 1, [], 'Is Use Time Zone Config')
                ->addColumn(
                    'sort_order',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Sort Order'
                )
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Location Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Location Created At')
                ->setComment('Store Locator Location');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_storelocator_holiday')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_storelocator_holiday'))
                ->addColumn('holiday_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Holiday Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Holiday Name')
                ->addColumn('status', Table::TYPE_SMALLINT, 1, [], 'Status')
                ->addColumn('from', Table::TYPE_TIMESTAMP, null, [], 'Holiday From Date')
                ->addColumn('to', Table::TYPE_TIMESTAMP, null, [], 'Holiday To Date')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Holiday Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Holiday Created At')
                ->setComment('Store Locator Holiday');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_storelocator_location_holiday')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_storelocator_location_holiday'))
                ->addColumn('holiday_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Holiday Id')
                ->addColumn('location_id', Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                    'primary'  => true,
                    'nullable' => false
                ], 'Location ID')
                ->addIndex($installer->getIdxName(
                    'mageplaza_storelocator_location_holiday',
                    ['location_id']
                ), ['location_id'])
                ->addIndex($installer->getIdxName(
                    'mageplaza_storelocator_location_holiday',
                    ['holiday_id']
                ), ['holiday_id'])
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_storelocator_location_holiday',
                        'location_id',
                        'mageplaza_storelocator_location',
                        'location_id'
                    ),
                    'location_id',
                    $installer->getTable('mageplaza_storelocator_location'),
                    'location_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_storelocator_location_holiday',
                        'holiday_id',
                        'mageplaza_storelocator_holiday',
                        'holiday_id'
                    ),
                    'holiday_id',
                    $installer->getTable('mageplaza_storelocator_holiday'),
                    'holiday_id',
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        'mageplaza_storelocator_location_holiday',
                        ['location_id', 'holiday_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['location_id', 'holiday_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Location To Holiday Link Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
