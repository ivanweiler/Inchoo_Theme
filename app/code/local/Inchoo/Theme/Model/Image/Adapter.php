<?php

/*
Name: Image class v.0.9 b1
Description: GD2 I/O and basic operations. GIF,JPEG,PNG and BMP support.
Coded by: Ivan Weiler // Scip
Notes: Alpha channels are preserved as long as they can be.
*/

class Inchoo_Theme_Model_Image {

	protected $_gd;
	protected $_info = array();

	protected $_config = array(
		'bgcolor' => array(255,255,255,0),		// white opaque // alpha: 0-127
		'compression' => 85,					// 0-100 jpeg quality
		//'alpha' => false						// preserve alpha channel; HEAVY on saving GIFs
		//'fonts_dir' =>
	);
	
	
	function __construct( $config=array() ){
		if(!function_exists('imagecreatetruecolor')) trigger_error("Image class requires GD2 support.", E_USER_ERROR);
		
		$this->fonts = dirname(__FILE__).'/Image/fonts';
		
		foreach($config as $key => $value) $this->default[$key] = $value;
		
		if(is_string($this->default['bgcolor'])) 
			$this->default['bgcolor'] = $this->hex2array($this->default['bgcolor']);
		
	}
	
	protected function checkDependencies()
	{
		
	}
	
	function open($path)
	{
		if( !($info = getimagesize($path)) ) return false;
		
		if(!in_array($info[2],array(1,2,3,6))) return false;
		
		//for save() only
		$this->info['path'] = $path;
		$types = array(1=>'gif',2=>'jpeg',3=>'png',6=>'bmp');
		$this->info['type'] = $types[$info[2]];
		$this->info['mime'] = $info['mime'] ? $info['mime'] : 'image/' . $this->info['type'];
		
		$this->info['w'] = $info[0];
		$this->info['h'] = $info[1];
		
		//$this->info['bits'] = $info['bits'];
		//$this->info['channels'] = $info['channels'];
			
		switch($this->info['type']){
			case 'gif': 
					if(!($this->gd = @ imagecreatefromgif($path))) return false;
					$this->imagepalletetotruecolor(); // force TC
					break;
			case 'jpeg':
					return $this->gd = @ imagecreatefromjpeg($path);
			case 'png':
					if(!($this->gd = @ imagecreatefrompng($path))) return false;
					$this->imagepalletetotruecolor(); // force TC for 8-bit png
					break;
			default: return false;		
		}
		return true;
	}
	
	
	function create($w,$h,$bgcolor=null)
	{
		
		if(is_string($bgcolor)) $bgcolor = $this->hex2array($bgcolor);
		elseif(!is_array($bgcolor)) $bgcolor = $this->default['bgcolor'];
		
		$this->gd = imagecreatetruecolor($w, $h);
		imagefill($this->gd, 0, 0, $this->array2color($bgcolor));
		
		$this->info['w'] = $w;
		$this->info['h'] = $h;
	}
	
	
	function setgd(&$gd)
	{
		
		if(get_resource_type($gd)!='gd') return false;
		
		$this->gd = $gd;
		$this->refresh(); // refresh info
		$this->imagepalletetotruecolor();	// always TC
		
		return true;
	}
	
	
	function saveas($type, $path='', $clear=false)
	{
		
		switch($type){
			
			case 'png': 
				imagesavealpha($this->gd,true); // force PNG transparency
				$return = imagepng($this->gd, $path, '');
				break;
			case 'jpeg':
			case 'jpg':
				$this->bgfill();
				$return = imagejpeg($this->gd, $path, $this->default['compression']);
				break;
			case 'gif':
				$this->downsamplealpha($this->gd);
				$return = imagegif($this->gd, $path, '');
				break;
			default: $return = false;
		
		}
		
		if($return and $clear) $this->clear();
		
		return $return;
	
	}
	
	
	function save($clear=false)
	{
		if(!$this->info['path']) return false;
		return $this->saveas($this->info['type'],$this->info['path'],$clear);
	}
	
	
	function display($type)
	{
		header('Content-type: image/'.$type);
		$this->saveas($type);
	}
	
	
	function & getgd(){ return $this->gd; }
	
	function info(){ return $this->info; }
	
	function width(){ return $this->info['w']; }
	
	function height(){ return $this->info['h']; }
	
	function refresh(){
		$this->info['w'] = imagesx($this->gd);
		$this->info['h'] = imagesy($this->gd);
	}
	
	
	function clear(){
		imagedestroy($this->gd);
		$this->info = array();
	}
	
	function close(){ $this->clear(); }
	
	
	/* Basic operations ************************************************************/
	
