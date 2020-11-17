<?php defined('SYSPATH') or die('No direct script access.');
class Model_renderpdf extends Model {
	public $data;
	public $pdf;
	public $card_width;
	public $card_height;
	public $page_width;
	public $page_height;
	public $side;
	public $x; //x, y position for the card, this gets changed every time new card gets added
	public $y;
	public $i = 1;
	public $new_row = true;
	public $cards_per_page;
	public $new_page = false;
	public $per_row;
		
	public function set_data($data){
		//set data
		$this->data = $data;
		
		//set card dimensions
		$this->card_width = $data['dimensionsInMM']['width'][0];
		$this->card_height = $data['dimensionsInMM']['height'][0];
		
		//set page dimensions
		$this->page_width = 210;
		$this->page_height = 297;
				
		//create page
		$pdf = Model::factory("fpdf");
		$pdf->FPDF('P','mm','A4');
		$pdf->fontpath = "media/fonts/pdf/";
		$this->pdf = $pdf;
		
		for($x = 1; $x <= 2; $x++){
			$this->new_page = true;
			//set side
			$this->side = "side".$x;
			
			//add page
			$this->add_page();
			
			//get total cards per page
			$this->cards_per_page = floor(floor($this->page_width / $this->card_width) * floor($this->page_height / $this->card_height));
			
			//add cards
			for($i = 1; $i <= $this->cards_per_page; $i++){
			
				//get offset for each card
				$offset = $this->get_offset($i);
				
				//add card bg
				$this->add_bg();
				
				//add text
				$this->add_text();
				
				//add lines
				$this->add_cut_line();
				
				//add aditional images
				$this->add_image();
				
				//add horizontal/vertical line
				$this->add_lines();
			}
			
		}
	}
		
	public function get_offset($card_nr){
		$this->i = $card_nr;
		
		$per_row = floor($this->page_width / ($this->card_width + 5));
		$per_colum = floor($this->page_height / ($this->card_height + 5));
	
		$this->per_row = $per_row;
		
		//get margins
		$margin_x = floor( ($this->page_width - ($this->card_width * $per_row) ) / 2 );
		$margin_y = floor( ($this->page_height - ($this->card_height * $per_colum) ) / 2 );


		//get x
		if($this->new_page){
			$this->x = $margin_x;
		}elseif($this->x + ($this->card_width * 2) + 3  < $this->page_width){
			$this->x = ($this->x + $this->card_width) + 2;
			$this->new_row = false;
		}else{
			$this->x = $margin_x;
			$this->new_row = true;
		}
		
		//get y
		if($this->new_page){
			$this->y = $margin_y;
			$this->new_page = false;
		}elseif(($this->y + ($this->card_height * 2)) + 3 < $this->page_height && $this->new_row == true){
			$this->y = ($this->y + $this->card_height) + 2;
		}else{
		}
	}
	
	public function add_bg(){
				//replace pattern
		$replace = array("'", "rgb(", ")", "none repeat scroll 0% 0%", " ", '"');

		//if there is ``url`` in string it means user have set img as bg
		if(preg_match("/url/i", $this->data[$this->side]['background']) > 0){
			//create path
			$extract_name = explode("/", str_replace($replace, "", preg_replace("/.+s[\/]/", "", $this->data[$this->side]['background'])));
			$path = "media/images/designs/backgrounds/" . $extract_name[0] . "/" . $extract_name[1];
			
			//set img
			$this->pdf->Image($path ,$this->x, $this->y, $this->card_width, $this->card_height);
		}else{

			//check if color is in hex since IE returns hex insted of rgb
			if(preg_match("/#/i", $this->data[$this->side]['background']) > 0)
				$bgColor = Model::factory("renderimage")->hex_to_rgb($this->data[$this->side]['background']);
			else if( $this->data[$this->side]['background'] == "none")
				$bgColor = array(255, 255, 255);
			else
				$bgColor = explode(",", str_replace($replace, "",   $this->data[$this->side]['background']) );
			//make bg
			$this->pdf->SetFillColor($bgColor[0], $bgColor[1], $bgColor[2]);
			
			$this->pdf->Rect($this->x, $this->y, $this->card_width, $this->card_height, "F");
			
		}
	}
	
