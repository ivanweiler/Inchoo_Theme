<?php
class Inchoo_Theme_Model_Image_Source_Catalog_Category extends Inchoo_Theme_Model_Image_Source_Abstract
{
	
	public function getBaseDir()
	{
		return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category';
	}
	
	public function getImage($attributeName=null)
	{
		return $this->_model->getImage();
	}
	
}