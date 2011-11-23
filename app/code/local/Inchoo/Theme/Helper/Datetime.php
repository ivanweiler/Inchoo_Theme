<?php
//WIP draft
class Inchoo_Theme_Helper_Datetime extends Mage_Core_Helper_Abstract
{

	//$this->formatDate(Mage::app()->getLocale()->date($_coupon->getExpiresAt(),Varien_Date::DATETIME_INTERNAL_FORMAT),'short')
	//Mage_Sales_Model_Abstract
	
	//time can be Mysql datetime (2008-07-09 10:13:41) or Unix timestamp
	//format can be Magento format or php date() format
	
	//$formatType=php|iso, $format=d.M.Y
	//$formatType=full|long|medium|short, $format=date|time|datetime	
	public function format($dateTime, $formatType='short', $format='datetime', $store=null)
	{
		try {
			$store = Mage::app()->getStore($store);
		} catch (Exception $e) {
        	$store = Mage::app()->getStore();
        }
        
        $isTimestamp = ((string)(int)$dateTime == $dateTime);
        $timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        
        if(!$isTimestamp) {
        	$dateTime = Varien_Date::toTimestamp($dateTime);
        }
		
        $date = new Zend_Date($dateTime, null, Mage::app()->getLocale()->getLocale());
        $date->setTimezone($timezone);
        
        if(in_array($formatType,array('full','long','medium','short'))) {
        	switch($format) {
        		case 'date': 
					$format = Mage::app()->getLocale()->getDateFormat($formatType);
					break;
        		case 'time':
					$format = Mage::app()->getLocale()->getTimeFormat($formatType);
					break;
        		case 'datetime':
        		default:
        			$format = Mage::app()->getLocale()->getDateTimeFormat($formatType);
        	}
        }
        
        return $date->toString($format, $formatType);
	}

	
	//Magento like shortcuts, needed?
	
	public function formatDate($date=null, $format='short', $showTime=false)
	{
		return $this->format($date, $format, $showTime ? 'datetime' : 'date');
	}	
	
	public function formatStoreDate($store, $date, $format='short', $showTime=false)
	{
		return $this->format($date, $format, $showTime ? 'datetime' : 'date', $store);
	}
	
	
}