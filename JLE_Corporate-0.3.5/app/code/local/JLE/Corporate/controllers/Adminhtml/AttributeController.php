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
 * Corporate attributes controller
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */

/**
 * Controller for Client Attributes Management
 */
class JLE_Corporate_Adminhtml_AttributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Client Entity Type instance
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

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

    /**
     * Load layout, set breadcrumbs
     *
     * @return JLE_Corporate_Adminhtml_AttributeController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/attributes')
            ->_addBreadcrumb(
                Mage::helper('jle_corporate')->__('Customer'),
                Mage::helper('jle_corporate')->__('Customer'))
            ->_addBreadcrumb(
                Mage::helper('jle_corporate')->__('Manage Gov Attributes'),
                Mage::helper('jle_corporate')->__('Manage Gov Attributes'));
        return $this;
    }

    /**
     * Retrieve client attribute object
     *
     * @return Mage_Client_Model_Attribute
     */
    protected function _initAttribute()
    {
        $attribute = Mage::getModel('jle_corporate/attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Attributes grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Manage Gov Attributes'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new attribute action
     *
     */
    public function newAction()
    {
        $this->addActionLayoutHandles();
        $this->_forward('edit');
    }

    /**
     * Edit attribute action
     *
     */
    public function editAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title($this->__('Manage Gov Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()
                    ->addError(Mage::helper('jle_corporate')->__('Attribute is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('jle_corporate')->__('You cannot edit this attribute.'));
                $this->_redirect('*/*/');
                return;
            }

            $this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title($this->__('New Attribute'));
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        Mage::register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId()
            ? Mage::helper('jle_corporate')->__('Edit Client Attribute')
            : Mage::helper('jle_corporate')->__('New Client Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label)
            ->renderLayout();
    }

    /**
     * Validate attribute action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('jle_corporate')->__('Attribute with the same code already exists')
                );

                $this->_initLayoutMessages('adminhtml/session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        return Mage::helper('jle_corporate/attribute')->filterPostData($data);
    }

    /**
     * Save attribute action
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
            $attributeObject = $this->_initAttribute();
            $helper = Mage::helper('jle_corporate/attribute');

            //filtering
            try {
                $data = $this->_filterPostData($data);
            } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                    if (isset($data['attribute_id'])) {
                        $this->_redirect('*/*/edit', array('_current' => true));
                    } else {
                        $this->_redirect('*/*/new', array('_current' => true));
                    }
                    return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('jle_corporate')->__('You cannot edit this attribute.')
                    );
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code']     = $attributeObject->getAttributeCode();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['frontend_input']     = $attributeObject->getFrontendInput();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['is_system']          = $attributeObject->getIsSystem();
            } else {
                $data['backend_model']      = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model']       = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type']       = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined']    = 1;
                $data['is_system']          = 0;

                // add set and group info
                $data['attribute_set_id']   = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = Mage::getModel('eav/entity_attribute_set')
                    ->getDefaultGroupId($data['attribute_set_id']);
            }

            if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'adminhtml_client';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                $data[$scopeKeyPrefix . 'default_value'] = $helper->stripTags(
                    $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField));
            }

            $data['entity_type_id']     = $this->_getEntityType()->getId();
            $data['validate_rules']     = $helper->getAttributeValidateRules($data['frontend_input'], $data);

            $validateRulesErrors = $helper->checkValidateRules($data['frontend_input'], $data['validate_rules']);
            if (count($validateRulesErrors)) {
                foreach ($validateRulesErrors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }

            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }

            try {
                Mage::dispatchEvent('jle_corporate_attribute_before_save', array(
                    'attribute' => $attributeObject
                ));
                $attributeObject->save();
                Mage::dispatchEvent('jle_corporate_attribute_save', array(
                    'attribute' => $attributeObject
                ));

                $this->_getSession()->addSuccess(
                    Mage::helper('jle_corporate')->__('The client attribute has been saved.')
                );
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'attribute_id'  => $attributeObject->getId(),
                        '_current'      => true
                    ));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('jle_corporate')->__('An error occurred while saving the client attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Delete attribute action
     *
     */
    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || !$attributeObject->getIsUserDefined())
            {
                $this->_getSession()->addError(
                    Mage::helper('jle_corporate')->__('You cannot delete this attribute.')
                );
                $this->_redirect('*/*/');
                return;
            }
            try {
                $attributeObject->delete();
                Mage::dispatchEvent('jle_corporate_attribute_delete', array(
                    'attribute' => $attributeObject
                ));

                $this->_getSession()->addSuccess(
                    Mage::helper('jle_corporate')->__('The client attribute has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('jle_corporate')->__('An error occurred while deleting the client attribute.')
                );
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }

        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check whether attributes management functionality is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('jle/corporate/attributes/client_attributes');
    }
}
