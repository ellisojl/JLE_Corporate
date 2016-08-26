<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_product'))
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'identity'  => true,
	    'nullable'  => false,
	    'primary'   => true,
		), 'Client ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	    'primary'   => true,
		), 'Customer ID')
    ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'SKU')
	->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
	->addColumn('product_cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Cost')
	->addIndex($installer->getIdxName('jle_corporate/client_product', array('client_id', 'product_id', 'product_sku', 'product_price')), array('client_id', 'product_id', 'product_sku', 'product_price'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_product', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_product', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Product');
	
$installer->getConnection()->createTable($table);

$installer->endSetup();
$installer->installEntities();