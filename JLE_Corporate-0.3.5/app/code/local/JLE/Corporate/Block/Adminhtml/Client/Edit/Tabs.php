<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('client_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Client Information'));
	}
	
	protected function _beforeToHtml()
	{
		$client = Mage::registry('current_client');
		$this->addTab('general',
			array(
				'label' => $this->__('General'),
				'title' => $this->__('General'),
				'content' => $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_general')->toHtml(),
			)
		);
		if ($client->getId()) {
			$this->addTab('customers',
				array(
					'label' => $this->__('Users'),
					'title' => $this->__('Users'),
					'content' => $this->getLayout()->createBlock(
							'jle_corporate/adminhtml_client_edit_tab_customers',
							'client.customer.grid'
					)->toHtml(),
				)
			);
			if ($client->getFixedBillingAddress()) {
				$this->addTab('billingAddress',
					array(
						'label' => $this->__('Fixed Billing Address'),
						'title' => $this->__('Fixed Billing Address'),
						'content' => $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_billingAddress')->toHtml(),
					)
				);
			}
			if ($client->getFixedShippingAddress()) {
				$this->addTab('shippingAddress',
					array(
						'label' => $this->__('Fixed Shipping Address'),
						'title' => $this->__('Fixed Shipping Address'),
						'content' => $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_shippingAddress')->toHtml(),
					)
				);
			}
			$this->addTab('discount',
				array(
					'label' => $this->__('Discount'),
					'title' => $this->__('Discount'),
					'content' => $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_discount')->toHtml(),
				)
			);
	
			$this->addTab('products',
				array(
					'label' => $this->__('Products - Flat'),
					'title' => $this->__('Products - Flat'),
					'content' => $this->getLayout()->createBlock(
							'jle_corporate/adminhtml_client_edit_tab_products',
							'client.product.grid'
					)->toHtml(),
				)
			);
		}
		
		$this->_activeTab = 'general';
		
		return parent::_beforeToHtml();
	}
	
}
