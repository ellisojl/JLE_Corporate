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
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 
 * order controller
 *
 * @category    JLE
 * @package     JLE_Corporate
 */
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class JLE_Corporate_Adminhtml_OrderController extends Mage_Adminhtml_Sales_OrderController
{
	
	/**
     * Orders grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Government Orders'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(		
            $this->getLayout()->createBlock('jle_corporate/adminhtml_order_grid', 'corporate.order.grid')
                ->toHtml()
        );

//    	$this->getLayout()->createBlock('jle_corporate/adminhtml_order_grid');
//        $this->loadLayout(false);
//        $this->renderLayout();
    }
	
	public function createAction() {
		$this->_redirectUrl(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order_create/start"));
	}

	public function viewAction() {
		$this->_redirectUrl(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/order_id/" . $this->getRequest()->getParam('order_id')));
	}
	
	/**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnholdOrder = 0;
        $countNonUnholdOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
			Mage::helper('jle_chargelogic')->finalizeOrder($order);
            $order->setState('processing', 'processing');
			$order->getPayment()->setChargelogicFinalized(1)->save();
            $order->save();
			$countUnholdOrder++;
        }
        if ($countNonUnholdOrder) {
            if ($countUnholdOrder) {
                $this->_getSession()->addError($this->__('%s order(s) were not released from holding status.', $countNonUnholdOrder));
            } else {
                $this->_getSession()->addError($this->__('No order(s) were released from holding status.'));
            }
        }
        if ($countUnholdOrder) {
            $this->_getSession()->addSuccess($this->__('%s order(s) have been released from holding status.', $countUnholdOrder));
        }
        $this->_redirect('*/*/');
    }
	
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('jle_corporate/adminhtml_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('jle_corporate/adminhtml_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

	
}
	
