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
 */


/**
 * ServiceCode attribute map Grid
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */
class JLE_Corporate_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
	
    public function __construct()
    {
        parent::__construct();
//        $this->setId('sales_order_grid');
    }

		
	protected function _prepareCollection()
    {
		$customerTable = Mage::helper('jle_corporate')->getCustomerTable();
		$clientTable = Mage::helper('jle_corporate')->getClientTable();
//    	parent::_prepareCollection();
        $collection = Mage::getResourceModel($this->_getCollectionClass())
			->addAttributeToSelect('*')
			->addAttributeToFilter('status', JLE_Corporate_Helper_Data::ORDER_STATUS_GOV)
		;
		$collection->getSelect()
			->joinLeft($customerTable, 'main_table.customer_id = '.$customerTable.'.customer_id', 'client_id')
			->joinLeft(array('client'=>$clientTable), $customerTable.'.client_id = '.'client.client_id', array('client_name'=>'name'))
		;			
        $this->setCollection($collection);
		return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumnAfter('client_name',
            array(
                'header'    => Mage::helper('sales')->__('Client'),
                'type'      => 'text',
                'index'     => 'client_name',
                'filter'    => false,
                'sortable'  => false,
                'filter_index' => 'client.client_name',
        ), 'shipping_name');
		
		parent::_prepareColumns();
		
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/adminhtml_order/view'),
                        'field'   => 'order_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		unset($this->_columns['store_id']);
		unset($this->_columns['status']);
						
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
		
        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/adminhtml_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/adminhtml_order/massUnhold'),
            ));
        }

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
}
