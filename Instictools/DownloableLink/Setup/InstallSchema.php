<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Instictools\DownloableLink\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('downloadable_link'),
            'is_visible',
            [
                'type'     => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'after'    => 'is_shareable',
                'comment'  => 'Is visible'
            ]
        );

        $installer->endSetup();
    }
}
