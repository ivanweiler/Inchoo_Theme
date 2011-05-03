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
		if($controller->getRequest()->getActionName() == 'noRoute') {
			return $this;
		}
		
		//var_dump( Mage::getModel('core/url')->getUrl('*/*/*') );
		//var_dump(Mage::getModel('core/url')->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true)));
		//var_dump($this->_getExceptions());
		
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
		
		//(enable all)1 + match 1 => no, (disable all)0 + !match 0 => no
		if($exceptionMatch == $mode) {
			//$controller->getResponse()->setHeader('HTTP/1.1','404 Not Found');
	        //$controller->getResponse()->setHeader('Status','404 File not found');
			$controller->getRequest()->setActionName('noRoute')->setDispatched(false);		
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
		//return;
		
		if(!$observer->getEvent()->getBlock() instanceof Mage_Page_Block_Html) {
			return;
		}
		
		if(!class_exists('tidy',false)) {
			 return;
		};
		
		if(!Mage::getStoreConfigFlag('dev/html/tidy_html_files')) {
			return;
		}
		
		$html = $observer->getEvent()->getTransport()->getHtml();
		
		$start = microtime(true);

		// Tidy
		try {
			$config = array(
				'indent'				=>	'auto', //true
				'indent-spaces'			=>	0,
				'indent-cdata'			=>	true,
				//'indent-attributes'	=>	true,
				//only way to have closed metas
				'output-xhtml'			=>	true,
				//'clean'					=>	false,
				'wrap'					=> 0,
				//'drop-proprietary-attributes'	=>	false,
				//strict removes form name attribute
				'doctype'				=>	'omit',
				'preserve-entities'		=>	true,
				'newline'				=>	'LF',

				//'new-blocklevel-tags' ?
				'new-inline-tags'		=>	'
					fb:activity,
					fb:add-profile-tab,
					fb:bookmark,
					fb:comments,
					fb:connect-bar,
					fb:facepile,
					fb:fan,
					fb:friendpile,
					fb:like,
					fb:like-box,
					fb:live-stream,
					fb:login,
					fb:login-button,
					fb:name,
					fb:profile-pic,
					fb:recommendations,
					fb:server-fbml,
					fb:share-button,
					fb:social-bar',
			);
			
			//Mage::dispatchEvent('itheme_tidy_config',array());
			
			$tidy = new tidy;	
			$tidy->parseString($html, $config, 'utf8');
			$tidy->cleanRepair();
			
			$matches = array();
			$doctype = preg_match('/<!DOCTYPE .+?>/', $html, $matches);
			
			$html = (string)$tidy;
			
			//closed script in same line
			$html = str_replace("\">\n</script>", "\"></script>", $html);
			
			//comments in new line
			$html = str_replace("><!--", ">\n<!--", $html);
			$html = str_replace("--><", "-->\n<", $html);

			//remove empty lines //slow regex?
			//$html = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $html);
			//$html = str_replace("\n\n", "\n", $html);
			
			//$html = preg_replace('/(.)(<!--)/',"$1\n$2",$html);
			//$html = preg_replace('/(-->)(.)/',"$1\n$2",$html);
			
			if($doctype) {
				$html = $matches[0] . "\n" . $html;
			}

		} catch (Exception $e) {
        	// If something went wrong just output the original HTML code
		}
		
		$observer->getEvent()->getTransport()->setHtml($html);
		
		//echo microtime(true) - $start;
		
	}	
	
}
