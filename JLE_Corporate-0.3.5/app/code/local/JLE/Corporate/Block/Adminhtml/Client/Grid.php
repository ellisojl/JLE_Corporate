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
class JLE_Corporate_Block_Adminhtml_Client_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();
		$this->setId('client_id');
//		$this->setDefaultSort();
//		$this->setDefaultDir();
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('jle_corporate/client_collection');
        $collection->setOrder('client_id', 'DESC');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('client_id', array(
            'header'=> Mage::helper('jle_corporate')->__('ID'),
            'width' => '20px',
            'type'  => 'text',
            'index' => 'client_id'
        ));

        $this->addColumn('name', array(
            'header'=> Mage::helper('jle_corporate')->__('Name'),
            'type'  => 'text',
            'index' => 'name',
            'truncate' => 9999
        ));

        $this->addColumn('code', array(
            'header'=> Mage::helper('jle_corporate')->__('Code'),
            'type'  => 'text',
            'index' => 'code',
            'truncate' => 9999
        ));

        $this->addColumn('website_id', array(
            'header'=> Mage::helper('jle_corporate')->__('Website'),
            'index' => 'website_id',
            'type'		=> 'options',
            'options' 	=> $this->getWebsites()
        ));
		
        $this->addColumn('customer_group_id', array(
            'header'=> Mage::helper('jle_corporate')->__('Group'),
            'index' => 'customer_group_id',
            'type'		=> 'options',
            'options' 	=> $this->getCustomerGroups()
        ));

		$this->addColumn('action',
			array(
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('jle_corporate')->__('Edit'),
						'url'     => array(
						'base'=>'*/*/edit',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));
			
        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('corporate/adminhtml_client/edit', array('client_id' => $row->getId()));
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('client_id');
        $this->getMassactionBlock()->setFormFieldName('client_id');
        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_client/index', array('_current'=>true));
    }
	
	/**
	 * Retrieve an array of all of the websites
	 *
	 * @return array
	 */
	protected function getWebsites()
	{
		$websites = Mage::getResourceModel('core/website_collection');
		$options = array(0 => $this->__('Global'));
		
		foreach($websites as $website) {
			$options[$website->getId()] = $website->getName();
		}

		return $options;
	}

	/**
	 * Retrieve an array of all of the groups
	 *
	 * @return array
	 */
	protected function getCustomerGroups()
	{
		$groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt'=> 0))
                ->load()
                ->toOptionArray();
		$options = array(0 => $this->__('Global'));
		foreach($groups as $group) {
			$options[$group['value']] = $group['label'];
		}

		return $options;
	}
	
}
