<?php
class Inchoo_Theme_Model_Image_Source_Enterprise_Catalogevent_Event extends Inchoo_Theme_Model_Image_Source_Abstract
{
	
	public function getBaseDir()
	{
		return Mage::getBaseDir('media') . DS . Enterprise_CatalogEvent_Model_Event::IMAGE_PATH;
	}
	
	public function getImage($attributeName=null)
	{
		return $this->_model->getImage();
	}
	
}