	function merge(&$img,$x=0,$y=0)
	{
	
		if(is_a($img,'Image')) $gd = & $img->getgd();
		else $gd = $img;
	
		if(	! @ imagecopy($this->gd, $gd, $x, $y, 0, 0, imagesx($gd), imagesy($gd)) ){
			return false;
		}
		
		return true;
	}
	
	
	function resize($w=0,$h=0,$proportion=true)
	{
		
		//$w = abs($w); $h = abs($h);
		
		if(!$w and !$h) return false;
		elseif(!$w and !$proportion) $w = $this->info['w'];
		elseif(!$w and  $proportion) $w = $this->info['w'] / ($this->info['h'] / $h);
		elseif(!$h and !$proportion) $h = $this->info['h'];
		elseif(!$h and  $proportion) $h = $this->info['h'] / ($this->info['w'] / $w);
		
		$temp = imagecreatetruecolor($w, $h);
		
		imagealphablending($temp , false); // transparency
		
		if(	! @ imagecopyresampled($temp, $this->gd, 0, 0, 0, 0, $w, $h, $this->info['w'], $this->info['h']) ){
			imagedestroy($temp);
			return false;
		}
		
		imagedestroy($this->gd);
		$this->gd = $temp;
		$this->info['w'] = $w;
		$this->info['h'] = $h;
		
		return true;
	}
	
	/*
	function rotate($angle){
		$temp = imagerotate($this->gd, $angle, $this->array2color($this->default['bgcolor']));
		imagealphablending($temp , false); // transparency
		imagedestroy($this->gd);
		$this->gd = $temp;
	}
	*/
	/*
	merge -> treba autosize
	copy -> kopira gd i vraca new Image->setgd()
	*/
	
	function crop($x,$y,$w,$h)
	{
		
		if( ($x+$w)>$this->info['w'] ) $w = $this->info['w']-$x;
		if( ($y+$h)>$this->info['h'] ) $h = $this->info['h']-$y;
		
		$temp = imagecreatetruecolor($w, $h);
		
		imagealphablending($temp , false); // transparency
		
		if(	! @ imagecopy($temp, $this->gd, 0, 0, $x, $y, $w, $h) ){
			imagedestroy($temp);
			return false;
		}
		
		imagedestroy($this->gd);
		$this->gd = $temp;
		$this->info['w'] = $w;
		$this->info['h'] = $h;
		
		return true;
	}
	
	
	function bgfill($bgcolor=null)
	{ // new->create->merge ?
		if(is_string($bgcolor)) $bgcolor = $this->hex2array($bgcolor);
		
		if(!is_array($bgcolor)) $bgcolor = $this->default['bgcolor'];
		
		$temp = imagecreatetruecolor($this->info['w'], $this->info['h']);
		imagefill($temp,0,0,$this->array2color($bgcolor));
		//ImageAlphaBlending ?
		imagecopy($temp, $this->gd, 0, 0, 0, 0, $this->info['w'], $this->info['h']);
		
		imagedestroy($this->gd);
		$this->gd = $temp;
	}
	
	
	function write($text, $format=array(), $x=0, $y=0, $autosize=false)
	{
		
		$format = array_merge(array('font'=>'default','size'=>12,'color'=>array(255,0,0,0),'leading'=>0,'align'=>'left','width'=>false),$format);
		
		if(is_string($format['color'])) $format['color'] = $this->hex2array($format['color']);
		
		$fontfile = $this->fonts . '/' . strtolower(basename($format['font'])) . (end(explode('.',$format['font']))!='ttf' ? '.ttf' : '');
		if(!file_exists($fontfile)) return false;
		
		$format['size'] = $format['size']*0.75; //points to pixels // x-x/4
		
		$info = imagettfbbox($format['size'],0,$fontfile,"ČĆŽŠ´jqy");
	
		$tlx=$x-$info[6]; $tly=$y-$info[7]; //BL to TL
		
		$lineheight = $info[1]-$info[7];
		
		if($format['width']>0){ 
			$text = Image::wrap($text,$format['size'],$fontfile,$format['width']);
			//echo '<pre>'; var_dump($text); die();
			$boxwidth = $format['width'];
		}else{
			$info = imagettfbbox($format['size'],0,$fontfile,$text);
			$boxwidth = $info[4] - $info[0];
		}
		$boxheight = count(explode("\n",$text))*($lineheight+$format['leading']);
		
		
		if($autosize and ( ($x+$boxwidth)>$this->info['w'] or ($y+$boxheight)>$this->info['h'] )){
			$temp = imagecreatetruecolor($x+$boxwidth, $y+$boxheight);
			imagefill($temp,0,0,$this->array2color($this->default['bgcolor']));
			imagecopy($temp, $this->gd, 0, 0, 0, 0, $this->info['w'], $this->info['h']);
			imagedestroy($this->gd);
			$this->gd = $temp;
			$this->info['w'] = $x+$boxwidth;
			$this->info['h'] = $y+$boxheight;
		}
	
		foreach(explode("\n",$text) as $linenum => $line){
			  $info = imagettfbbox($format['size'], 0, $fontfile, $line); 
			  $linewidth = $info[4] - $info[0];
				switch(strtolower(substr($format['align'],0,1))){
					case 'r': $newx = $tlx + $boxwidth - $linewidth; break;
					case 'c': $newx = $tlx + ($boxwidth/2) - ($linewidth/2); break;
					default: $newx = $tlx;
				}
				imagettftext($this->gd, $format['size'], 0, $newx, $tly+(($lineheight+$format['leading'])*$linenum), $this->array2color($format['color']), $fontfile, $line);
		}
	return true;
	}
	
