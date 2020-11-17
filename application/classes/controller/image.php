<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Image extends Controller_Template{
	public $template = "render";

	public function action_index(){
	}
	
		
	public function action_rotate(){
		$this->auto_render = false;
		$post = $this->request->post();
		
		$fileName = 'media/images/designs/rotated/'. date("y-m-d-h-m-s").rand(99, 999). ".jpg";

		$image = Image::factory($post['path'], "GD");
		
		$image->rotate(90);
		
		
		$image->save($fileName);
		
		echo json_encode(array(
			"url" => $fileName,
			"width" => $image->width,
			"height" => $image->height
		));
	}

	public function action_upload(){
		$this->auto_render = false;
		
		if(empty($_FILES)) return;
		
		$response = array();
		
		$post = $this->request->post();
		
		$validate = Validation::factory($_FILES)
						->rule("fileName", array("Upload", "image"))
						->rule("fileName", "Upload::size", array(":value", "1M"));
						
		if($validate->check()){
			$response["errors"] = false;
			
			$filename = date("Y-m-d-m-s").rand(999, 99999).".png";

			$image = Image::factory($validate['fileName']['tmp_name']);
			
			//resize width
			if($image->width > $post['cardWidth'])
				$image->resize($post['cardWidth'], null);
			
			//resize height
			if($image->height > $post['cardHeight'])
				$image->resize(null, $post['cardHeight']);
			
			//save
			$image->save("media/images/designs/backgrounds/user_uploaded/".$filename);
			
			$response["path"] = "media/images/designs/backgrounds/user_uploaded/".$filename;
			
		}else{
			$getErros = $validate->errors("user_errors");
			$response["errors"] = true;

			$i = 1;
			foreach($getErros as $error){
				$response["errorList"][$i] = $error;
				$i++;
			}
		}
		
		echo json_encode($response);
		
	}

	
	public function action_render_image(){
		if(!$this->request->is_ajax()) return;
		
		//get post data
		$post = $this->request->post();
		
		//call img model
		$img = Model::Factory("renderimage");
		
		//render images
		$paths = $img->set_data($post['data'], $post['action']);
		
		$this->template->card1 = "<img src='".$paths[0]."' />";
		$this->template->card2 = "<img src='".$paths[1]."' />";
		
		if($post['selectedFormat'] == "image"){
			$this->template->imageChecked = "checked='1'";
			$this->template->pdfChecked = "";
		}else{
			$this->template->imageChecked = "";
			$this->template->pdfChecked = "checked='1'";
		}
		
		$this->template->code = rand(1, 99999);
	}
	
	public function action_delete_temp(){
		$this->auto_render = false;
		
		$post = $this->request->post();
		
		unlink($post['image1']);
		unlink($post['image2']);
	}
	
	public function action_image(){
		$this->auto_render = false;
		$this->response->headers("Content-Type", "image/jpeg");

		$path = "media/js/presetsdata/test23_0618872001345308139.js";
		
		$file = fread(fopen($path, "r"), filesize($path));
		
		$data = json_decode($file, true);
		
		//call img model
		$img = Model::Factory("renderimage");
		
		$src = $img->set_data($data, "download", null);
		
		var_dump($src);
		
	}
	
	public function action_pull_images_data(){
		$this->auto_render = false;
	
		$action = $this->request->post("action");
		$dirs = "";
		
		//get dir
		if($action == "bg-images")
			$path = "media/images/designs/backgrounds/";
		else
			$path = "media/images/designs/floating-images/";
		
		//open that dir
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				//build list of dirs
				if ($entry != "." && $entry != ".." && $entry != "user_uploaded") {
					if($entry != "random")
						$dirs .= "<option value='".$entry."' >".$entry."</option>";
					else
						$dirs .= "<option value='".$entry."' selected='1'>".$entry."</option>";
				}
			}
			closedir($handle);
		}
		
		echo json_encode(array(
			"dir_list" => $dirs,
			"files"	   => $this->searchForFiles($path."random/")
		));
	}
	
	public function action_pull_images(){
		$this->auto_render = false;
		
		$post = $this->request->post();
		
		//get dir
		if($post['action'] == "bg-images")
			$path = "media/images/designs/backgrounds/";
		else
			$path = "media/images/designs/floating-images/";

		
		echo json_encode(array(
			"files" => $this->searchForFiles($path.$post['dir']."/")
		));
	}
	
	public function searchForFiles($path){
			$excludeList = array(".", "..");

			$files = array_diff(scandir($path), $excludeList);

			sort($files, SORT_NUMERIC);

			return array("count" => count($files), "names" => $files, "path" => $path);
	}

}