	public function add_page(){
		$this->pdf->AddPage();
		$this->new_row = true;
	}
	
	public function add_lines(){

		for($i = 1; $i <= 2; $i++){
			if($i == 1) $position = "horizontalLine"; else $position = "verticalLine";
			
			$data = $this->data[$this->side][$position];
			
			foreach($this->data[$this->side][$position] as $key => $value){
				//skip default line
				if($key == "default" && $key != "0") continue;
				
				
				//get color
				if(preg_match("/#/i", $data[$key]['color']) > 0)
					$color = Model::factory("renderimage")->hex_to_rgb($data[$key]['color']);
				else
					$color = explode(",", str_replace(array("rgb(", ")", " "), "", $data[$key]['color']));
				
				$this->pdf->SetDrawColor($color[0], $color[1], $color[2]);
				$this->pdf->SetFillColor($color[0], $color[1], $color[2]);
				
				
				if($data[$key]['style'] == "dashed"){
					$this->pdf->SetLineWidth(0.2);
					
					if($i == 1){
						$spaces = $this->card_width / 2;
						$x = $this->x;
						$x2 = $this->x;
						$y = $this->y + ($data[$key]['top'] / 5.5);
						$y2 = $this->y + ($data[$key]['top'] / 5.5);
					}else{
						$spaces = $this->card_height / 2;
						$y = $this->y;
						$y2 = $this->y;
						$x = $this->x + ($data[$key]['left'] / 5.5);
						$x2 = $this->x + ($data[$key]['left'] / 5.5);
					}
					
					for($i2 = 1; $i2 <= $spaces; $i2++){
						if($i == 1){	
							$x = $x2 + 1;
							$x2 = $x2 + 2;
						}else{
							$y = $y2 + 1;
							$y2 = $y2 + 2;					
						}

						$this->pdf->Line($x, $y, $x2, $y2);
					}
					
				}else{
					$thickness = $data[$key]['thickness'] / 10;
					
					$this->pdf->SetLineWidth($thickness * 3);
				
					if($i == 1)
						$this->pdf->Line($this->x, $this->y + ($data[$key]['top'] / 5.7), $this->x + $this->card_width, $this->y + ($data[$key]['top'] / 5.7));
					//vertical line
					else
						$this->pdf->Line($this->x + ($data[$key]['left'] / 5.7), $this->y, $this->x + ($data[$key]['left'] / 5.7), $this->y + $this->card_height);
				}
			}
		}
	}
	
	public function add_image(){
		foreach($this->data[$this->side]['floatingImages'] as $key => $value){
			//skip any empty values
			if(empty($value['path'])) continue;
			
			//dimensions reduced to mm
			$width = $value['width'] / 5.7; 
			$height = $value['height'] / 5.7;
			
			//offset reduced to mm
			$x = $this->x + (($value['left'] - 47) / 5.7);
			$y = $this->y + (($value['top'] - 52) / 5.7);
			
			$this->pdf->Image($value['path'], $x, $y, $width, $height);
		}
	}
	
