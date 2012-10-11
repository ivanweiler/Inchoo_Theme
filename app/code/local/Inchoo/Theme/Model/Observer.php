<?php
class Inchoo_Theme_Model_Observer
{
	//clean_catalog_images_cache_after
	public function clearCache()
	{
		Mage::getModel('inchoo_theme/image')->clearCache();
	}
	
	//controller_action_predispatch
	public function actionPreDispatch($observer)
	{
		$controller = $observer->getEvent()->getControllerAction();
		
		//this also allows controllers custom noRoute action
		if($controller->getRequest()->getActionName() == 'noRouteDummy') {
			return $this;
		}
		
		$mode = (bool)Mage::getStoreConfig('advanced/modules_disable_route/mode');
		$exceptionMatch = false;
		$urlToMatch =  Mage::getModel('core/url')->getUrl('*/*/*');
		
		foreach($this->_getExceptions() as $exception)
		{
			if(Mage::getModel('core/url')->getUrl($exception) == $urlToMatch) {
				$exceptionMatch = true;
				break;
			}
		}
		
		if($exceptionMatch == $mode) {
			$controller->getRequest()->setActionName('noRouteDummy')->setDispatched(false);		
		}
		
		return $this;
	}
	
	private function _getExceptions()
	{
		$return = array();
		
		$configValueSerialized = Mage::getStoreConfig('advanced/modules_disable_route/exception');
		
		if (!$configValueSerialized) return $return;
		
		$exceptions = @unserialize($configValueSerialized);
		
		if (!$exceptions) return $return;
		
		foreach($exceptions as $exception){
			if(isset($exception['value']) && trim($exception['value'])){
				$return[] = trim($exception['value']);
			}
		}

		return $return;
	}
	
	//core_block_abstract_to_html_after
	public function tidyPageHtml($observer)
	{
		if(!$observer->getEvent()->getBlock() instanceof Mage_Page_Block_Html) {
			return;
		}
		
		if(!class_exists('tidy', false)) {
			 return;
		};

		if(!Mage::getStoreConfigFlag('dev/html/tidy_html_files')) {
			return;
		}
		
		$html = $observer->getEvent()->getTransport()->getHtml();

		// Tidy
		try {
			$config = array(
				'indent'				=>	'auto', //true
				'indent-spaces'			=>	0,
				'indent-cdata'			=>	true,
				'wrap'					=>	0,
				'newline'				=>	'LF',
				'input-xml'				=>	true,
			);
			
			//Mage::dispatchEvent('itheme_tidy_config', array('config' => &$config));
			
			$tidy = new tidy;	
			$tidy->parseString($html, $config, 'utf8');
			$tidy->cleanRepair();
			
			$html = (string)$tidy;
			
			//fix, why is Tidy doing this?
			$html = str_replace("//\n<![CDATA[", "\n//<![CDATA[", $html);

		} catch (Exception $e) {
        	// If something went wrong just output the original HTML code
		}

		$observer->getEvent()->getTransport()->setHtml($html);
	}	
	
}
