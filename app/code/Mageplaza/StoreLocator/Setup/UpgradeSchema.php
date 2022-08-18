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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\StoreLocator\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            if ($installer->tableExists('mageplaza_storelocator_location')) {
                $columns = [
                    'product_ids'             => [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'Product Ids',
                    ],
                    'is_show_product_page'    => [
                        'type'    => Table::TYPE_SMALLINT,
                        'length'  => 1,
                        'comment' => 'Show on Product Page',
                    ],
                    'is_selected_all_product' => [
                        'type'    => Table::TYPE_SMALLINT,
                        'length'  => 1,
                        'comment' => 'Selected all product',
                    ],
                ];

                $tagTable = $installer->getTable('mageplaza_storelocator_location');
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tagTable, $name, $definition);
                }
            }

            if (!$connection->tableColumnExists($installer->getTable('sales_order'), 'mp_time_pickup')) {
                $connection->addColumn($installer->getTable('sales_order'), 'mp_time_pickup', [
                    'type'    => Table::TYPE_TEXT,
                    'length'  => 255,
                    'comment' => 'Pickup Time'
                ]);
            }
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            if ($installer->tableExists('mageplaza_storelocator_location')) {
                $setup->getConnection()->changeColumn(
                    $setup->getTable('mageplaza_storelocator_location'),
                    'product_ids',
                    'product_ids',
                    [
                        'type'     => Table::TYPE_TEXT,
                        'length'   => Table::MAX_TEXT_SIZE
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $connection->changeColumn($setup->getTable(
                'mageplaza_storelocator_location'),
                'postal_code',
                'postal_code', [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 255,
                ]);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            if ($installer->tableExists('mageplaza_storelocator_location')) {
                $columns = [
                    'facebook'             => [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'Facebook',
                    ],
                    'twitter'    => [
                        'type'    => Table::TYPE_TEXT,
                        'length'  => 255,
                        'comment' => 'twitter',
                    ]
                ];

                $tagTable = $installer->getTable('mageplaza_storelocator_location');
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tagTable, $name, $definition);
                }
            }
        }

        $installer->endSetup();
    }
}
