<?php defined('SYSPATH') or die('No direct script access.');
class Model_renderimage extends Model {
	public $image;
	public $width;
	public $height;
	
	public function create_image($width, $height){
		$this->image = imagecreatetruecolor($width, $height);
		$this->width = $width;
		$this->height = $height;
	}
	
	public function add_bg_color($red, $green, $blue){
		//get color
		$getColor =  imagecolorallocate($this->image, $red, $green, $blue);
		
		//fill image with this new color
		imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $getColor);
	}
	
	public function add_bg_image($path){
		$img = imagecreatefromjpeg($path);
		imagecopy($this->image, $img, 0, 0, 0, 0, $this->width, $this->height);
	}
	
	public function add_image($path, $x, $y, $width, $height){
		$img = imagecreatefromjpeg($path);
		list($originalWidth, $originalHeight, $type, $attr) = getimagesize($path);
		
		imagecopyresized($this->image, $img, $x, $y, 0, 0, $width, $height, $originalWidth, $originalHeight);
	}
	
	public function add_text($string, $x, $y, $color, $fontSize, $underLine, $fontName){
		//get color
		$txtColor =  imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
		
		imagettftext($this->image, $fontSize, 0, $x, $y, $txtColor, $fontName, $string);
		
		//draw underline
		if($underLine){
			$underLine = imagettfbbox($fontSize, 0, $fontName, $string);
			imageline($this->image, $x, $y + 2, $x + $underLine[2], $y + 2, $txtColor);
		}
	}
	
	public function draw_line( $x1, $y1, $x2, $y2, $color){
		$color = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
		
		imageline($this->image, $x1, $y1, $x2, $y2, $color);
	}
		
	public function render_image($path){
		
		return imagejpeg($this->image,  $path, 100);
		
		imagedestroy($this->image);
	}
}
