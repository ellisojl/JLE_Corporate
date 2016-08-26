<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->addVersion034Fields();
$installer->startSetup();

// Order status
// -- Get
$sStatus = JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV;
$sStatusLabel = JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV_LABEL;
// -- Insert
$installer->run("
DELETE FROM `{$this->getTable('sales_order_status')}` WHERE status = '{$sStatus}';
INSERT INTO `{$this->getTable('sales_order_status')}` VALUES (
    '{$sStatus}',
    '{$sStatusLabel}'
);
");

$installer->endSetup();
