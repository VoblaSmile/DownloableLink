<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('downloadable_link_purchased_item'),
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
        }

        $setup->endSetup();
    }
}
