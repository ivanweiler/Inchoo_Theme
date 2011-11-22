<?php
class Inchoo_Theme_Helper_Price extends Mage_Core_Helper_Abstract
{
	
	//displays code + rounded no-decimals price, WIP
	/*
    public function currency($value, $format = true, $includeContainer = true, $mode = 'round')
    {
		switch($mode) {
			case 'floor':
				$price = floor(Mage::helper('core')->currency($value, false, false));
				break;
			case 'ceil':
				$price = ceil(Mage::helper('core')->currency($value, false, false));
				break;
			case 'round':
				$price = round(Mage::helper('core')->currency($value, false, false));
				break;
			default:
				$price = $this->getCurrentCurrencyValue($value);
		}
		
		//var_dump((string)$price);
		
		//format == showCurrency in our case, like in older Magento
		
		if(!$format && !$includeContainer) {
			return (string)$price;
        }
		
        //$code = $this->getCurrentCurrencyCode();
		$code = $this->getCurrentCurrencySymbol();
        
		//below should respect zend format and it doesn't!!
		
        if(!$includeContainer) {
            return "$code $price";
        }
        
        if(!$format) {
            return '<span class="price-value">' . $price . '</span>';
        }
    
        return '<span class="price-code">' . $code . '</span><span class="price-value">' . $price . '</span>';
    }
    */
    
    public function getCurrentCurrencyCode()
    {
    	return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
    
    public function getCurrentCurrencySymbol()
    {
		return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    }
	
    public function getCurrentCurrencyValue($value)
    {
		$store = Mage::app()->getStore();
		
		//convert if needed
        if ($store->getCurrentCurrency() && $store->getBaseCurrency()) {
            $value = $store->getBaseCurrency()->convert($value, $store->getCurrentCurrency());
        }
		
		if ($store->getCurrentCurrency()) {
            return $store->getCurrentCurrency()->format($value, array('display'=>Zend_Currency::NO_SYMBOL), false, false);
        }
		
        return $value;
	
		//return Mage::app()->getStore()->getPriceFilter()->filter($value);
    }

}