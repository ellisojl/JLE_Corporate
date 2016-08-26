<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client'))
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
	    'identity'  => true,
	    'nullable'  => false,
	    'primary'   => true,
		), 'Client ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
	    'unsigned'  => true,
	    'nullable'  => false,
	    'default'   => '0',
		), 'Customer Group ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
	    'unsigned'  => true,
	    'nullable'  => true,
	    'default'   => NULL,
		), 'Attribute Set ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    	'unsigned'  => true,
    	'nullable'  => false,
    	'default'   => '0',
		), 'Website ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Active')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
		), 'Account Name')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
		), 'Account Code')
    //->addIndex($installer->getIdxName('jle_corporate/client', 'customer_group_id'), array('customer_group_id'))
	->addIndex($installer->getIdxName('jle_corporate/client', array('name', 'code', 'website_id')), array('name', 'code', 'website_id'))
    ->addForeignKey($installer->getFkName('jle_corporate/client', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity');
	
$installer->getConnection()->createTable($table);

$installer->endSetup();
$installer->installEntities();