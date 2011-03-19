<?php
class Inchoo_Theme_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	//move this to Inchoo_Theme_Helper_Template // Filter ?
	
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
		$variable = Mage::getModel('core/variable')
				->setStoreId(Mage::app()->getStore()->getId())
				->loadByCode($code);
        return $variable->getValue($mode) ? $variable->getValue($mode) : '';        		
	}
	
	public function getConfig($path, $store = null)
	{
		return Mage::getStoreConfig($path,$store);
	}

}