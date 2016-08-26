<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_Addresses extends Mage_Adminhtml_Block_Widget_Form
{
	
	protected function _prepareForm()
	{
		$store = Mage::app()->getStore(0);
		$client = Mage::registry('current_client');
        $addressModel = Mage::getModel('customer/address');
        $addressModel->setCountryId(Mage::helper('core')->getDefaultCountry($store));
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('adminhtml_customer_address')
            ->setEntity($addressModel)
            ->initDefaultValues();

		$form = new Varien_Data_Form();
		$form->setFieldNameSuffix('address');

		/*************Put in billing***********************/
		if ($client->getFixedBillingAddress()) {
	        $billingAttributes = $addressForm->getAttributes();
	        if(isset($billingAttributes['street'])) {
	            Mage::helper('adminhtml/addresses')
	                ->processStreetAttribute($billingAttributes['street']);
	        }
	        foreach ($billingAttributes as $attribute) {
	            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
	            $attribute->setAttributeCode('billing_' . $attribute->getAttributeCode());
	            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
	            $attribute->unsIsVisible();
//				if ($attribute->getIsRequired()) {
//					$attribute->setData('is_required', 0);
//					$attribute->setData('scope_is_required', 0);
//				}
	        }
	        $billingFieldset = $form->addFieldset('billing_address_fieldset', array(
	            'legend'    => Mage::helper('customer')->__("Billing Address"))
	        );
	
	        $this->_setFieldset($billingAttributes, $billingFieldset);
		}
		
		/*************Put in shipping***********************/
		if ($client->getFixedShippingAddress()) {
			$shippingAttributes = $addressForm->getAttributes();
	        if(isset($shippingAttributes['street'])) {
	            Mage::helper('adminhtml/addresses')
	                ->processStreetAttribute($shippingAttributes['street']);
	        }
	        foreach ($shippingAttributes as $attribute) {
	            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
	            $attribute->setAttributeCode(str_replace('billing_', 'shipping_', $attribute->getAttributeCode()));
	            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
	            $attribute->unsIsVisible();
	        }
			
	        $shippingFieldset = $form->addFieldset('shipping_address_fieldset', array(
	            'legend'    => Mage::helper('customer')->__("Shipping Address"))
	        );
			
			$this->_setFieldset($shippingAttributes, $shippingFieldset);
		}
			
//        $addressCollection = $customer->getAddresses();
//        $this->assign('customer', $customer);
//        $this->assign('addressCollection', $addressCollection);
        $form->setValues($client->getAddressesData());
        $this->setForm($form);

		return $this;
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
	
}