	public function add_cut_line(){
		$this->pdf->SetLineWidth(0.2);

		//set red color
		$this->pdf->SetDrawColor(255, 0, 0);
		//add top vertical lines
		if($this->i <= $this->per_row){
			$this->pdf->Line($this->x + 0.1, $this->y,$this->x + 0.1, $this->y - 3);
			$this->pdf->Line($this->x + $this->card_width - 0.1, $this->y, $this->card_width + $this->x - 0.1, $this->y - 3);
		}
		
		//add bottom vertical lines
		if($this->i == $this->cards_per_page || ($this->i + 1) == $this->cards_per_page){
			$this->pdf->Line($this->x + 0.1, $this->y + $this->card_height ,$this->x + 0.1, $this->y + $this->card_height + 3);
			$this->pdf->Line($this->x + $this->card_width - 0.1, $this->y  + $this->card_height , $this->card_width + $this->x - 0.1, $this->y + $this->card_height + 3);
		}
		
		//add horizontal lines
		if($this->new_row){
			$this->pdf->Line($this->x, $this->y + 0.1, $this->x - 3, $this->y + 0.1);
			$this->pdf->Line($this->x, $this->y + $this->card_height - 0.1, $this->x - 3, $this->y + $this->card_height  - 0.1);
		}elseif($this->x + ($this->card_width * 2) + 3  > $this->page_width){
			$this->pdf->Line($this->x + $this->card_width , $this->y + 0.1, $this->x + $this->card_width + 3, $this->y + 0.1);
			$this->pdf->Line($this->x + $this->card_width, $this->y + $this->card_height - 0.1, $this->x + $this->card_width + 3, $this->y + $this->card_height - 0.1);
		}
		
	}
	
	public function add_text(){
		foreach($this->data[$this->side]['textPositions'] as $key => $value){
			if($key == "__comment") continue;
			$style = $this->data[$this->side]['textProperties'][$key];
			
			$font_size= str_replace("pt", "", $style['font-size']);
			
			//get right font name
			switch(str_replace("'", "", $style["font-family"])){
				case "Courier New, Courier New, monospace":
					$font = "Courier";
				break;
				
				case "Times New Roman, Times New Roman, Times, serif":
					$font = "Times";
				break;				
				
				case "Arial, Helvetica, sans-serif":
					$font = "Arial";
				break;
				
				case "Comic Sans MS, Comic Sans MS5, cursive":
					$this->pdf->AddFont('Comic','','comic.php');
					$this->pdf->AddFont('Comic','B','comicb.php');
					$this->pdf->AddFont('Comic','BI','comicbl.php');
					$this->pdf->AddFont('Comic','I','comicbl.php');
					$font = "Comic";
				break;				
				
				case "Verdana, Verdana, Geneva, sans-serif":
					$this->pdf->AddFont('Verdana','','verdana.php');
					$this->pdf->AddFont('Verdana','B','verdanab.php');
					$this->pdf->AddFont('Verdana','BI','verdanabi.php');
					$this->pdf->AddFont('Verdana','I','verdanai.php');
					$font = "Verdana";
				break;
								
				case "Impact, Impact5, Charcoal6, sans-serif":
					$this->pdf->AddFont('Impact','','impact.php');
					$this->pdf->AddFont('Impact','B','impactb.php');
					$this->pdf->AddFont('Impact','BI','impactbi.php');
					$this->pdf->AddFont('Impact','I','impactbi.php');
					$font = "Impact";
				break;
				
				default:
					$font = "Arial";
				break;
			}
			
			$tstyle = "";
			
			//get right font
			if($style['font-weight'] == 700 || $style['font-weight'] == "bold")
				$tstyle = "B";
			if($style['font-style'] == "italic")
				$tstyle .= "I";
			if($style['text-decoration'] == "underline")
				$tstyle .= "U";

			$this->pdf->SetFont($font, $tstyle, $font_size / 1.5);
			
		
			//check if color is in hex since some browsers returns rgb
			if(preg_match("/#/i", $style['color']) > 0)
				$color = Model::factory("renderimage")->hex_to_rgb($style['color']);
			else
				$color = explode(",", str_replace(array("rgb(", ")", " "), "", $style['color']));
			
			
			$this->pdf->setTextColor($color[0], $color[1], $color[2]);
			
			$top = $this->y + (((($font_size / 100) * 25.4)) + 3) + ($value['top'] / 5.6);

			$left = $this->x + 3 + ($value['left'] / 5.3);
			
			$this->pdf->text($left, $top, $value['text']);
			
		}
	}

	
	public function save($name, $action){
		$this->pdf->Output($name, $action);
	}
}
?>