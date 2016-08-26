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
 * DISCLAIMERMage::app()->getStore(0)
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
 * Helper
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */
class JLE_Corporate_Helper_Data extends Mage_Core_Helper_Abstract {

    const ORDER_STATUS_GOV             =       'review_gov';
    const ORDER_STATUS_GOV_LABEL       =       'Gov Review';
    const ORDER_STATE_GOV              =       'holded';
	
    const ORDER_STATUS_SUPER_GOV       =       'review_super_gov';
    const ORDER_STATUS_SUPER_GOV_LABEL =       'Supervisor Review';

	protected $_customers_table;
	protected $_conn_read;
	protected $_conn_write;
	
    /**
     * Local constructor
     */
    protected function _init() 
    {
		$this->_customers_table = Mage::getSingleton('core/resource')->getTableName('jle_corporate/client_customer');
		$this->_client_table = Mage::getSingleton('core/resource')->getTableName('jle_corporate/client');
		$this->_conn_read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$this->_conn_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }
	
	public function getCustomerTable() {
		if (!$this->_customers_table) {
			$this->_init();
		}
		return $this->_customers_table;
	}

	public function getClientTable() {
		if (!$this->_client_table) {
			$this->_init();
		}
		return $this->_client_table;
	}
   	
	public function getClientDataForCustomer($customerId) {
		if (!$this->_customers_table) {
			$this->_init();
		}
		$clients = $this->_conn_read->fetchAll('SELECT * FROM ' . $this->_customers_table . ' WHERE customer_id = ' . $customerId);
		foreach($clients as $data) {
			$client = Mage::getModel('jle_corporate/client')->load($data['client_id']);
			if ($client->getIsActive()) {
				return array('client'=>$client, 'is_supervisor'=>$data['is_supervisor']);
			}
		}
		return false;
	}
	
	public function assignClientToUserSession($client) {
		Mage::getSingleton('customer/session')->setClientId($client->getId());
	}

	public function assignUserAsSupervisorSession($client) {
		Mage::getSingleton('customer/session')->setIsClientSupervisor($client->getId());
	}
	
	public function unassignCustomerSession() {
		Mage::getSingleton('customer/session')->unsetData('client_id');
		Mage::getSingleton('customer/session')->unsetData('is_client_supervisor');
	}
	
	public function getIsGovUser() {
		if (Mage::getSingleton('customer/session')->isLoggedIn() 
				&& Mage::getSingleton('customer/session')->getClientId()) {
			return true;
		}
		return false;
	}
	
	public function getIsSupervisor() {
		if (Mage::getSingleton('customer/session')->isLoggedIn() 
				&& Mage::getSingleton('customer/session')->getIsClientSupervisor()) {
			return true;
		}
		return false;
	}
	
	public function getClientId() {
		return Mage::getSingleton('customer/session')->getClientId();
	} 

	public function getClientFromSession() {
		if (Mage::getSingleton('customer/session')->getClientId()) {
			return Mage::getModel('jle_corporate/client')->load($this->getClientId());
		}
		return "";
	}
	
	public function isClientLive($client) {
		if ($client->getIsActive() && (!$client->getExpireDate() || (strtotime($client->getExpireDate()) > time()))) {
			return true;
		}
		return false;
	}

	public function getGovGroupId() {
		return 4;
	}
	
	public function getOrderNotificationEmails() {
		return Mage::getStoreConfig('jle_corporate/general/order_notification_emails');
	}
	
	public function sendOrderNotifiaction($emails, $order, $client, $type = 'customer', $name = 'Gov Rep') {

		$storeId = $order->getStore()->getId();
		$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($storeId);
        $paymentBlockHtml = $paymentBlock->toHtml();
		$mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
Mage::log('in the top of send order not, emails: ' . $emails);
		$array = array_map('trim',explode(',', $emails));
		foreach ($array as $email) {
        	$emailInfo->addTo($email, $name);
		}
		$mailer->addEmailInfo($emailInfo);
		if ($client->getPrimaryEmail()) {
		$sender = Array('name'  => $client->getPrimaryName(),
                  		'email' => $client->getPrimaryEmail());
		}
		if ($client->getPrimaryEmail()) {
			$mailer->setSender($sender);
		} else {
			$mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
		}
        $mailer->setStoreId($storeId);
		if ($type == 'supervisor') {
			$templateId = 'jle_corporate_order_approval_template';
		} else {
			$templateId = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId);			
		}
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml,
                'client' 	   => $client
            )
        );
Mage::log('about to send it');
        $mailer->send();
		
	}

	public function canApproveOrder($order) {
		$client = $this->getClientForOrder($order);
		return ($this->getIsSupervisor() && $this->getIsSupervisorForClient($client) 
				&& ($order->getStatus() == JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV));
	}

	public function canViewOrder($order) {
		$client = $this->getClientForOrder($order);
		return ($this->getIsSupervisor() && $this->getIsSupervisorForClient($client));
	}
	
	public function getClientForOrder($order) {
		$data = $this->getClientDataForCustomer($order->getCustomerId());
		return $data['client'];
	}
	
	public function getIsSupervisorForClient($client) {
		$data = $client->getCustomersArray();
		$customer_id = Mage::getSingleton('customer/session')->getId();
		return $data[$customer_id];
	}
	
	public function getClientProductPrice($productId) {
		if ($clientId = $this->getClientId()) {
			$client = Mage::getModel('jle_corporate/client')->load($clientId);
			$price = $client->getPricesForProduct($productId);
			if ($price) {
				return $price['product_price'];
			}
			if ($client->getDiscountCode()) {
				$product = Mage::getModel('catalog/product')->load($productId);
				$estimate = Mage::getSingleton('salesrule/estimate');
				$estimate->clearProducts();
				$estimate->addProduct($product);
				$estimate->setCouponCode($client->getDiscountCode());
				try {
		            $estimate->estimate();
		        } catch (Mage_Core_Exception $e) {
		            Mage::getSingleton('catalog/session')->addError($e->getMessage());
		        } catch (Exception $e) {
		            Mage::logException($e);
		            Mage::getSingleton('catalog/session')->addError(
		                Mage::helper('salesrule')->__('There was an error calculating price')
		            );
		        }
				$savings = $estimate->getSavingsForProduct($productId);
				if ($savings['savings']) {
					return $savings['price'];
				}
			}
					
		}
		return '';
	}
	
}
?>