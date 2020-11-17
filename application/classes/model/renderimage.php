<?php defined('SYSPATH') or die('No direct script access.');
class Model_renderimage extends Model {
	public $data;
	public $image;
	public $side;
	
	public function set_data($data, $action, $fileName = null){
		$this->data = $data;
		
		$images_names = array();
		switch($action){
			//render small preview images for both sides
			case "preview":
				for($i = 1; $i <= 2; $i++){
					$this->side = "side".$i;
					//create image
					$this->create_image();
					
					//add background image or fill with color
					$this->add_background();
					
					//add text
					$this->add_text();
					
					//create path
					$path = "temp/".str_replace(array(" ", "."), "", microtime()).".jpg";
					
					//add aditional images
					$this->add_image();
					
					//add horizontal/vertical lines
					$this->add_lines($data['dimensions']['width'], $data['dimensions']['height']);

					//render and resize it by 2.02
					$images_names[] = $this->render(2.02, $path);
				}
			break;
			
			case "preset":
				$this->side = "side1";
				//create image
				$this->create_image();
				
				//add background image or fill with color
				$this->add_background();
				
				//add text
				$this->add_text();
				
				//add aditional images
				$this->add_image();

				//add horizontal/vertical lines
				$this->add_lines($data['dimensions']['width'], $data['dimensions']['height']);
				
				$path_full = "media/images/designs/preview/full_size/" . $fileName . "_" . str_replace(array(" ", "."), "", microtime()) . ".jpg";
				$path_thumb = "media/images/designs/preview/thumb/". $fileName . "_" . str_replace(array(" ", "."), "", microtime()) . ".jpg";
				
				$images_names[] = $this->render(1.5, $path_full);
				$images_names[] = $this->render(3, $path_thumb);
			break;
			
			case 'save_preset_preview':
				$this->side = "side1";
				//create image
				$this->create_image();
				
				//add background image or fill with color
				$this->add_background();
				
				//add text
				$this->add_text();
				
				//add horizontal/vertical lines
				$this->add_lines($data['dimensions']['width'], $data['dimensions']['height']);

				//create path
				$path = "temp/".str_replace(array(" ", "."), "", microtime()).".jpg";
				
				//render and resize it by 2.02
				$images_names[] = $this->render(2.02, $path);
			break;
			
			case 'download':
				for($i = 1; $i <= 2; $i++){
					$this->side = "side".$i;;
					
					//create image
					$this->create_image();
					
					//add background image or fill with color
					$this->add_background();
					
					//add text
					$this->add_text();
					
					//add aditional images
					$this->add_image();
					
					//add horizontal/vertical lines
					$this->add_lines($data['dimensions']['width'], $data['dimensions']['height']);
					
					$width = $data['dimensions']['width'] / 5.7 * 2.8;
					$height = $data['dimensions']['height'] / 5.7 * 2.8;
					
					$image = imagecreatetruecolor($width, $height);
					
					imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, $data['dimensions']['width'], $data['dimensions']['height']);
					
					$this->image = $image;
					
					if($i == 1)
						$first_page = $this->create_cards_set($width, $height);
					else
						$second_page = $this->create_cards_set($width, $height);
				}
				
				$final_width = imagesx($first_page);
				
				//create new image
				$final_image = imagecreatetruecolor($final_width,  imagesy($first_page) + imagesy($second_page));
				
				imagecopyresampled($final_image, $first_page, 0, 0, 0, 0, $final_width, imagesy($first_page), imagesx($first_page), imagesy($first_page));
				imagecopyresampled($final_image, $second_page, 0, imagesy($first_page), 0, 0, $final_width, imagesy($first_page), imagesx($first_page), imagesy($first_page));
				
				//add seperating line
				imageline($final_image, 0, imagesy($first_page), imagesx($first_page), imagesy($first_page), imagecolorallocate($final_image, 255, 0, 0));
				
				$images_names = imagejpeg($final_image, null, 100);
			break;
			
		}
		
