<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_BillingAddress extends Mage_Adminhtml_Block_Widget_Form
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
		$form->setFieldNameSuffix('billing');

		/*************Put in billing***********************/
        $attributes = $addressForm->getAttributes();
        if(isset($attributes['street'])) {
            Mage::helper('adminhtml/addresses')
                ->processStreetAttribute($attributes['street']);
        }
        $fieldset = $form->addFieldset('billing_address_fieldset', array(
            'legend'    => Mage::helper('customer')->__("Billing Address"))
        );		
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }

        $this->_setFieldset($attributes, $fieldset);
		$address = $client->getBillingAddress();
        $form->addValues($address->getData());
        $this->setForm($form);

		return $this;
	}
		
}
