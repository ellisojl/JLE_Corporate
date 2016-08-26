<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$entity = $installer->getEntityTypeId('customer');
 
$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'fixed_pon', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Fixed PO#',
    'backend'       => '',
    'visible'       => false,
    'required'      => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
   'use_in_forms'	=> array('adminhtml_customer', 'customer_account_create')
));

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'fixed_pon',
 '90'  //sort_order
);

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "fixed_pon");
$attribute->setData("used_in_forms", array('adminhtml_customer'))
        ->setData("is_used_for_customer_segment", false)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 0)
		->setData("adminhtml_only", 1)
        ->setData("sort_order", 100);
$attribute->save();

$installer->endSetup();