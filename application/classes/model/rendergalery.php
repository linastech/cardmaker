<?php defined('SYSPATH') or die('No direct script access.');
class Model_rendergalery extends Model {
	public $list = null;
	public $path = "media/js/presetsdata/";
	public $file;
	
	public function build_list($path = null){
		if(isset($path))
			$this->path = $path;
		
		$handle = opendir($this->path);
		
		//check each file and build a list
		while(($file = readdir($handle)) !== false){
			//check if it's a file
			if(filetype($this->path.$file) == "file"){
			
				$this->file = $file;
				//open that file
				$hndl = fopen($this->path . $file, "r");
				
				//get contents
				$rawJson = fread($hndl, filesize($this->path.$file));
				
				//parse it to json
				$data = $this->parse_json($rawJson);
				
				$fullSize = $data['preview']['fullSize'];
				$thumb = $data['preview']['thumb'];
				
				//skip any templates that doesn't have correct data
				if(!isset($fullSize) || !isset($thumb) || !file_exists($fullSize) || !file_exists($thumb))
						continue;
						
				isset($data['preview']['title']) ? $title = $data['preview']['title'] : $title = null;
				isset($data['preview']['desc']) ? $alt = $data['preview']['desc'] : $alt = null;
					
				//add new list element
				$this->add_item($fullSize, $thumb, $title, $alt);
				
				fclose($hndl);
			}
		}
		//close dir
		closedir($handle);
	}
	
	public function add_item($fullSize, $thumb, $title, $alt){
		//set title
		if($title != null)
			$title = 'title="'.$title.'"';
		
		//set description
		if($alt != null)
			$alt = 'alt="'.$alt.'"';
		
		$this->list .= '
		<li>
			<a href="'.$fullSize.'">
				<img src="'.$thumb.'" '.$title.' '.$alt.' data="'.$this->path.$this->file.'">
			</a>
		</li>
		';
	}
	
	private function parse_json($json){
		return json_decode($json, true);
	}
	
	public function render(){
		return $this->list;
	}
}
?>