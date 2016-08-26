<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * LandingPage feed module setup
 *
 * @category    JLE
 * @package     JLE_Corporate
 */
class JLE_Corporate_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
	
    /**
     * Prepare client attribute values to save in additional table
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'is_visible'                => $this->_getValue($attr, 'visible', 1),
            'is_system'                 => $this->_getValue($attr, 'system', 1),
            'input_filter'              => $this->_getValue($attr, 'input_filter', null),
            'multiline_count'           => $this->_getValue($attr, 'multiline_count', 0),
            'validate_rules'            => $this->_getValue($attr, 'validate_rules', null),
            'data_model'                => $this->_getValue($attr, 'data', null),
            'sort_order'                => $this->_getValue($attr, 'position', 0)
        ));

        return $data;
    }

    /**
     * Add client attributes to client forms
     *
     * @return void
     */
    public function installClientForms()
    {
        $client           = (int)$this->getEntityTypeId('gov_client');

        $attributeIds       = array();
        $select = $this->getConnection()->select()
            ->from(
                array('ea' => $this->getTable('eav/attribute')),
                array('entity_type_id', 'attribute_code', 'attribute_id'))
            ->where('ea.entity_type_id IN(?)', array($client));
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = array();
        $entities   = $this->getDefaultEntities();
		$attributes = $entities['gov_client']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$client][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array(
                    'adminhtml_client',
                );
                foreach ($usedInForms as $formCode) {
                    $data[] = array(
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId
                    );
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('jle_corporate/form_attribute'), $data);
        }
    }

    /**
     * Add client attributes to client forms
     *
     * @return void
     */
    public function addCouponToClientForms()
    {
        $client           = (int)$this->getEntityTypeId('gov_client');

		$attributes   = array('discount_code'		=> array(
                        'type'               => 'varchar',
                        'label'              => 'Auto-applied Discount Code',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 140,
                        'position'           => 140,
                    ));
		foreach ($attributes as $attributeCode => $attribute) {
			$this->addAttribute($client, 'discount_code', $attributes['discount_code']);
		}
        $attributeIds       = array();
        $select = $this->getConnection()->select()
            ->from(
                array('ea' => $this->getTable('eav/attribute')),
                array('entity_type_id', 'attribute_code', 'attribute_id'))
            ->where('ea.entity_type_id IN(?)', array($client));
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = array();
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$client][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array(
                    'adminhtml_client',
                );
                foreach ($usedInForms as $formCode) {
                    $data[] = array(
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId
                    );
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('jle_corporate/form_attribute'), $data);
        }
    }

    /**
     * Add client attributes to client forms
     *
     * @return void
     */
    public function addVersion033Fields()
    {
		$attributes   = array('attribute_set_id'      => array(
                        'type'               => 'static',
                        'label'              => 'Attribute Set',
                        'input'              => 'hidden',
                        'required'			 => false,
                        'is_visible'		 => 0,
                        'sort_order'         => 6,
                        'position'           => 6,
                        'adminhtml_only'     => 1,
                        'admin_client'	     => 1,
                    ),
                    'created_at'      => array(
                        'type'               => 'static',
                        'label'              => 'Created Date',
                        'input'              => 'hidden',
                        'is_visible'		 => 0,
                        'sort_order'         => 7,
                        'position'           => 7,
                        'adminhtml_only'     => 1,
                        'admin_client'	     => 1,
                    ),
                    'updated_at'      => array(
                        'type'               => 'static',
                        'label'              => 'Updated Date',
                        'input'              => 'hidden',
                        'is_visible'		 => 0,
                        'sort_order'         => 8,
                        'position'           => 8,
                        'adminhtml_only'     => 1,
                        'admin_client'	     => 1,
                    ),
					'fixed_billing_address'    => array(
                        'type'               => 'int',
                        'label'              => 'Fixed Billing Address',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 125,
                        'position'           => 125,
                    ),
					'fixed_shipping_address'    => array(
                        'type'               => 'int',
                        'label'              => 'Fixed Shipping Address',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 126,
                        'position'           => 126,
                    ),
					'billing_address_data'    => array(
                        'type'               => 'text',
                        'label'              => 'Billing Address Data',
                        'input'              => 'hidden',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 99,
                        'position'           => 99,
                    ),
					'shipping_address_data'    => array(
                        'type'               => 'text',
                        'label'              => 'Shipping Address Data',
                        'input'              => 'hidden',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 99,
                        'position'           => 99,
                    ));
		$this->addAttributes($attributes);
    }

    /**
     * Add client attributes to client forms
     *
     * @return void
     */
    public function addVersion034Fields()
    {
		$attributes   = array('orders_need_approval'    => array(
                        'type'               => 'int',
                        'label'              => 'Orders require internal approval',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 127,
                        'position'           => 127,
                    ),
					'orders_need_super_approval'    => array(
                        'type'               => 'int',
                        'label'              => 'Orders require supervisor approval',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 128,
                        'position'           => 128,
                    ));
		$this->addAttributes($attributes);
    }

    public function addVersion035Fields()
    {
		$attributes   = array('is_tax_exempt'    => array(
                        'type'               => 'int',
                        'label'              => 'Tax Exempt',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 95,
                        'position'           => 95,
                    ));
		$this->addAttributes($attributes);
    }

	protected function addAttributes($attributes) {
		$client           = (int)$this->getEntityTypeId('gov_client');
		foreach ($attributes as $attributeCode => $attribute) {
			$this->addAttribute($client, $attributeCode, $attribute);
		}
        $attributeIds       = array();
        $select = $this->getConnection()->select()
            ->from(
                array('ea' => $this->getTable('eav/attribute')),
                array('entity_type_id', 'attribute_code', 'attribute_id'))
            ->where('ea.entity_type_id IN(?)', array($client));
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = array();
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$client][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array(
                    'adminhtml_client',
                );
                foreach ($usedInForms as $formCode) {
                    $data[] = array(
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId
                    );
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('jle_corporate/form_attribute'), $data);
        }
	}


    /**
     * Retreive default entities: client
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = array(
            'gov_client'                       => array(
                'entity_model'                   => 'jle_corporate/client',
                'attribute_model'                => 'jle_corporate/attribute',
                'table'                          => 'jle_corporate/client',
                'increment_model'                => 'eav/entity_increment_numeric',
                'additional_attribute_table'     => 'jle_corporate/eav_attribute',
                'entity_attribute_collection'    => 'jle_corporate/attribute_collection',
                'attributes'                     => array(
                    'is_active'          => array(
                        'type'				=> 'static',
                        'label'				=> 'Is Active',
                        'input'				=> 'select',
                        'source'			=> 'eav/entity_attribute_source_boolean',
                        'is_visible'		=> 0,
                        'sort_order'		=> 1,
                        'position'          => 1,
                        'admin_client'	    => 1
                    ),
                    'name'              => array(
                        'type'               => 'static',
                        'label'              => 'Name',
                        'input'              => 'text',
                        'sort_order'         => 2,
                        'position'           => 2,
                        'admin_client'	     => 1
                    ),                
                    'code'  	             => array(
                        'type'               => 'static',
                        'label'              => 'Code',
                        'input'              => 'text',
                        'sort_order'         => 3,
                        'position'           => 3,
                        'admin_client'       => 1
                    ),                
                    'website_id'         => array(
                        'type'               => 'static',
                        'label'              => 'Associate to Website',
                        'input'              => 'select',
                        'source'             => 'customer/customer_attribute_source_website',
                        'backend'            => 'customer/customer_attribute_backend_website',
                        'is_visible'		 => 0,
                        'sort_order'         => 4,
                        'position'           => 4,
                        'adminhtml_only'     => 1,
                    ),
                    'customer_group_id'      => array(
                        'type'               => 'static',
                        'label'              => 'Customer Group',
                        'input'              => 'select',
                        'source'             => 'customer/customer_attribute_source_group',
                        'is_visible'		 => 0,
                        'sort_order'         => 5,
                        'position'           => 5,
                        'adminhtml_only'     => 1,
                        'admin_client'	     => 1,
                    ),
                    'expire_date'         => array(
                        'type'               => 'datetime',
                        'label'              => 'Account Expire Date',
                        'input'              => 'date',
                        'frontend'           => 'eav/entity_attribute_frontend_datetime',
                        'backend'            => 'eav/entity_attribute_backend_datetime',
                        'required'           => false,
                        'sort_order'         => 10,
                        'is_visible'		 => 0,
                        'input_filter'       => 'date',
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:4:"date";}',
                        'position'           => 10,
                    ),
                    'free_shipping'    => array(
                        'type'               => 'int',
                        'label'              => 'Free Shipping',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 20,
                        'position'           => 20,
                    ),
                    'primary_name'             => array(
                        'type'               => 'varchar',
                        'label'              => 'Primary Contact Name',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 30,
                        'position'           => 30,
                    ),
                    'primary_phone'          => array(
                        'type'               => 'varchar',
                        'label'              => 'Primary Contact Phone',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 40,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 40,
                    ),                    
                    'primary_ext'          => array(
                        'type'               => 'varchar',
                        'label'              => 'Primary Contact Ext',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 50,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 50,
                    ),                    
                    'primary_email'              => array(
                        'type'               => 'varchar',
                        'label'              => 'Primary Contact Email',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 60,
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:5:"email";}',
                        'position'           => 60,
                    ),
                    'other_email'              => array(
                        'type'               => 'varchar',
                        'label'              => 'Other Account Handler Emails',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 70,
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:5:"email";}',
                        'position'           => 70,
                        'comments'			 => "Use comma to separate",
                    ),
                    'master_email'              => array(
                        'type'               => 'varchar',
                        'label'              => 'Master Email Associated in Nav',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 80,
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:5:"email";}',
                        'position'           => 80,
                    ),
                    'notify_master'    => array(
                        'type'               => 'int',
                        'label'              => 'Send Notification to Master Email',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_boolean',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 90,
                        'position'           => 90,
                    ),
                    'tax_extempt_no'             => array(
                        'type'               => 'varchar',
                        'label'              => 'Tax Exempt ID #',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 100,
                        'validate_rules'     => 'a:1:{s:15:"max_text_length";i:255;}',
                        'position'           => 100,
                    ),
                    'custom_tax_rate'        => array(
                        'type'               => 'decimal',
                        'label'              => 'Custom Tax Rate',
                        'input'              => 'text',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 110,
                        'position'           => 110,
                    ),
                    'fixed_po'             => array(
                        'type'               => 'varchar',
                        'label'              => 'Use Fixed PO#',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 120,
                        'position'           => 120,
                    ),
                    'notes'             => array(
                        'type'               => 'text',
                        'label'              => 'Notes',
                        'input'              => 'textarea',
                        'is_visible'		 => 0,
                        'required'           => false,
                        'sort_order'         => 130,
                        'position'           => 130,
                    ),
                )
            )
        );
        return $entities;
    }
}

