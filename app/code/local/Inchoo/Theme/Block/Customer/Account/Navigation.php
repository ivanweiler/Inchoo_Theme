<?php
class Inchoo_Theme_Block_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
	
	protected $_sortOrder = array();
	protected $_lastSortOrder = 0;

    public function addLink($name, $path, $label, $urlParams=array(), $sortOrder=0)
    {
    	if(!$sortOrder) {
    		$this->_lastSortOrder+=10;
    		$sortOrder = $this->_lastSortOrder;
    	}
		
    	$this->_sortOrder[$name] = $sortOrder;
        
		return parent::addLink($name, $path, $label, $urlParams);
    }
    
    public function removeLink($name)
    {
    	if(isset($this->_links[$name])) {
    		unset($this->_links[$name]);
    	}
    	return $this;
    }
    
    public function setSortOrder($name,$sortOrder)
    {
    	$this->_sortOrder[$name] = $sortOrder;
    }
    
    public function getLinks()
    {
    	usort($this->_links, array($this, '_sort'));
        return $this->_links;
    }
    
    protected function _sort($a, $b)
    {
        return (int)$this->_sortOrder[$a->getName()] < (int)$this->_sortOrder[$b->getName()] ? -1 : ((int)$this->_sortOrder[$a->getName()] > (int)$this->_sortOrder[$b->getName()] ? 1 : 0);
    }
	
}