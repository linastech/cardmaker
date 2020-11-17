<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller_Template{
	public $template = "index";

    public function before(){
	
		parent::before();
		
		$build_list = Model::factory('rendergalery');
		$build_list->build_list();
		$list = $build_list->render();
		
		
		$this->template->presetsList = $list;

	}
	
	public function action_index() {}
	
	public function action_pdf(){
		$this->auto_render = false;
		$path = "media/js/presetsdata/default.js";
		
		$file = fread(fopen($path, "r"), filesize($path));
		
		$data = json_decode($file, true);
		
		$card_width = $data['dimensions']['width'] / 5.7;
		$card_height = $data['dimensions']['height'] / 5.7;
		
		$page_width = 210;
		$page_height = 297;
		
		$pdf = Model::factory("renderpdf");
		
		$pdf->set_data($data);
		
		$pdf->save();
		
		$this->response->headers("Content-Type", "application/pdf");
	}
	
	public function action_save(){
		$this->auto_render = false;
		
		if(!$this->request->is_ajax())
			return;
		$post = $this->request->post();
		
		//get right action
		if($post['action'] == "saveCard"){
			$name = strip_tags(str_replace(" ", "_", $post['name']));
			$stamp = date("ymdhms").rand(9,99);
			
			//insert data into database
			DB::insert("user_cards", array("name", "date", "time_stamp"))
				->values(array($name, DB::expr("NOW()"), $stamp))
				->execute();
			
			//create file
			$file = fopen("media/js/user_cards/".$name. "-" . $stamp .".js", "w");
		
			//encode to json
			$data = json_encode($post['data']);
			
			//write json to the file
			fwrite($file, $data);
		
			fclose($file);

			echo json_encode(array(
									"response" 	=> true,
									"link"		=> URL::base(TRUE)."home/load/".$stamp
								));
			
			
		}else{
			$view = View::Factory("save_image");
			
			$view->date = date("y-m-d");
			
			echo $view->render();
		}
	}
	
	public function action_load(){
		$name = $this->request->param("id");
		
		$db = DB::select()->from('user_cards')->where("time_stamp", "=", $name)->execute()->as_array();
		
		if(count($db) > 0){
			$path = "media/js/user_cards/".$db[0]['name']."-".$db[0]['time_stamp'].".js";
			$this->template->load_user_card = '{
				"status": true,
				"path": "'.$path.'"
			}';
		}
	}
	
}
