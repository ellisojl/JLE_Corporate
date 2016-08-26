<?php
/**
 * @category    JLE
 * @package     JLE_Corporate
 */

class JLE_Corporate_Block_Adminhtml_Client_Price_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('edit_price_form');
        $this->setTitle(Mage::helper('jle_corporate')->__('Product Price'));
    }

    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'jle_corporate';
	
	protected function _prepareForm()
	{
		$product = Mage::registry('current_product');
		$client = Mage::registry('current_client');
		$clientPrices = Mage::registry('current_client_price');
		if (!$clientPrices) {
			$clientPrices['product_price'] = $product->getPrice();
			$clientPrices['product_cost'] = $product->getInternalCost();
		}
		$form = new Varien_Data_Form(
			array(
				'id' => 'edit_client_price_form',
				'action' => $this->getUrl('*/*/saveprice', array(
					'client_id' => $this->getRequest()->getParam('client_id'),
					'product_id' => $this->getRequest()->getParam('product_id')
					)
				),
				'name'	  => 'editForm',
				'method'  => 'post',
				'enctype' => 'multipart/form-data'
			)
		);
		
		$fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('jle_corporate')->__('Client Product Price')
        ));
		
		$fieldset->addField('client_id', 'hidden', array('name' => 'client_id'));
		$fieldset->addField('product_id', 'hidden', array('name' => 'product_id'));
		
		$fieldset->addField('product_name', 'note',
            array(
                'text'      => 		$product->getName(),
                'label'     => Mage::helper('jle_corporate')->__('Name'),
            )
        );

		$fieldset->addField('product_sku', 'text',
            array(
                'name'      => 'product_sku',
                'label'     => Mage::helper('jle_corporate')->__('SKU'),
                'readonly'  => true,
                'style'		=> 'background: none; border: 0;'
            )
        );
		
		$fieldset->addField('price', 'note',
            array(
                'text'      => Mage::helper('core')->currency($product->getPrice(), true, false),
                'label'     => Mage::helper('jle_corporate')->__('Current Price'),
            )
        );

		$fieldset->addField('product_price', 'text',
            array(
                'name'      => 'product_price',
                'label'     => Mage::helper('jle_corporate')->__('Account Price'),
                'class'     => 'required-entry',
                'required'  => true,
            )
        );
		
		$fieldset->addField('product_cost', 'text',
            array(
                'name'      => 'product_cost',
                'label'     => Mage::helper('jle_corporate')->__('Cost @ Upload'),
                'currency_code' => Mage::app()->getStore(0)->getBaseCurrency()->getCode(),
                'class'     => 'required-entry',
                'readonly'	=> true,
                'style'		=> 'background: none; border: 0;'
            )
        );

		$fieldset->addField('cost_now', 'note',
            array(
                'text'      => Mage::helper('core')->currency($product->getInternalCost(), true, false),
                'label'     => Mage::helper('jle_corporate')->__('Cost Now'),
            )
        );
		
		$fieldset->addField('margin_upload', 'note',
            array(
                'text'      => round((($clientPrices['product_price']-$clientPrices['product_cost'])/$clientPrices['product_price'])*100, 1) . '%',
                'label'     => Mage::helper('jle_corporate')->__('Margin @ Upload'),
            )
        );
		
		$fieldset->addField('margin_now', 'note',
            array(
                'text'      => round((($clientPrices['product_price']-$product->getInternalCost())/$clientPrices['product_price'])*100, 1) . '%',
                'label'     => Mage::helper('jle_corporate')->__('Margin Now'),
            )
        );
		
		$data = array();
		$data['client_id'] = $client->getId();
		$data['product_id'] = $product->getId();
		$data['product_sku'] = $product->getSku();
		$data['product_cost'] = round($clientPrices['product_cost'], 2);
		$data['product_price'] = round($clientPrices['product_price'], 2);
		$form->addValues($data);
        $form->setAction($this->getUrl('*/*/saveprice'));
        $form->setUseContainer(true);
        $this->setForm($form);

		return parent::_prepareForm();
	}
	
	
			
}
