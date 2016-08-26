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
 * client controller
 *
 * @category    JLE
 * @package     JLE_Corporate
 */
class JLE_Corporate_Adminhtml_ClientController extends Mage_Adminhtml_Controller_Action
{
	protected $_entityType;
	
	/**
	 * Initialise the client model
	 *
	 * @return false|JLE_Corporate_Model_Client
	 */
	protected function _initClient()
	{
		if ($id = $this->getRequest()->getParam('client_id')) {
			$client = Mage::getModel('jle_corporate/client')->load($id);
			
			if ($client->getId()) {
				Mage::register('current_client', $client);
				return $client;
			}
		} else {
			$client = Mage::getModel('jle_corporate/client');
			Mage::register('current_client', $client);
			return $client;
		}
		return false;
	}
	
	
    /**
     * Main index action
     *
     */
    public function indexAction()
    {
        // Add the grid container as the only item on this page
        $this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle('Manage Clients');
        $this->renderLayout();
		
    }

	/**
	 * Create a new Client
	 *
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	
	/**
	 * Display the add/edit form for the client
	 *
	 */
	public function editAction()
	{
		$client = $this->_initClient();
		
//		$this->_initAttribute();
//		$this->_initOption();
		
		$this->loadLayout();
		$this->_setActiveMenu('customer');
		
		if ($client) {
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				$headBlock->setTitle($client->getName());
			}	
		} else {
			$this->getLayout()->getBlock('head')->setTitle('New Client');
		}
		
		$this->renderLayout();
	}
	
    /**
     * Grid Action
     * Display list of customers related to current client
     *
     * @return void
     */
    public function customerGridAction()
    {
        if (!$client = $this->_initClient()) {
            return;
        }
        $this->getResponse()->setBody(		
            $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_customers', 'client.customers.grid')
                ->toHtml()
        );
    }

    /**
     * Grid Action
     * Display list of products related to current client
     *
     * @return void
     */
    public function productGridAction()
    {
        if (!$client = $this->_initClient()) {
            return;
        }
        $this->getResponse()->setBody(		
            $this->getLayout()->createBlock('jle_corporate/adminhtml_client_edit_tab_products', 'client.products.grid')
                ->toHtml()
        );
    }

	
	public function editCustomerAction() {
		if ($id = $this->getRequest()->getParam('id')) {
			$this->_redirectUrl(Mage::helper('adminhtml')->getUrl("adminhtml/customer/edit/id/".$id));
		} else {
			$this->_redirectUrl(Mage::helper('adminhtml')->getUrl("adminhtml/customer/edit"));
		} 
	}
	
	/**
	 * Display the add/edit form for the client product prices
	 *
	 */
	public function editpriceAction()
	{
		$client = $this->_initClient();
		if ($productId = $this->getRequest()->getParam('product_id')) {
			$product = Mage::getModel('catalog/product')->load($productId);
			Mage::register('current_product', $product);
			Mage::register('current_client_price', $client->getPricesForProduct($productId));
		}
		$this->loadLayout();
		$this->_setActiveMenu('customer');
		$this->getLayout()->getBlock('head')->setTitle('Edit Client Product Price');
		$this->renderLayout();		
	}

	
	/**
	 * Save the posted data
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('client')) {
			$client = $this->_initClient();
			/** @var $customerForm Mage_Customer_Model_Form */
            $clientForm = Mage::getModel('jle_corporate/form');
            $clientForm->setEntity($client)
                ->setFormCode('adminhtml_client')
                ->ignoreInvisible(false)
            ;
            $formData = $clientForm->extractData($this->getRequest(), 'client');
			$client->setIsActive($data['is_active']);
			$client->setName($data['name']);
			$client->setCode($data['code']);
			$client->setWebsiteId($data['website_id']);
			$client->setCustomerGroupId($data['customer_group_id']);
//			$client->setAttributeSetId($this->_getEntityType()->getId());
            $errors = $clientForm->validateData($formData);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('client_id' => $client->getId())));
                return;
            }

            $clientForm->compactData($formData);
