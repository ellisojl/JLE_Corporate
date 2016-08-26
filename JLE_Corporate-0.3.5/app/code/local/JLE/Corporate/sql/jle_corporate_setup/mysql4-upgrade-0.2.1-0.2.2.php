<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_customer'))
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'identity'  => true,
	    'nullable'  => false,
	    'primary'   => true,
		), 'Client ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	    'primary'   => true,
		), 'Customer ID')
    ->addColumn('is_supervisor', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Supervisor')
	->addIndex($installer->getIdxName('jle_corporate/client_customer', array('client_id', 'customer_id', 'is_supervisor')), array('client_id', 'customer_id', 'is_supervisor'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_customer', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_customer', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Customer');
	
$installer->getConnection()->createTable($table);

$installer->endSetup();
$installer->installEntities();