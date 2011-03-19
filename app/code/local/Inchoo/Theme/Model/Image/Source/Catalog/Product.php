<?php
class Inchoo_Theme_Model_Image_Source_Catalog_Product extends Inchoo_Theme_Model_Image_Source_Abstract
{
	
	public function getBaseDir()
	{
		return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
	}
	
	public function getImage($attributeName=null)
	{
		return $this->_model->getData($attributeName);
	}
	
}