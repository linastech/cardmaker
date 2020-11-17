<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Image extends Controller_Template{
	public $template = "render";

	public function action_index(){
	}
	
		
	public function action_rotate(){
		$post = $this->request->post();
		
		$fileName = 'media/images/designs/rotated/'. rand(99, 999). ".jpg";

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
			
			$filename = date("Y-m-d-m-s").rand(999, 99999).".jpg";

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
	}
} 









