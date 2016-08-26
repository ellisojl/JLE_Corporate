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
 * Client mysql4 abstract resource model
 *
 * @category    JLE
 * @package     JLE_Corporate
 * @author      Josh Ellison <ellisojl@gmail.com>
 */

class JLE_Corporate_Model_Resource_Client extends Mage_Eav_Model_Entity_Abstract
{
	protected $_main_table;
    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->setType('gov_client');
        $this->setConnection('client_read', 'client_write');
		$this->_main_table = 'jle_corporate/client';
    }
	
    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array();
    }
	
    public function getEntityIdField()
    {
        if (!$this->_entityIdField) {
            $this->_entityIdField = $this->getEntityType()->getEntityIdField();
            if (!$this->_entityIdField) {
                $this->_entityIdField = 'client_id';
            }
        }
        return $this->_entityIdField;
    }
	
	public function getMainTable() {
		return $this->_main_table;
	}
		
	
}