/*			
			if ($id = $this->getRequest()->getParam('client_id')) {
                $client->load($id);
                $data['client_id'] = $id;
            } else {
            	$data['created_at'] = time();
            }
			$data['updated_at'] = time();
			$client->setData($data);
			//validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('edit', array('id' => $client->getId(), '_current' => true));
                return;
            }
*/
			try {
				if ($client->getFixedBillingAddress()) {
					$billing = $this->getRequest()->getPost('billing');
					if (is_array($billing)) {
		                $addressForm = Mage::getModel('customer/form');
		                $addressForm->setFormCode('adminhtml_customer_address');
                        $address = Mage::getModel('customer/address');
						$address->setData($billing);
                        $client->addBillingAddress($address);
		            }
				} 				
				if ($client->getFixedShippingAddress()) {
					$shipping = $this->getRequest()->getPost('shipping');
					if (is_array($shipping)) {
		                $addressForm = Mage::getModel('customer/form');
		                $addressForm->setFormCode('adminhtml_customer_address');
                        $address = Mage::getModel('customer/address');
						$address->setData($shipping);
                        $client->addShippingAddress($address);
		            }
				} 				
				$isNewClient = $client->isObjectNew();
				if (!$client->getId()) {
	            	$client->setCreatedAt(time());
	            }
				$client->setUpdatedAt(time());
				$client->unsEntityTypeId();				
				$client->save();
				/*********************************Do Customers******************************/
				$in_customers = array();
				if (isset($data['in_client_customer'])) {
					parse_str($data['in_client_customer'], &$in_customers);
					$client->saveCustomers($in_customers);
				}
				/*********************************Do Import Products************************/
				if(isset($_FILES['client']['name']['flat_file']) && (file_exists($_FILES['client']['tmp_name']['flat_file']))) {
					if (isset($data['purge_discounts'])) {
						$client->purgeProductPrices();
					}
					$csv = array_map('str_getcsv', file($_FILES['client']['tmp_name']['flat_file']));
					foreach($csv as $row) {
						if (sizeof($row) && isset($row[0]) && isset($row[1])) {
							$_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row[0]); 
							if ($_product) {
								$priceData = array(
									'product_id'=>$_product->getId(),
									'product_sku'=>$row[0],
									'product_price'=>$row[1],
									'product_cost'=>$_product->getInternalCost(),
								);
								$client->saveProductPrice($priceData);
								
							}
						}
					} 
					
				}
				$this->_getSession()->addSuccess($this->__('Client was saved'));
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($client->getId() && $this->getRequest()->getParam('back', false)) {
				$this->_redirect('*/*/edit', array('client_id' => $client->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}

		$this->_redirect('*/*');
	}

	public function savepriceAction() {
		if (($clientId = $this->getRequest()->getPost('client_id')) 
				&& ($productId = $this->getRequest()->getPost('product_id'))) {
			$client = $this->_initClient();
			try {
				$data = $this->getRequest()->getPost();
			} catch (Exception $e) {
				Mage::logException($e);
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
			$client->saveProductPrice($data);
		} else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}
		$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('client_id' => $clientId)));
		return;
	}
	
	public function deletepriceAction() {
		$client = $this->_initClient();
		if ($productId = $this->getRequest()->getParam('product_id')) {
			$client->deleteProductPrice($productId);
		} else {
			$this->_getSession()->addError($this->__('There was no data to delete.'));
		}
		$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('client_id' => $client->getId())));
		return;
	}

    /**
     * Delete client action
     */
    public function deleteAction()
    {
        $client = $this->_initClient();
        
        if ($client->getId() < 1) {
            $this->_getSession()->addError(
                $this->__('This client no longer exists.')
            );
            return $this->_redirect('*/*/index');
        }
        
        try {
            $client->delete();
            
            $this->_getSession()->addSuccess(
                $this->__('The client has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/index');
    }
	
    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['client_layout_update_xml'])) {
            /** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
            if (!empty($data['client_layout_update_xml']) && !$validatorCustomLayout->isValid($data['landing_layout_update_xml'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }
	
    /**
     * Return Client Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType('gov_client');
        }
        return $this->_entityType;
    }

	
}
