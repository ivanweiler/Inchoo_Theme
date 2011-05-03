<?php
class Inchoo_Theme_Helper_Data extends Mage_Core_Helper_Abstract
{
	//move this to Inchoo_Theme_Helper_Template // Filter ?
	
	private $_customvarCache = array();
	
	public function getBlockHtml($type, $params)
	{
		return Mage::app()->getLayout()->createBlock($type, null, $params)->toHtml();
	}
	
	public function getCmsBlockHtml($id)
	{
		return Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($id)->toHtml();
	}
	
	public function getCustomvar($code, $type=Mage_Core_Model_Variable::TYPE_TEXT)
	{
		if(!isset($this->_customvarCache[$code])) {
			
			$variable = Mage::getModel('core/variable')
					->setStoreId(Mage::app()->getStore()->getId())
					->loadByCode($code);
			
			$this->_customvarCache[$code][Mage_Core_Model_Variable::TYPE_TEXT] = $variable->getValue(Mage_Core_Model_Variable::TYPE_TEXT);
			$this->_customvarCache[$code][Mage_Core_Model_Variable::TYPE_HTML] = $variable->getValue(Mage_Core_Model_Variable::TYPE_HTML);
		}
		
        return (string)$this->_customvarCache[$code][$type];	
	}
	
	public function getConfig($path, $store = null)
	{
		return Mage::getStoreConfig($path,$store);
	}

}