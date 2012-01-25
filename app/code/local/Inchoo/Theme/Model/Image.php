<?php
class Inchoo_Theme_Model_Image extends Mage_Catalog_Model_Product_Image
{
	
	protected $_source;
	protected $_crop;
	
	protected $_supportedSources = array(
		'catalog/product',
		'catalog/category',
		'enterprise_catalogevent/event',
		'blog/post',
		'blog/blog'
	);
	
	public function setCrop($crop)
	{
		$this->_crop = $crop;
		return $this;
	}
	
    public function crop()
    {
		$this->getImageProcessor()->crop($this->_crop[0], $this->_crop[1], $this->_crop[2], $this->_crop[3]);
        return $this;
    }
    
    
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;

        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        
        //$baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
        $baseDir = $this->getSource()->getBaseDir();

        if ('/no_selection' == $file) {
            $file = null;
        }
        if ($file) {
            if ((!file_exists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        
        if (!$file) {
            // check if placeholder defined in config
            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
            if ($isConfigPlaceholder && file_exists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            }
            else {
                // replace file with skin or default skin placeholder
                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $baseDir = $skinBaseDir;
                }
                else {
                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                    if (!file_exists($baseDir . $file)) {
                        $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
                    }
                }
            }
            $this->_isBaseFilePlaceholder = true;
        }

        
        $baseFile = $baseDir . $file;

        if ((!$file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
        }

        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(
            //Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath(),
            $this->getSource()->getBaseDir(),
            'cache',
            Mage::app()->getStore()->getId(),
            $path[] = $this->getDestinationSubdir()
        );
        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";    
         
        // add misk params as a hash
        $miscParams = array(
                ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
                ($this->_keepFrame        ? '' : 'no')  . 'frame',
                ($this->_keepTransparency ? '' : 'no')  . 'transparency',
                ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
                $this->_rgbToString($this->_backgroundColor),
                'angle' . $this->_angle,
                'quality' . $this->_quality
        );
        
        if(!empty($this->_crop)) {
        	$miscParams[] = implode('-',$this->_crop);
        }

        // if has watermark add watermark params to hash
        /*
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }
		*/
        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->_newFile = implode('/', $path) . $file; // the $file contains heading slash

        return $this;
    }
    
   /**
    * Needed because Varien_Image::crop() is broken, 
    * remove this and custom adapter when fixed
    * http://www.magentocommerce.com/bug-tracking/issue?issue=6989
    */
    //@todo: It looks fixed in Magento>=1.6, test and remove this
    public function getImageProcessor()
    {
    	if( !$this->_processor ) {
            $this->_processor = new Inchoo_Theme_Model_Image_Adapter_Gd2($this->getBaseFile());
        }
        
        return parent::getImageProcessor();
    }
    
    
    public function setSourceFromModel($model)
    {
		foreach($this->_supportedSources as $source) {
			$className = Mage::getConfig()->getModelClassName($source);
			if($model instanceof $className) {
				//$this->_setSource($source,$model);
				$this->_source = Mage::getModel('inchoo_theme/image_source_'.str_replace('/','_',$source),$model);
				break;
			}
    	}
    }
    
    public function getSource()
    {
    	return $this->_source;
    }
    
    
    public function clearCache()
    {
    	foreach($this->_supportedSources as $source) {
    		$source = Mage::getModel('inchoo_theme/image_source_'.str_replace('/','_',$source));
    		$source->clearCache();
    	}
    }
	
	
}