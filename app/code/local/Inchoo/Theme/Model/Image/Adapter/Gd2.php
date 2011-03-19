<?php
class Inchoo_Theme_Model_Image_Adapter_Gd2 extends Varien_Image_Adapter_Gd2
{
	
    function __construct($fileName=null)
    {
		$this->checkDependencies();

        if( !file_exists($fileName) ) {
            throw new Exception("File '{$fileName}' does not exists.");
        }

        $this->open($fileName);
    }
    
    public function crop($top=0, $bottom=0, $right=0, $left=0)
    {
        if( $left == 0 && $top == 0 && $right == 0 && $bottom == 0 ) {
            return;
        }

        $newWidth = $this->_imageSrcWidth - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top - $bottom;

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
		
        imagealphablending($canvas , false); 
        
        imagecopy($canvas, $this->_imageHandler, 0, 0, $left, $top, $newWidth, $newHeight);
        
        $this->_imageHandler = $canvas;
        
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }    
    
	
}