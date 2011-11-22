<?php
/*

echo Mage::helper('itheme/price')->currency(22.65, true, false)
USD 23

echo Mage::helper('itheme/price')->currency(22.65, true, false)
23

echo Mage::helper('itheme/price')->currency(22.65, true, true)
<span class="price-code">USD</span><span class="price-value">23</span>

echo Mage::helper('itheme/price')->currency(22.65, false, true)
<span class="price-value">23</span>


echo Mage::helper('itheme/price')->getCurrentCurrencyCode()
USD

echo Mage::helper('itheme/price')->getCurrentCurrencySymbol()
$

*/
class Inchoo_Theme_Helper_Price extends Mage_Core_Helper_Abstract
{
	
	//displays code + rounded no-decimals price
    public function currency($value, $format = true, $includeContainer = true, $mode = 'round')
    {
		switch($mode) {
			case 'floor':
				$price = floor(Mage::helper('core')->currency($value, false, false));
			case 'ceil':
				$price = ceil(Mage::helper('core')->currency($value, false, false));
			case 'round':
				$price = round(Mage::helper('core')->currency($value, false, false));
			default: 
				$price = Mage::helper('core')->currency($value, false, false);
		}
		
		if(!$format) {
            return Mage::helper('core')->currency($price, false, $includeContainer);
        }
		
        $code = $this->getCurrentCurrencyCode();
        
        if(!$includeContainer) {
            return "$code $price";
        }
        
        if(!$format) {
            return '<span class="price-value">' . $price . '</span>';
        }
    
        return '<span class="price-code">' . $code . '</span><span class="price-value">' . $price . '</span>';
    }
    
    public function getCurrentCurrencyCode()
    {
    	return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
    
    public function getCurrentCurrencySymbol()
    {
		return str_replace(Mage::helper('core')->currency(1.1111, false, false),'',Mage::helper('core')->currency(1.1111, true, false));
    }
	
    public function getCurrentCurrencyValue($value)
    {
		return Mage::helper('core')->currency($value, false, false);
    }

}