<?php
class Inchoo_Theme_Helper_Image extends Mage_Catalog_Helper_Image
{
	
	protected $_scheduleCrop = false;

    protected function _reset()
    {
    	$this->_scheduleCrop = false;
		parent::_reset();
        return $this;
    }
    
	public function load($model, $attributeName=null, $imageFile=null)
    {
		
    	//var_dump($model->debug()); die();
		
    	$this->_reset();
        $this->_setModel(Mage::getModel('inchoo_theme/image'));
        $this->_getModel()->setSourceFromModel($model);
        
         //left to minify code change and to use product placeholders
        if(!$attributeName) {
        	$attributeName = 'image';
        }
        $this->_getModel()->setDestinationSubdir($attributeName);

        $this->setWatermark(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image"));
        $this->setWatermarkImageOpacity(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity"));
        $this->setWatermarkPosition(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position"));
        $this->setWatermarkSize(Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size"));

        
		if(!$imageFile) {
            $imageFile = $this->_getModel()->getSource()->getImage($attributeName);
        }
		
        if(!$imageFile) {
        	$imageFile = '/no_selection';
        }
        
        $this->setImageFile($imageFile);
    	
        //??
        $this->_getModel()->setBaseFile($this->getImageFile());
        
        return $this;
    	
    }
	
    public function crop($top, $bottom, $right, $left)
    {
        $this->_getModel()->setCrop(array($top, $bottom, $right, $left));
        $this->_scheduleCrop = true;
        return $this;
    }
    
    public function thumb($width, $height)
    {
    	$this->keepFrame(true);
    	$this->resize($width, $height);
    	return $this;
    }
    
    public function cthumb($w, $h)
    {
    	//calculate all here
    	
		$originalWidth = $this->_getModel()->getImageProcessor()->getOriginalWidth();
		$originalHeight = $this->_getModel()->getImageProcessor()->getOriginalHeight();
		
	    if($originalWidth <= $w && $originalHeight<= $h) {
	    	
	    	return $this->thumb($w,$h);
	    
	    } elseif($originalWidth <= $w) {	//crop height

			$this->crop(($originalHeight-$h)/2, ($originalHeight-$h)/2, 0, 0);
			return $this->thumb($w,$h);
			
		} elseif($originalHeight <= $h) {	//crop width
			//$top, $bottom, $right, $left

			$this->crop(0, 0, ($originalWidth-$w)/2, ($originalWidth-$w)/2);
			return $this->thumb($w,$h);
			
		} else {	//crop width & height

			$this->keepFrame(false);
			
			$cropHeight = round($originalWidth * ($h / $w));
			$cropWidth = round($originalHeight * ($w / $h));
				
			if($originalWidth-$cropWidth < $originalHeight-$cropHeight) { //echo 2; die();
				
				//$cropHeight = round($originalWidth * ($h / $w));
				$this->crop(($originalHeight-$cropHeight)/2, ($originalHeight-$cropHeight)/2, 0, 0);
				$this->resize($w,$h);

				/*
				$frameHeight = round($w * ($originalHeight / $originalWidth));
				$this->resize($w,$frameHeight);
				$this->crop(($frameHeight-$h)/2, ($frameHeight-$h)/2, 0, 0);
				*/
				
			} else { //echo 3; die();

				//$cropWidth = round($originalHeight * ($w / $h));
				$this->crop(0, 0, ($originalWidth-$cropWidth)/2, ($originalWidth-$cropWidth)/2);
				$this->resize($w,$h);

				/*
				$frameWidth = round($h * ($originalWidth / $originalHeight));
				$this->resize($frameWidth,$h);
				$this->crop(0, 0, ($frameWidth-$w)/2, ($frameWidth-$w)/2);
				*/
			}
			
		}
		
		return $this;
    	
    }
    
    public function __toString()
    {
        try {
            if( $this->getImageFile() ) {
                $this->_getModel()->setBaseFile( $this->getImageFile() );
            } else {
                $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
            }

            if( $this->_getModel()->isCached()/* && false*/) {
                return $this->_getModel()->getUrl();
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }
                
				//added
				if( $this->_scheduleCrop ) {
                    $this->_getModel()->crop();
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }
                

                if( $this->getWatermark() ) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()->saveFile()->getUrl();
            }
        } catch( Exception $e ) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
	
}