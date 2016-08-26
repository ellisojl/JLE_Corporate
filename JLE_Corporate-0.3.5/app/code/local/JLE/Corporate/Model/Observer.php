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
 * Observer
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */

class JLE_Corporate_Model_Observer
{
	
	public function addGovCss(Varien_Event_Observer $observer) {
		/** @var $_block Mage_Core_Block_Abstract */
        /*Get block instance*/
        $_block = $observer->getBlock();
        /*get Block type*/
        $_type = $_block->getType();
       /*Check block type*/
       if ($_type == 'page/html_head') {
       		if (Mage::helper('jle_corporate')->getIsGovUser()) {
            	$_block->addCss('css/gov.css');
			}
        }
	}
	
	public function orderPlaced(Varien_Event_Observer $observer) {
		$order = $observer->getEvent()->getOrder();
		if ($data = Mage::helper('jle_corporate')->getClientDataForCustomer($order->getCustomerId())) {
			$client = $data['client'];
			if ($client->getOrdersNeedSuperApproval() && $client->getSupervisors()) {
				$order->setState(
		            JLE_Corporate_Helper_Data::ORDER_STATE_GOV,
		            JLE_Corporate_Helper_Data::ORDER_STATUS_SUPER_GOV,
		            Mage::helper('jle_corporate')->__('Order in review from Government Supervisor.'),
		            false
		        );
				
		        $order->save();
			} else if ($client->getOrdersNeedApproval()) {
				$order->setState(
		            JLE_Corporate_Helper_Data::ORDER_STATE_GOV,
		            JLE_Corporate_Helper_Data::ORDER_STATUS_GOV,
		            Mage::helper('jle_corporate')->__('Order in review from Government.'),
		            false
		        );
			}
			if ($client->getMasterEmail()) {
				$originalEmail = $order->getCustomerEmail();
				$order->setCustomerEmail($client->getMasterEmail());
				if ($originalEmail != $client->getMasterEmail()) {
					Mage::helper('jle_corporate')->sendOrderNotifiaction($originalEmail, $order, $client);
				}
			}
	        $order->save();				
			if ($emails = Mage::helper('jle_corporate')->getOrderNotificationEmails()) {
				Mage::helper('jle_corporate')->sendOrderNotifiaction($emails, $order, $client, 'supervisor');
			}
			if ($client->getOrderNeedSuperApproval() && $client->getSupervisors()) {
				Mage::helper('jle_corporate')->sendOrderNotifiaction(implode(", ", $client->getSupervisors()), $order, $client, 'supervisor');
			}
			if ($client->getNotifyMaster()) {
				Mage::helper('jle_corporate')->sendOrderNotifiaction($client->getPrimaryEmail(), $order, $client, 'supervisor');
			}
		}
	}

	public function customerLogin(Varien_Event_Observer $observer) {
		$customer = $observer->getCustomer();
		$this->_assignClient($customer);
		Mage::getSingleton('core/session')->unsetData('coupons');
		Mage::helper('checkout/cart')->getQuote()->setData('coupon_code','')->save();
	}

	public function customerLogout(Varien_Event_Observer $observer) {
		$customer = $observer->getCustomer();
		Mage::helper('jle_corporate')->unassignCustomerSession();
	}

	public function cartProductAdd(Varien_Event_Observer $observer) {
		// Get the quote item
		$item = $observer->getQuoteItem();
		// Ensure we have the parent item, if it has one
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		// Load the custom price
		$price = Mage::helper('jle_corporate')->getClientProductPrice($item->getProductId());
		if ($price) {
			// Set the custom price
			$item->setCustomPrice($price);
			$item->setOriginalCustomPrice($price);
			// Enable super mode on the product.
			$item->getProduct()->setIsSuperMode(true);
		}
	}
	
	/*Not needed after all, coupon prices are applied on add to cart*/
	protected function _applyDiscountToQuote() {
		if (Mage::helper('jle_corporate')->getIsGovUser() && ($clientId = Mage::helper('jle_corporate')->getClientId())) {
			$client = Mage::getModel('jle_corporate/client')->load($clientId);
			if ($client->getDiscountCode()) {
				$_quote = Mage::getSingleton('checkout/session')->getQuote();
				$_quote->setCouponCode($client->getDiscountCode());
//				$_quote= Mage::getModel('sales/quote')->setCouponCode($client->getDiscountCode());
				$_quote->save();			
			}
		}
	}

    protected function _assignClient($customer) {
		try {
			if ($customer && ($customer->getGroupId() == Mage::helper('jle_corporate')->getGovGroupId())) { //is type government
				$customerId = $customer->getEntityId();
				$data = Mage::helper('jle_corporate')->getClientDataForCustomer($customerId);
				if ($data) {
					$client = $data['client'];
					if (Mage::helper('jle_corporate')->isClientLive($client)) {
						Mage::helper('jle_corporate')->assignClientToUserSession($client);
						if ($data['is_supervisor']) {
							Mage::helper('jle_corporate')->assignUserAsSupervisorSession($client);
						}
					} else {
						Mage::log('client is expired');
					}
				}
			}
		} catch(Exception $e) {
            Mage::log('govt login error: '.$e->getMessage());
        }
    }

   /**
   * Sets free shipping if has free shipping selected
   * 
   * @param   Varien_Event_Observer $observer
   * @return  JLE_Corporate_Model_Observer
   */
	public function quoteCollectTotalsBefore($observer)
	{
		if (Mage::helper('jle_corporate')->getIsGovUser() && ($clientId = Mage::helper('jle_corporate')->getClientId())) {
			$client = Mage::getModel('jle_corporate/client')->load($clientId);
			if ($client->getFreeShipping()) {
				$quote = $observer->getEvent()->getQuote();
				$address = $quote->getShippingAddress();
				if ($client->getFreeShipping()) {
					$address->setFreeShipping(true);
					$address->setCollectShippingRates(true);
					$rates = $address->collectShippingRates()->getGroupedAllShippingRates();
			        foreach ($rates as $carrier) {
			            foreach ($carrier as $rate) {
			                $rate->setPrice(0);
			                $rate->save();
							break;
			            
			            }
			        }
					$address->save();
					
				}
			}
		}
		return $this;
	}
	
   /**
   * Removes taxes if gov client is tax extempt
   *
   * Sets free shipping if has free shipping selected
   * 
   * @param   Varien_Event_Observer $observer
   * @return  JLE_Corporate_Model_Observer
   */
	public function quoteCollectTotalsAfter($observer)
	{
		if (Mage::helper('jle_corporate')->getIsGovUser() && ($clientId = Mage::helper('jle_corporate')->getClientId())) {
			$client = Mage::getModel('jle_corporate/client')->load($clientId);
			if ($client->getIsTaxExempt()) {
				$quote = $observer->getEvent()->getQuote();
				foreach ($quote->getAllAddresses() as $address) {
					$tax = $address->getTaxAmount();
					$address->setSubtotal(0);
				    $address->setBaseSubtotal(0);					 
				    $address->setGrandTotal(0);
				    $address->setBaseGrandTotal(0);
					$address->collectTotals();
					$address->setTaxAmount(0);
					$address->setGrandTotal($address->getGrandTotal()-$tax);
					$address->save();
		        }
			}
		}
		return $this;
	}
	
}