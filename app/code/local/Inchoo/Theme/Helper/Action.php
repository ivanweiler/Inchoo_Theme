<?php
class Inchoo_Theme_Helper_Action extends Mage_Core_Helper_Abstract
{

	/*
	 * needs to be invite/customer_account/view
	 * invite_customer_account_view .. what is what, same?? layout is!
	 * 
	 * catalog => catalog/index/index => catalog_index_index
	 * catalog/* /* => catalog_x_y_z
	 * 
	 * inchoo_theme => how to know if theme is router or controller, 
	 * we won't be appending index_index, only replace *
	 * 
	 */
	
	public function is($actions)
	{
		$_action = Mage::app()->getFrontController()->getAction();
		$actions = (array)$actions;
		
		foreach($actions as $action) {

			//$action = strtolower(str_replace('/', '_', $action));
			
			$action = strtolower($action);

			if(strpos($action,'/')) {
				$rca = explode('/',$action);
				
				if($rca[0]=='*') {
					$rca[0] = $_action->getRequest()->getRequestedRouteName();
				}
				
				if(!isset($rca[1]) || $rca[1]=='*') {
					$rca[1] = $_action->getRequest()->getRequestedControllerName();
				}

				if(!isset($rca[2]) || $rca[2]=='*') {
					$rca[2] = $_action->getRequest()->getRequestedActionName();
				}
				
				$action = implode('_',$rca);
			}
			
			/*
			if(substr_count($action,'*')==2) {
				$action = str_replace('*', $_action->getRequest()->getRequestedControllerName(), $action);
			}
			$action = str_replace('*', $_action->getRequest()->getRequestedActionName(), $action);
			*/
			
			//var_dump($action);
			
			if($action == strtolower(Mage::app()->getFrontController()->getAction()->getFullActionName())) {
				return true;
			}
			
			/*
			if(!strpos($action,'/')) {
				continue;
			}
			
			$rca = explode('/*',$action);
			
			if(count($rca)<3) {
				$rca = array_merge($rca,array_fill(0, 3-count($rca), 'index'));
			}
			*/

		}

		return false;
	}
	
    public function isHomePage()
    {
    	$urlModel = Mage::getModel('core/url');
        return $urlModel->getUrl('') == $urlModel->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
	
	public function isCmsPageView($identifier='')
	{
		if(!$this->is('cms/page/view')) {
			return false;
		}
		
		if(!$identifier) {
			return true;
		}
		
		if(is_numeric($identifier)) {
			return Mage::getSingleton('cms/page')->getId() == $identifier;
		} elseif(is_string($identifier)) {
			return Mage::getSingleton('cms/page')->getIdentifier() == $identifier;
		} else {
			return false;
		}
	}
	
	public function isCatalogProductView($identifier='')
	{
		if(!$this->is('catalog/product/view')) {
			return false;
		}		
		
		if(!$identifier) {
			return true;
		}
		
		if(!Mage::registry('current_product') instanceof Mage_Catalog_Model_Product) {
			return false; //no-route anyway ?
		}
		
		if(is_numeric($identifier)) {
			return Mage::registry('current_product')->getId() == $identifier;
		} elseif(is_string($identifier)) {
			return Mage::registry('current_product')->getUrlKey() == $identifier;
		} else {
			return false;
		}		
	}
	
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

	
	public function __call($method, $args)
	{
		if('is' !== substr($method, 0, 2)) {
			throw new Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
		}
		
		$action = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", substr($method, 2)));
		return $this->is($action);
	}

	
}
