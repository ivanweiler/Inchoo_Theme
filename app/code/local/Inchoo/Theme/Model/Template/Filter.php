<?php
class Inchoo_Theme_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
	
    public function __construct()
    {
        parent::__construct();
        
        $currentCustomer = Mage::getSingleton('customer/session')->getCustomer();
		$currentCustomer->setIsLoggedIn(Mage::getSingleton('customer/session')->isLoggedIn());
        
        $this->setVariables(array(
        	//'store' => Mage::app()->getStore(),
        	'customer' => $currentCustomer
        ));

    }	

	
}