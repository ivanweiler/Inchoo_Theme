<?php
class Inchoo_Theme_Model_Image_Source_Sales_Quote_Item extends Inchoo_Theme_Model_Image_Source_Abstract
{

	public function getBaseDir()
	{
		return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
	}
	
	public function getImage($attributeName='thumbnail')
	{
		return $this->_getImageProduct($attributeName)->getData($attributeName);
	}
	
	private function _getImageProduct($attributeName)
	{
		switch($this->_model->getProductType()) {
			
			case 'configurable':
				$product = $this->_model->getChildProduct();
				if (!$product 
					|| !$product->getData($attributeName)
					|| ($product->getData($attributeName) == 'no_selection')
					|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Configurable::CONFIGURABLE_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Configurable::USE_PARENT_IMAGE)) {
					$product = $this->_model->getProduct();
				}
				break;				
			case 'grouped':
				$product = $this->_model->getProduct();
				if (!$product->getData($attributeName)
					||($product->getData($attributeName) == 'no_selection')
					|| (Mage::getStoreConfig(Mage_Checkout_Block_Cart_Item_Renderer_Grouped::GROUPED_PRODUCT_IMAGE) == Mage_Checkout_Block_Cart_Item_Renderer_Grouped::USE_PARENT_IMAGE)) {
					$product = $this->_model->getGroupedProduct();
				}			
				break;
			default:
				$product = $this->_model->getProduct();
		
		}
		
		return $product;

	}
	
}