<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_OrderController extends Mage_Core_Controller_Front_Action
{

	public function historyAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Orders'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();		
	}
	
	public function viewAction() {
		if ($id = $this->getRequest()->getParam('order_id')) {
			$order = Mage::getModel('sales/order')->load($id);			
			if ($order->getId()) {
				Mage::register('current_order', $order);
			}
		}
		if (!$this->_canViewOrder($order)) {
			Mage::getSingleton('customer/session')->addError($this->__('You cannot view that order.'));
			$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
			return;
		}
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
	}	

	public function printAction() {
		if ($id = $this->getRequest()->getParam('order_id')) {
			$order = Mage::getModel('sales/order')->load($id);			
			if ($order->getId()) {
				Mage::register('current_order', $order);
			}
		}
		if (!$this->_canViewOrder($order)) {
			Mage::getSingleton('customer/session')->addError($this->__('You cannot view that order.'));
			$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
			return;
		}
        $this->loadLayout('print');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
	}	
	
    /**
     * Renders CMS Home page
     *
     * @param string $coreRoute
     */
    public function approveAction()
    {
    	$order = $this->_getOrder();
		if ($order && Mage::helper('jle_corporate')->canApproveOrder($order)) {
			try {
				$order->setState(
		            JLE_Corporate_Helper_Data::ORDER_STATE_GOV,
		            JLE_Corporate_Helper_Data::ORDER_STATUS_GOV,
		            Mage::helper('jle_corporate')->__('Order in review from Government.'),
		            false
		        );
		        $order->save();
				Mage::getSingleton('customer/session')->addSuccess(
	                $this->__('The order has been approved.')
	            );
			}
	        catch (Mage_Core_Exception $e) {
	            Mage::getSingleton('customer/session')->addError($e->getMessage());
	        }
	        catch (Exception $e) {
	            Mage::getSingleton('customer/session')->addError($this->__('The order has not been approved.'));
	            Mage::logException($e);
	        } 
		}	
		$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
    }
	
	
	/**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     *
     */
    public function cancelAction()
    {
    	$order = $this->_getOrder();
		if ($order && Mage::helper('jle_corporate')->canApproveOrder($order)) {
			try {
	            $order->setState(
		            Mage_Sales_Model_Order::STATE_CANCELED,
		            Mage_Sales_Model_Order::STATE_CANCELED,
		            Mage::helper('jle_corporate')->__('Cancelled by supervisor'),
		            false
		        )->save();
	            Mage::getSingleton('customer/session')->addSuccess(
	                $this->__('The order has been cancelled.')
	            );
	        }
	        catch (Mage_Core_Exception $e) {
	            Mage::getSingleton('customer/session')->addError($e->getMessage());
	        }
	        catch (Exception $e) {
	            Mage::getSingleton('customer/session')->addError($this->__('The order has not been cancelled.'));
	            Mage::logException($e);
	        }
		} 
		$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
    }
	
	protected function _getOrder() {
		$orderId = (int) $this->getRequest()->getParam('order_id');
		if ($orderId) {
			return Mage::getModel('sales/order')->load($orderId);
		}
		return null;
	}
	
	protected function _canApproveOrder($order) {
		return Mage::helper('jle_corporate')->canApproveOrder($order);		
	}

	protected function _canViewOrder($order) {
		return Mage::helper('jle_corporate')->canViewOrder($order);
		
	}

}
