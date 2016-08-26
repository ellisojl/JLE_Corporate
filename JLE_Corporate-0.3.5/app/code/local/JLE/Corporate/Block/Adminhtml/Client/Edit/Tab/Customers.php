<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid
		
{

	public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('entity_id');
		$this->setId('corporate_client_customers');
        $this->setUseAjax(true);
    }

    public function getClient()
    {
        return Mage::registry('current_client');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in client flag
        if ($column->getId() == 'in_customer') {
            $customerIds = $this->_getSelectedCustomers();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$customerIds));
            }
            elseif(!empty($customerIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$customerIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getClient()->getId()) {
            $this->setDefaultFilter(array('in_customer'=>1));
        }
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect(array('name', 'firstname', 'lastname', 'company'))
            ->joinField('customer_id',
                'jle_corporate/client_customer',
                'customer_id',
                'customer_id=entity_id',
                'client_id='.(int) $this->getRequest()->getParam('id', $this->getClient()->getId()),
                'left')
//            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_company', 'customer_address/company', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
//            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
			;
			$collection->getSelect()->order(array('at_customer_id.is_supervisor DESC', 'at_customer_id.customer_id DESC'));
			//'jle_corporate/client_customer.customer_id', 'jle_corporate/client_customer.is_supervisor'
        $this->setCollection($collection);
/*
        if ($this->getClient()->getCustomersReadonly()) {
            $customerIds = $this->_getSelectedCustomers();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$customerIds));
        }
*/
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_customer', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_customer',
            'field_name'=> 'customer',
            'inline_css'=> 'customer',
            'values'    => $this->_getSelectedCustomers(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('is_supervisor', array(
            'header'    => Mage::helper('jle_corporate')->__('Is Supervisor'),
            'type'      => 'checkbox',
            'name'      => 'is_supervisor',
            'field_name'=> 'supervisor',
            'inline_css'=> 'supervisor',
            'values'    => $this->_getSupervisorCustomers(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'filter'    => false,
            'sortable'  => false,
        ));
        $this->addColumn('firstname', array(
            'header'    => Mage::helper('customer')->__('First Name'),
            'index'     => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    => Mage::helper('customer')->__('Last Name'),
            'index'     => 'lastname'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '80',
            'index'     => 'email'
        ));
        $this->addColumn('billing_company', array(
            'header'    => Mage::helper('customer')->__('Company'),
            'width'     => '80',
            'index'     => 'billing_company'
        ));
        $this->addColumn('billing_city', array(
            'header'    => Mage::helper('customer')->__('City'),
            'width'     => '40',
            'index'     => 'billing_city'
        ));
        $this->addColumn('action_edit',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/editCustomer'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/customerGrid', array('_current'=>true));
    }

    protected function _getSelectedCustomers()
    {
        $customers = $this->getRequest()->getPost('selected_customers');
        if (is_null($customers) && $this->getClient()->getId()) {
            $customers = $this->getClient()->getCustomersArray();
			if ($customers) {
            	return array_keys($customers);
			}
        }
        return $customers;
    }

    protected function _getSupervisorCustomers()
    {
		$customers = array();
		if ($this->getClient()->getId()) {
			$customers = $this->getClient()->getCustomersArray();
		}
		$supervisors = array();
		foreach ($customers as $key=>$value) {
			if ($value == 1) {
				$supervisors[] = $key;
			}
		}
		return $supervisors;
    }
	
	
}
