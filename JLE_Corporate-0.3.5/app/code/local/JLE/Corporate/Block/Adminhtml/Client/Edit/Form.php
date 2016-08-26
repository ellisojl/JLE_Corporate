<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'jle_corporate';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'gov_client';
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(
			array(
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array('client_id' => $this->getRequest()->getParam('client_id'))),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
			)
		);
		$form->addField('in_client_customer', 'hidden', array('name' => 'client[in_client_customer]'));
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
	
	public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
	
	public function getCustomersJson() {
		$client = Mage::registry('current_client');
		$customers = array();
		if ($client->getId()) {
			$customers = $client->getCustomers();
		}
		$data = array();
		foreach($customers as $row) {
			$data[$row['customer_id']] = $row['is_supervisor'];
		}
		if (!empty($data)) {
            return Mage::helper('core')->jsonEncode($data);
        }
        return '{}';		
	}
		
}
