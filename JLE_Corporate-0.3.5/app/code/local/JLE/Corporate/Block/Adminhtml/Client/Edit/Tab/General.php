<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('client_');
        $form->setFieldNameSuffix('client');
        
		$client = Mage::registry('current_client');
		
		/** @var $customerForm Mage_Customer_Model_Form */
        $clientForm = Mage::getModel('jle_corporate/form');
        $clientForm->setEntity($client)
            ->setFormCode('adminhtml_client')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('jle_corporate')->__('Client Information')
        ));

        $attributes = $clientForm->getAttributes();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute->setFrontendLabel(Mage::helper('jle_corporate')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }
		unset($attributes['discount_code']);
		unset($attributes['attribute_set_id']);
		unset($attributes['created_at']);
		unset($attributes['updated_at']);
		$this->_addElementTypes($fieldset);

		$this->_setFieldset($attributes, $fieldset);

		$form_data = $this->_getFormData();
		if (!$form_data) {
			$form_data = array('is_active'=>1);
		}		
		$form->setValues($form_data);

		$this->setForm($form);
		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
		if ($client = Mage::registry('current_client')) {
			return $client->getData();
		}
	
		return array();
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
