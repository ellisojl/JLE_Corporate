<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit  extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_controller = 'adminhtml_client';
		$this->_blockGroup = 'jle_corporate';
		$this->_headerText = $this->_getHeaderText();
		
		$this->_addButton('new_customer', array(
			'label'     => Mage::helper('jle_corporate')->__('New Customer'),
			'onclick' => "window.open('". Mage::getUrl("adminhtml/customer/edit") ."')",
			'class' => 'new'
		));
		$this->_addButton('delete', array(
			'label'     => Mage::helper('jle_corporate')->__('Delete'),
			'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?')
				. '\', \''.$this->getDeleteUrl().'\')',			
			'class' => 'delete'
		));
		$this->_addButton('save_and_edit_button', array(
			'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
			'onclick'   => 'editForm.submit(\''.$this->getSaveAndContinueUrl().'\')',
			'class' => 'save'
		));
	}
	
	/**
	 * Retrieve the URL used for the save and continue link
	 * This is the same URL with the back parameter added
	 *
	 * @return string
	 */
	public function getSaveAndContinueUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current'   => true,
			'back'       => 'edit',
		));
	}

	/**
	 * Retrieve the URL used for the delete link
	 *
	 * @return string
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete', array(
			'_current'   => true,
		));
	}

    /**
     * Retrieve the header text
     * If landing page exists, use name
     *
     * @return string
     */
	protected function _getHeaderText()
	{
		if ($client = Mage::registry('current_client')) {
			if ($name = $client->getName()) {
				return $name;
			}
		}
		return $this->__('Edit Client');
	}
}