	//wrap could be better!
	function wrap($text,$size,$fontfile,$width)
	{
	
		$lines = explode("\n", str_replace("\r",'',$text));
		foreach($lines as $lkey => $line){
			$temp = '';
			$words = explode(' ',$line);
			
			if(count($words)==1){ $wordwrap=true; $words = array(); for($i=0; $i<strlen($line); $i++) $words[]=$line[$i]; } 
	
			foreach($words as $wkey => $word){
					$tempinfo = imagettfbbox($size, 0, $fontfile, $temp.$word);
					$tempwidth = $tempinfo[4] - $tempinfo[0];
					
					if($tempwidth > $width){
						if(!empty($temp)) $words[$wkey]="\r\n".$word;
						$temp = $word.' ';
					}
					else{ $temp .= $word.' '; }
			} 
			$lines[$lkey] = str_replace(" \r",'',implode(' ',$words)); //fast \r hack; not good
		}
		return implode("\n", $lines);
	}
	
	
	
	
	
	/* Converters ***************************************************************/
	
	function imagepalletetotruecolor(){
		if (imageistruecolor($this->gd)) return;
		
		$temp = imagecreatetruecolor($this->info['w'],$this->info['h']);
	  
	  //preserve transparency
	  if( ($bgcolor = imagecolortransparent($this->gd)) >= 0 ){ // doesn't return real RGBA number => imagecolorsforindex()
			$bgcolor = $this->array2color(imagecolorsforindex($this->gd, $bgcolor));
			imagefill($temp,0,0,$bgcolor);
		}
		
		imagecopy($temp,$this->gd,0,0,0,0,$this->info['w'],$this->info['h']);	
		imagedestroy($this->gd);
		$this->gd = $temp;
	}
	
	function downsamplealpha()
	{
		$all_visible_colors = array();
		$alpha_coordinates = array();
		    
		for ($x = 0; $x < $this->info['w']; $x++) {
			for ($y = 0; $y < $this->info['h']; $y++) {
				
				$color = imagecolorat($this->gd, $x, $y);    
				if ( ($color >> 24) == 127 ) $alpha_coordinates[]=array($x,$y);	
				
			}
		}
		
		$this->bgfill(); // samo ako postoji alpha >0 <127 ??
	
		if(!empty($alpha_coordinates)){
	
			imagetruecolortopalette($this->gd,true,255); // 256-1
	
			// add default bgcolor to $all_visible_colors if bgfill
			$all_visible_colors[] = $this->array2color($this->default['bgcolor']);
			
			for($i=0; $i<imagecolorstotal($this->gd); $i++){
				$all_visible_colors[] = $this->array2color(imagecolorsforindex($this->gd,$i));
			}
	
			for($i=0; $i<=16777215; $i++){  // 0 - 16777215
				if(!in_array($i,$all_visible_colors)) break;
			}
	
			$rgba = $this->color2array($i);
			
			// imagecolorallocate ADDS another palette color automaticly => 256th color
			$transparent_color = imagecolorallocate($this->gd,$rgba[0],$rgba[1],$rgba[2]);
			
			foreach($alpha_coordinates as $xy) imagesetpixel($this->gd,$xy[0],$xy[1],$transparent_color);
			
			unset($alpha_coordinates);
			
			imagecolortransparent($this->gd, $transparent_color);
		
		} else {
			
			imagetruecolortopalette($this->gd,true,256);
		
		}
	}
	
	
	/* Functions *****************************************************************/
	
	function array2color($array)
	{
		$array = array_values($array); //imagecolorsforindex compatible
		$color = ($array[3] << 24) + ($array[0] << 16) + ($array[1] << 8) + $array[2];
		return $color;
	}
	
	function color2array($color)
	{
		$a = ($color >> 24) & 0xFF;
		$r = ($color >> 16) & 0xFF;
		$g = ($color >> 8) & 0xFF;
		$b = $color & 0xFF;
		return array($r, $g, $b, $a);
	}
	
	function hex2array($hex)
	{
		$length = strlen($hex);
		$color[0] = hexdec(substr($hex, $length - 6, 2));
		$color[1] = hexdec(substr($hex, $length - 4, 2));
		$color[2] = hexdec(substr($hex, $length - 2, 2));
		$color[3] = 0;
		return $color;
	}
	
} // ENFOF Image;



/*

//$test = new Image(array('bgcolor' => array(255,0,0,0)));

$test = new Image();

$test->open('test.jpg');

//$test->open('BIG.bmp');

//$test->create(100,100,array(0,255,0));


//$test->crop(0,0,100,100);

//$test->resize(150,150);

//$test->save();
//$test->saveas('png','test2.png');

//$test->bgfill('#ff0000');

//$test->grayscale();

$test2 = new Image();
$test2->open('star.png');
//$test_gd = & $test2->getgd();

$test->merge($test2,-10,0);

$test->display('png');


*/




