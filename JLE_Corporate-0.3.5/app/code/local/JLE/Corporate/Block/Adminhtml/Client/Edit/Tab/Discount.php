<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_Discount extends Mage_Adminhtml_Block_Widget_Form
{
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('client_');
        $form->setFieldNameSuffix('client');
        
		$client = Mage::registry('current_client');
		
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('jle_corporate')->__('Client Discounts')
        ));
		
		$fieldset->addField('note', 'note', array(
            'label'		=> Mage::helper('jle_corporate')->__('Flat File'),
            'text'		=> '<a href="'.$this->getSkinUrl('media/products_price.csv').'" target="_blank">' . Mage::helper('jle_corporate')->__('Click here to download a template and file guide') . "</a>"
        ));
		
        $fieldset->addField('flat_file', 'file', array(
            'name'     => 'flat_file',
            'label'    => Mage::helper('jle_corporate')->__('Upload Flat File'),
            'required' => false,
            'disabled' => false
        ));

        $fieldset->addField('purge_discounts', 'checkbox', array(
            'name'     => 'purge_discounts',
            'label'    => Mage::helper('jle_corporate')->__('Purge Existing Discounts'),
            'required' => false,
            'disabled' => false
        ));
		
		$fieldset->addField('discount_code', 'select', array(
			'name'		=> 'discount_code',
            'label'		=> Mage::helper('jle_corporate')->__('Auto-applied Discount Code'),
            'values'	=> $this->getDiscountCodes()
            
//            array('somecoupon'=>'somecoupon', '12345'=>'12345', 'thatcoupon'=>'thatcoupon'),
		));
		
		$this->_addElementTypes($fieldset);
		
		$this->_setFieldset(array(), $fieldset);
		
		if ($client->getId()) {
			$form->setValues(array('discount_code'=>$client->getDiscountCode()));
		}
		$this->setForm($form);
		
		return parent::_prepareForm();
	}
		
    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('jle_corporate/adminhtml_client_form_element_file'),
            'image'     => Mage::getConfig()->getBlockClassName('jle_corporate/adminhtml_client_form_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('jle_corporate/adminhtml_client_form_element_boolean'),
        );
    }
	
	protected function getDiscountCodes() {		
		$rules = Mage::getResourceModel('salesrule/rule_collection')->load();
		$discountCodes = array();
		$client = Mage::registry('current_client');
		foreach ($rules as $rule) {
		    if ($rule->getIsActive() && $rule->getCode()) {
		    	$rule = Mage::getModel('salesrule/rule')->load($rule->getId());
				if (in_array($client->getCustomerGroupId(), $rule->getCustomerGroupIds())) {
					$discountCodes[$rule->getCouponCode()] = $rule->getCouponCode();
				}
		    }
		}
		return $discountCodes;
	}

}
