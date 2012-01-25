<?php
class Inchoo_Theme_TestController extends Mage_Core_Controller_Front_Action
{
	
    public function preDispatch()
    {
		$this->setFlag('', self::FLAG_NO_DISPATCH, true);
        return parent::preDispatch();
    }	
	
	public function indexAction()
	{
		echo "Controller test ;)";
	}
	
	public function test1Action()
	{
		//$top=0, $bottom=0, $right=0, $left=0
		
		$category = Mage::getModel('catalog/category')->load(10);
		echo Mage::helper('inchoo_theme/image')
			->load($category)
			//->crop(10,10,20,0)
			//->resize(200,200)
			->cthumb(285,180)
			;
	}	
	
	public function test2Action()
	{
		$product = Mage::getModel('catalog/product')->load(166);
		echo Mage::helper('inchoo_theme/image')->load($product)->resize(200,200);
	}
	
	public function test3Action()
	{
		$category = Mage::getModel('catalog/category')->load(10);
		echo Mage::helper('inchoo_theme/image')
			->load($category)
			//->resize(200,200)
			->cthumb(500,300)
			;
		
	}
	
	public function test4Action()
	{
		var_dump(Mage::helper('inchoo_theme/action')->is('inchoo_theme/test/*'));
		var_dump(Mage::helper('inchoo_theme/action')->isInchooThemeTestTest4());
	}
	
	public function test5Action()
	{
		echo Mage::helper('itheme/datetime')->format('2008-07-09 10:13:41');
		echo '<br />';
		echo Mage::helper('itheme/datetime')->format(now());
		echo '<br />';
		echo Mage::helper('itheme/datetime')->format(strtotime(now()));
		echo '<br />';
		echo Mage::helper('itheme/datetime')->format(now(),'php','l jS \of F Y h:i:s A');
	}	
	
	
}