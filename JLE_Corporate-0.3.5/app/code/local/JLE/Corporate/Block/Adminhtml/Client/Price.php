<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Price  extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_objectId = 'client_price';
		$this->_controller = 'adminhtml_client';
		parent::__construct();
		$this->_blockGroup = 'jle_corporate';
		$this->_headerText = $this->_getHeaderText();
		
		$this->_addButton('delete', array(
			'label'     => Mage::helper('jle_corporate')->__('Delete'),
			'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?')
				. '\', \''.$this->getDeleteUrl().'\')',			
			'class' => 'delete'
		));
	}

	/**
	 * Retrieve the URL used for the delete link
	 *
	 * @return string
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/deleteprice', array(
			'_current'   => true,
		));
	}

    /**
     * Retrieve the header text
     * If client exists, use name
     *
     * @return string
     */
	protected function _getHeaderText()
	{
		$client = Mage::registry('current_client');
		return $this->__('Edit Price for ' . $client->getName());
	}

}