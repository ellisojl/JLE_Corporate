<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'jle_corporate/client_datetime'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_datetime'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Client Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
        'default' => $installer->getConnection()->getSuggestedZeroDate()
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'jle_corporate/client_datetime',
            array('client_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('client_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('jle_corporate/client_datetime', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_datetime', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_datetime', array('client_id')),
        array('client_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_datetime', array('client_id', 'attribute_id', 'value')),
        array('client_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_datetime', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_datetime', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('jle_corporate/client_datetime', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity Datetime');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/client_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Client Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'jle_corporate/client_decimal',
            array('client_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('client_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('jle_corporate/client_decimal', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_decimal', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_decimal', array('client_id')),
        array('client_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_decimal', array('client_id', 'attribute_id', 'value')),
        array('client_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_decimal', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('jle_corporate/client_decimal', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity Decimal');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/client_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Client Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'jle_corporate/client_int',
            array('client_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('client_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('jle_corporate/client_int', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_int', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_int', array('client_id')),
        array('client_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_int', array('client_id', 'attribute_id', 'value')),
        array('client_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_int', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_int', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_int', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity Int');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/client_text'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_text'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Client Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false,
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'jle_corporate/client_text',
            array('client_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('client_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('jle_corporate/client_text', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_text', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_text', array('client_id')),
        array('client_id'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_text', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_text', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('jle_corporate/client_text', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity Text');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/client_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/client_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Id')
    ->addColumn('client_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Client Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'jle_corporate/client_varchar',
            array('client_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('client_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('jle_corporate/client_varchar', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_varchar', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_varchar', array('client_id')),
        array('client_id'))
    ->addIndex($installer->getIdxName('jle_corporate/client_varchar', array('client_id', 'attribute_id', 'value')),
        array('client_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('jle_corporate/client_varchar', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/client_varchar', 'client_id', 'jle_corporate/client', 'client_id'),
        'client_id', $installer->getTable('jle_corporate/client'), 'client_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('jle_corporate/client_varchar', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Entity Varchar');
$installer->getConnection()->createTable($table);


/**
 * Create table 'jle_corporate/eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Visible')
    ->addColumn('input_filter', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Input Filter')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Multiline Count')
    ->addColumn('validate_rules', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Validate Rules')
    ->addColumn('is_system', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is System')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addColumn('data_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Data Model')
    ->addForeignKey($installer->getFkName('jle_corporate/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Eav Attribute');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/eav_attribute_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/eav_attribute_website'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Visible')
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Is Required')
    ->addColumn('default_value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default Value')
    ->addColumn('multiline_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Multiline Count')
    ->addIndex($installer->getIdxName('jle_corporate/eav_attribute_website', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName('jle_corporate/eav_attribute_website', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('jle_corporate/eav_attribute_website', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Client Eav Attribute Website');
$installer->getConnection()->createTable($table);

/**
 * Create table 'jle_corporate/form_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('jle_corporate/form_attribute'))
    ->addColumn('form_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Form Code')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addIndex($installer->getIdxName('jle_corporate/form_attribute', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('jle_corporate/form_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Corporate Form Attribute');
$installer->getConnection()->createTable($table);


$installer->endSetup();
$installer->installEntities();
$installer->installClientForms();