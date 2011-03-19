<?php
class Inchoo_Theme_Model_Image_Source_Blog_Blog extends Inchoo_Theme_Model_Image_Source_Abstract
{
	
	public function getBaseDir()
	{
		return Mage::getBaseDir('media') . DS . 'blog' . DS . 'post';
	}
	
	public function getImage($attributeName=null)
	{
		return $this->_model->getImage();
	}
	
}