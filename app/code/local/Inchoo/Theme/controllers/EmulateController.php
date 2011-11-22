<?php
class Inchoo_Theme_EmulateController extends Mage_Core_Controller_Front_Action //Mage_Adminhtml_Controller_Action
{
    	
    public function checkoutSuccessAction()
    {
    	$this->authAdmin('all');
    	
    	$orderIncrementId = $this->getRequest()->getParam('id', false);
    	$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
    	
    	if($order->getId()) {
	    	Mage::getSingleton('checkout/session')
	    		->setLastOrderId($order->getId())
	    		->setLastSuccessQuoteId($order->getQuoteId())
	    		->setLastQuoteId($order->getQuoteId());
			
			//$this->_forward('checkout/onepage/success');
			
	    	$this->loadLayout(array('default','checkout_onepage_success'));
			$this->renderLayout();
    	
    	} else {
    		Mage::getSingleton('core/session')->addError('Order doesn\'t exist.');
    		$this->_forward('noRoute');
    	}

    }
    
    public function customerAction()
    {
    	$this->authAdmin('all');
    	
    	$customerId = $this->getRequest()->getParam('id', false);
		$customer = Mage::getModel('customer/customer')->load($customerId);
		
		if($customer->getId()) {
			try {
				Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
				$this->_redirect('customer/account/');
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_forward('noRoute');
			}
		} else {
			Mage::getSingleton('core/session')->addError('Customer doesn\'t exist.');
			$this->_forward('noRoute');
		}
		
    }
    
    public function authAdmin($path)
    {
        list($username, $password) = $this->authValidate();
        
		$user = Mage::getModel('admin/user')->login($username, $password);
		if(!$user->getId() || $user->getIsActive()!='1') {
			$this->authFailed();
		}

		$acl = Mage::getResourceModel('admin/acl')->loadAcl();

		$resource = 'admin/'.$path;
		$allowed = false;
		try {
			$allowed = $acl->isAllowed($user->getAclRole(), $resource);
		} catch (Exception $e) {
			try {
				if (!$acl->has($resource)) {
					$allowed = $acl->isAllowed($user->getAclRole(), null);
				}
			} catch (Exception $e) { }
		}
	
		if(!$allowed) {
			$this->authFailed();
		}
    }
    
    public function authValidate()
    {
        $userPass = Mage::helper('core/http')->authValidate();
        return $userPass;
    }  

    public function authFailed()
    {
        Mage::helper('core/http')->authFailed();
    }    
    
}