		return $images_names;
	}
	
	public function create_cards_set($width, $height){
		$paper_width = 595;
		$paper_height = 842;
		
		$paper = imagecreatetruecolor($paper_width, $paper_height);
		
		$this->add_bg_color(255, 255, 255, $paper);
		
		$cards_count = floor($paper_width / $width) * floor($paper_height / $height);
		
		$x = 0;
		$y = 0;
		$new_row = false;
		
		//get how many cards goes in row/colum
		$per_row = floor($paper_width / $width);
		$per_column = floor(($paper_height ) / $height);
		
		//get margins
		$margin_x = floor( ($paper_width - ($width * $per_row) - (($per_row - 1) * 10))  / 2);
		$margin_y = floor( ($paper_height - ($height * $per_column) - (($per_column - 1) * 10))  / 2);
		
		for($i = 1; $i <= $cards_count; $i++){
			
			//get x
			if($i == 1){
				$x = $margin_x;
			}elseif((($width * 2) + $x) + 10 < $paper_width){
				$x = ($x + $width) + 10;
				$new_row = false;
			}else{
				$x = $margin_x;
				$new_row = true;
			}
			
			//get y
			if($i == 1)
				$y = $margin_y;
			elseif((($height * 2) + $y) + 10 < $paper_height && $new_row == true)
				$y = ($y + $height) + 10;
			
			imagecopyresampled($paper, $this->image, $x, $y, 0, 0, $width, $height, $width, $height);
		}
		
		return $paper;
	}
	
	public function add_lines($width, $height){
		if($this->side == "side2") return;
		
		for($i = 1; $i <= 2; $i++){
			//get line position
			if($i == 2) $line = "verticalLine"; else $line = "horizontalLine";
			foreach($this->data[$this->side][$line] as $key => $value){
			
				if($key == "default" && $key != "0") continue;
				
				//check if color is in hex since IE returns hex insted of rgb
				if(preg_match("/#/i", $value['color']) > 0)
					$color = $this->hex_to_rgb($value['color']);
				else
					$color = explode(",", str_replace(array("rgb(", ")", " "), "", $value['color']));

				//create color
				$line_color = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
				
				//horizontal lines
				if($i == 1){
					$x = 0;
					$y = $value['top'];
					$x2 = $width;
					if($value['thickness'] > 1) 
						$y2 = $value['top'] + $value['thickness'] -1;
					else
						$y2 = $value['top'];
				}else{
					$x = $value['left'];
					$y = 0;
					if($value['thickness'] > 1) 
						$x2 = $value['left'] + $value['thickness'] -1;
					else
						$x2 = $value['left'];
					$y2 = $height;
					
				}
				
				if($value['style'] == "dashed"){
									
					if($i == 1){
						$spaces = $width / 2;
						$x = 0;
						$x2 = 0;
					}else{
						$spaces = $height / 2;
						$y = 0;
						$y2 = 0;
					}
					
					for($i2 = 1; $i2 <= $spaces; $i2++){
						if($i == 1){	
							$x = $x2 + 7;
							$x2 = $x2 + 12;
						}else{
							$y = $y2 + 7;
							$y2 = $y2 + 12;					
						}

						imageline($this->image, $x, $y, $x2, $y2, $line_color);
					}
					
				}else{
					imagefilledrectangle($this->image, $x, $y, $x2, $y2, $line_color);
				}
				
			}
		}
	}
	
	public function create_image(){
		//create image
		$this->image = imagecreatetruecolor($this->data['dimensions']['width'], $this->data['dimensions']['height']);
	}
	
	public function add_image(){
	
		foreach($this->data[$this->side]['floatingImages'] as $key => $value){
			
			//skip any empty values
			if(empty($value['path'])) continue;
			
			$img = imagecreatefromjpeg($value['path']);
			list($originalWidth, $originalHeight, $type, $attr) = getimagesize($value['path']);
			
			imagecopyresized($this->image, $img, $value['left'] - 47, $value['top'] - 52, 0, 0, $value['width'], $value['height'], $originalWidth, $originalHeight);

		}
	}

	public function add_background(){
		//replace pattern
		$replace = array("'", "rgb(", ")", "none repeat scroll 0% 0%", " ", '"');

		//if there is ``url`` in string it means user have set img as bg
		if(preg_match("/url/i", $this->data[$this->side]['background']) > 0){
			//create path
			$extract_name = explode("/", str_replace($replace, "", preg_replace("/.+s[\/]/", "", $this->data[$this->side]['background'])));
			$path = "media/images/designs/backgrounds/" . $extract_name[0] . "/" . $extract_name[1];
			
			//load that image
			$img = imagecreatefromjpeg($path);
			//get image data
			list($originalWidth, $originalHeight, $type, $attr) = getimagesize($path);
			//add it to the card
			imagecopyresized($this->image, $img, 0, 0, 0, 0, $this->data['dimensions']['width'], $this->data['dimensions']['height'], $originalWidth, $originalHeight);
		}else{

			//check if color is in hex since IE returns hex insted of rgb
			if(preg_match("/#/i", $this->data[$this->side]['background']) > 0)
				$bgColor = $this->hex_to_rgb($this->data[$this->side]['background']);
			else if(  $this->data[$this->side]['background'] == "none")
				$bgColor = array(255, 255, 255);
			else
				$bgColor = explode(",", str_replace(array("rgb(", ")", " "), "",   $this->data[$this->side]['background']) );
				
			//add bg
			$this->add_bg_color($bgColor[0], $bgColor[1], $bgColor[2], $this->image);
		}
	}
	
	public function add_bg_color($red, $green, $blue, $src){
		imagefill($src, 0, 0, imagecolorallocate($src, $red, $green, $blue));
	}
	
	public function add_text(){
		//set text data
		$textPositions = $this->data[$this->side]['textPositions'];
		$textStyle = $this->data[$this->side]['textProperties'];
		
		foreach($textPositions as $key => $array){

			//skip comments
			if($key == "__comment") continue;
			
			//check if color is in hex since IE returns hex insted of rgb
			if(preg_match("/#/i", $textStyle[$key]['color']) > 0)
				$color = $this->hex_to_rgb($textStyle[$key]['color']);
			else
				$color = explode(",", str_replace(array("rgb(", ")", " "), "", $textStyle[$key]['color']));
			
			$getFontName = explode(",", $textStyle[$key]['font-family']);
			
			//since every font name is in lower case we should rename font also
			$fontName = "media/fonts/".strtolower(str_replace("'", "", str_replace(" ", "-", $getFontName[0])));
			
			$fontWeight = $textStyle[$key]['font-weight'];
			$fontStyle = $textStyle[$key]['font-style'];
			$fontSize = (intval($textStyle[$key]['font-size']));
			
			//get right font
			if($fontWeight == 700 || $fontWeight == "bold")
				$fontName = $fontName . "-bold";
			if($fontStyle == "italic")
				$fontName = $fontName . "-italic";
			
			$fontName = $fontName.".ttf";
			
			//offset
			$x = floor(intval($textPositions[$key]['left']) + 24);
			$y = floor($textPositions[$key]['top'] + 24 + intval($textStyle[$key]['font-size']));
			
			//skip any null values
			if(!isset( $array['left']) || !isset( $array['top']) || !isset( $array['text']) ) continue;

			//set text color
			$txtColor =  imagecolorallocate($this->image, $color[0], $color[1], $color[2]);

			//check if text is with underline
			if($textStyle[$key]['text-decoration'] == "underline"){
				if(isset($textPositions[$key]['width']))
					imageline($this->image, $x, $y + 3, $x + $textPositions[$key]['width'], $y + 3, $txtColor);
				else
					var_dump($textPositions);
			}
			
			imagettftext($this->image, $fontSize, 0, $x, $y, $txtColor, $fontName, $array['text']);
		}
	}

	
	//convert hex to rgb
	function hex_to_rgb($color){
		if ($color[0] == '#')
			$color = substr($color, 1);

		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1],
									 $color[2].$color[3],
									 $color[4].$color[5]);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else
			return false;

		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

		return array($r, $g, $b);
	}
	
	//first we gonna render it at full size and after that resize it
	//this way we can keep better resolution
	public function render($resize_by, $path){
		//get temp names
		$name = "temp/".str_replace(array(" ", "."), "", microtime()).".jpg";
		
		//save full size image
		imagejpeg($this->image, $name, 100);
		
		//now resize
		Image::factory($name)
			->resize($this->data['dimensions']['width'] / $resize_by, null)
			->save($path);

		//delete full size img
		unlink($name);
		
		return $path;
	}
		
}
?>