<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Client_Order_Account extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns if this user is the supervisor of the client
     *
     * @return boolean
     */
	public function isSupervisor()
	{
		return Mage::helper('jle_corporate')->getIsSupervisor();
		
	}

    /**
     * Returns if this user is in a client
     *
     * @return boolean
     */
	public function isGovUser()
	{
		return Mage::helper('jle_corporate')->getIsGovUser();
		
	}
	
	/**
     * Returns client object
     *
     * @return client
     */
	public function getClient()
	{
		$clientId = Mage::helper('jle_corporate')->getClientId();
		if ($clientId) {
			return Mage::getModel('jle_corporate/client')->load($clientId);
		}
		return "";
	}
	
    public function getViewUrl($order)
    {
        return $this->getUrl('corporate/order/view', array('order_id' => $order->getId()));
    }

    public function getApproveUrl($order)
    {
        return $this->getUrl('corporate/order/approve', array('order_id' => $order->getId()));
    }

    public function getCancelUrl($order)
    {
        return $this->getUrl('corporate/order/cancel', array('order_id' => $order->getId()));
    }
	
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }	
	
	public function getOrders() {
		$customerTable = Mage::helper('jle_corporate')->getCustomerTable();
		$clientTable = Mage::helper('jle_corporate')->getClientTable();
		$clientId = Mage::helper('jle_corporate')->getClientId();
        //TODO: add full name logic
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
//            ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
			->addAttributeToFilter('status', array('in' => JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV))
            ->addAttributeToSort('created_at', 'desc');
		$orders->getSelect()
			->join($customerTable, 'main_table.customer_id = '.$customerTable.'.customer_id', 'client_id')
			->join(array('client'=>$clientTable), $customerTable.'.client_id = '.'client.client_id', array('client_name'=>'name'))
		;
		$orders->setPageSize('5')
            ->load()
        ;
        return $orders;		
	}

	public function getAllOrders() {
		$customerTable = Mage::helper('jle_corporate')->getCustomerTable();
		$clientTable = Mage::helper('jle_corporate')->getClientTable();
		$clientId = Mage::helper('jle_corporate')->getClientId();
        //TODO: add full name logic
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
//            ->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addAttributeToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
//			->addAttributeToFilter('status', array('in' => JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV))
            ->addAttributeToSort('created_at', 'desc');
		$orders->getSelect()
			->join($customerTable, 'main_table.customer_id = '.$customerTable.'.customer_id', 'client_id')
			->join(array('client'=>$clientTable), $customerTable.'.client_id = '.'client.client_id', array('client_name'=>'name'))
		;
		$orders->load()
        ;
        return $orders;		
	}

	
}
