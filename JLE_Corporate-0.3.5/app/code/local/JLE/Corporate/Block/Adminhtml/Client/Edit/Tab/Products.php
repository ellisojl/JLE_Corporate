<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
		
{

	public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('in_product');
		$this->setId('corporate_client_products');
        $this->setUseAjax(true);
    }

    public function getClient()
    {
        return Mage::registry('current_client');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in client flag
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($customerIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($clientID = $this->getClient()->getId()) {
            $this->setDefaultFilter(array('at_product_price'=>1));
        }
		$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
		$store = Mage::app()->getStore($storeId);
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('sku', 'name', 'jle_cost'))
//			->addAttributeToSelect(array('margin_now'=>('`at_price`.`value`')))
			->addAttributeToFilter('type_id', array('neq'=>'grouped'))
//			->addAttributeToFilter('status', array('eq'=>'1'))
            ->joinField('product_price',
                'jle_corporate/client_product',
                '*',
                'product_id=entity_id',
                'client_id='.(int) $this->getRequest()->getParam('id', $this->getClient()->getId()),
                'left');
		;
		$collection->addExpressionAttributeToSelect('margin_then', 'ROUND(((at_product_price.product_price-at_product_price.product_cost)/at_product_price.product_price)*100, 1)', array());
		$collection->addExpressionAttributeToSelect('margin_now', 'ROUND(((at_product_price.product_price-{{jle_cost}})/at_product_price.product_price)*100, 1)', array('jle_cost'));
		$collection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
            $storeId
        );
		$collection->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
            null,
            'left',
            0
        );
		$collection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner',
            $storeId
        );
		
		$collection->addAttributeToSelect('price');
		$collection->getSelect()->order(array('at_product_price.product_id DESC'));
			//'jle_corporate/client_customer.customer_id', 'jle_corporate/client_customer.is_supervisor'
//Mage::log($collection->getSelect()->__toString());
		$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
/*        $this->addColumn('in_product', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_product',
            'field_name'=> 'product',
            'style'		=> 'pointer-events: none;',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'filter'    => false,
            'index'     => 'entity_id'
        ));
 */
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('Product Sku'),
            'index'     => 'sku'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'index'     => 'name'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('List Price'),
            'type'		=> 'currency',
            'currency_code' => 'USD',
            'index'     => 'price'
        ));
        $this->addColumn('product_price', array(
            'header'    => Mage::helper('jle_corporate')->__('Account Price'),
            'type'		=> 'currency',
            'currency_code' => 'USD',
            'index'     => 'product_price'
        ));
        $this->addColumn('product_cost', array(
            'header'    => Mage::helper('jle_corporate')->__('Cost @ Upload'),
            'type'		=> 'currency',
            'currency_code' => 'USD',
            'index'     => 'product_cost'
        ));
        $this->addColumn('jle_cost', array(
            'header'    => Mage::helper('jle_corporate')->__('Cost Now'),
            'type'		=> 'currency',
            'currency_code' => 'USD',
            'index'     => 'jle_cost'
        ));
        $this->addColumn('margin_then', array(
            'header'    => Mage::helper('jle_corporate')->__('Margin @ Upload %'),
            'index'     => 'margin_then'
        ));
        $this->addColumn('margin_now', array(
            'header'    => Mage::helper('jle_corporate')->__('Margin Now %'),
            'index'     => 'margin_now'
        ));
        $this->addColumn('action_edit',
            array(
                'header'    =>  Mage::helper('catalog')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('catalog')->__('Edit'),
                        'url'       => array(
                        	'base'=> '*/*/editprice',
                        	'params'=>array('client_id'=>$this->getRequest()->getParam('client_id'))
						),
                        'field'     => 'product_id'
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
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products) && $this->getClient()->getId()) {
            $products = $this->getClient()->getProductsArray();
			if ($products) {
            	return array_keys($products);
			}
        }
        return $products;
    }
	
	
}
