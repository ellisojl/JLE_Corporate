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
 * @category    JLE
 * @package     JLE_Corporate
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Client mysql4 abstract resource model
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */

class JLE_Corporate_Model_Client extends JLE_Corporate_Model_Abstract
{
	
	protected $_customers_table;
	protected $_productPrices_table;
	protected $_conn_read;
	protected $_conn_write;
	
	protected $_customers;
	protected $_productPrices;
	protected $_billingAddress;
	protected $_shippingAddress;
	
	/**
     * Assoc array of client attributes
     *
     * @var array
     */
    protected $_attributes;
	
    /**
     * Local constructor
     */
    protected function _construct() 
    {
        $this->_init('jle_corporate/client');
		$this->_customers_table = Mage::getSingleton('core/resource')->getTableName('jle_corporate/client_customer');
		$this->_productPrices_table = Mage::getSingleton('core/resource')->getTableName('jle_corporate/client_product');
		$this->_conn_read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$this->_conn_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }
	
	/**
     * Get customer created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }
        return null;
    }
	
	public function getCustomers() {
		if (!$this->getId()) {
			return array();
		}
		if (!$this->_customers) {
			$this->_customers = $this->_conn_read->fetchAll('SELECT * FROM ' . $this->_customers_table . ' WHERE client_id = ' . $this->getId());
		}
		return $this->_customers;
//        $customers = Mage::getResourceModel('jle_corporate/client_customer')
//            ->setClientId($this->getId())->getCustomers();
//        return $customers;
	}
	
	public function getCustomersArray() {
		$customers = $this->getCustomers();
		$data = array();
		foreach ($customers as $customer) {
			$data[$customer['customer_id']] = $customer['is_supervisor'];
		}
		return $data;
	}
	
	public function getSupervisors() {
		$customers = $this->getCustomers();
		$superviors = array();
		foreach ($customers as $customer) {
			if ($customer['is_supervisor']) {
				$superviors[] = $customer['customer_id'];
			}
		}		
		return $superviors;
	}
	
	public function saveCustomers($data) {
		$table = $this->_customers_table;
		$query = "DELETE FROM {$table} WHERE client_id = " . $this->getId();
		$this->_conn_write->query($query);
		foreach($data as $customer_id=>$is_supervisor) {
			$query = "INSERT INTO {$table} (client_id, customer_id, is_supervisor) VALUES (".$this->getId().", ".$customer_id.", ".$is_supervisor.")";
			$this->_conn_write->query($query);
		}		
	}
	
	public function getProductsArray() {
		$products = $this->getProductPrices();
		$data = array();
		foreach ($products as $product) {
			$data[$product['product_id']] = 1;
		}
		return $data;
	}
	
	public function getProductPrices() {
		if (!$this->getId()) {
			return array();
		}
		if (!$this->_productPrices) {
			$data = $this->_conn_read->fetchAll('SELECT * FROM ' . $this->_productPrices_table . ' WHERE client_id = ' . $this->getId());
			$this->_productPrices = array();
			foreach ($data as $row) {
				$this->_productPrices[$row['product_id']] = $row;
			}
		}
		return $this->_productPrices;
	}
	
	public function getPricesForProduct($productId) {
		if (!$this->_productPrices) {
			$this->getProductPrices();
		}
		if (array_key_exists($productId, $this->_productPrices)) {
			return $this->_productPrices[$productId];
		}
		return array();
	}
	
	public function saveProductPrice($data) {
		$table = $this->_productPrices_table;
		$query = "DELETE FROM {$table} WHERE client_id = " . $this->getId() . " AND product_id = " . $data['product_id'];
		$this->_conn_write->query($query);
		$query = "INSERT INTO {$table} (client_id, product_id, product_sku, product_price, product_cost) " 
		. "VALUES (".$this->getId().", ".$data['product_id'].", '".$data['product_sku']."', '".$data['product_price']."', '".$data['product_cost']."')";
		$this->_conn_write->query($query);
	}
	
	public function deleteProductPrice($productId) {
		$table = $this->_productPrices_table;
		$query = "DELETE FROM {$table} WHERE client_id = " . $this->getId() . " AND product_id = " . $productId;
		$this->_conn_write->query($query);
	}
	
	public function purgeProductPrices() {
		$table = $this->_productPrices_table;
		$query = "DELETE FROM {$table} WHERE client_id = " . $this->getId();
		$this->_conn_write->query($query);
	}
	
    /**
     * Retrieve all client attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Get client attribute model object
     *
     * @param   string $attributeCode
     * @return  JLE_Corporate_Model_Attribute | null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }
    
	public function getBillingAddress() {
		if (!$this->_billingAddress) {
			$this->_billingAddress = Mage::getModel('customer/address');
		}
		if ($this->getBillingAddressData()) {
			$data = json_decode($this->getBillingAddressData(), true);
			$this->_billingAddress->setData($data);
		}
		return $this->_billingAddress;
	}
	
	public function addBillingAddress($address) {
		$this->_billingAddress = $address;
		$this->setBillingAddressData(json_encode($address->getData()));
	}

	public function getShippingAddress() {
		if (!$this->_shippingAddress) {
			$this->_shippingAddress = Mage::getModel('customer/address');
		}
		if ($this->getShippingAddressData()) {
			$data = json_decode($this->getShippingAddressData(), true);
			$this->_shippingAddress->setData($data);
		}
		return $this->_shippingAddress;
	}
	
	public function addShippingAddress($address) {
		$this->_shippingAddress = $address;
		$this->setShippingAddressData(json_encode($address->getData()));
	}
	
    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }
	
    /**
     * Processing object before save data
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        return $this;
    }
	
	
}