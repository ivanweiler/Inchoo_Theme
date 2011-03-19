<?php
abstract class Inchoo_Theme_Model_Image_Source_Abstract
{
	protected $_model;
	
	public function __construct($model)
	{
		$this->_model = $model;
	}
	
	abstract public function getBaseDir();
	
	abstract public function getImage($attributeName=null);
	
	/*
	public function getPlaceholderFile($file)
	{
		
	}
	
	*/
	
	public function clearCache()
	{
		$directory = $this->getBaseDir().DS.'cache'.DS;
        $io = new Varien_Io_File();
        $io->rmdir($directory, true);
	}
	
}