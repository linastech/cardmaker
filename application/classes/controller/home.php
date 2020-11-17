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
	
	public function action_payment(){
		$this->auto_render = false;
		
		$code = $this->request->post('code');
		
		$check = DB::select()->from('payments')->where('code', '=', $code)->execute()->as_array();
		
		if(count($check) > 0){
			if($check[0]['status'] == "true"){
				echo json_encode(array(
					"payment_status" => true
				));
			}
		}else{
			echo json_encode(array(
				"payment_status" => false
			));
		}
	}
	
	public function action_pdf(){
		$this->auto_render = false;
		$path = "media/js/presetsdata/default.js";
		
		$file = fread(fopen($path, "r"), filesize($path));
		
		$data = json_decode($file, true);

		
		$pdf = Model::factory("renderpdf");
		
		$pdf->set_data($data);
		
		//D -> Download, I -> View
		$pdf->save("name", "I");
		
		$this->response->headers("Content-Type", "application/pdf");
	}
	
	public function action_download_document(){
		$this->auto_render = false;
		$action = $this->request->post("action");
		
		$json = $this->request->post("data");

		if($json == "") return;

		$data = json_decode($json, true);

		
		if($action == "pdf"){			
			$card_width = $data['dimensions']['width'] / 5.7;
			$card_height = $data['dimensions']['height'] / 5.7;
			
			$page_width = 210;
			$page_height = 297;
			
			$pdf = Model::factory("renderpdf");
			
			$pdf->set_data($data);
			
			//D -> Download, I -> View
			$pdf->save("Card.pdf", "D");
			
			$this->response->headers("Content-Type", "application/pdf");
		}else{
			//call img model
			$img = Model::Factory("renderimage");
			
			$src = $img->set_data($data, "download", null);
			
			header('Content-Disposition: attachment; filename="Card.jpg"');

			echo $src;
		}
		
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
	
	public function action_contact_us(){
		$this->auto_render = false;
		
		$post = $this->request->post();
		
		if($post['action'] == "send"){
			echo json_encode(array(
				"response" => true,
				"text" => "Your message has been successfully sent."
			));
			return;
		}
		
		$view = View::Factory("contact_us");
		
		echo $view->render();
	}
	
	public function action_about_us(){
		$this->auto_render = false;
		
		//get content
		$content = DB::select()->from("about_us")->execute()->as_array();
		
		$view = View::Factory("about_us");
		
		$view->content = $content[0]['content'];
		
		echo $view->render();
	}
	
	public function action_pay(){
		$this->auto_render = false;
		
		$webToPay = Model::factory("webtopay");
		
		$data = $_GET;
		
		$data_list = "";
		
		$params = array();
		parse_str(base64_decode(strtr($_GET['data'], array('-' => '+', '_' => '/'))), $params);

		
		foreach($params as $key => $value){
			$data_list .= $key. " => ". $value. " <br />";
		}
		

		try {
			$response = $webToPay->checkResponse($_GET, array(
				'projectid' => 27474,
				'sign_password' => 'secret',
			));

			//@todo: check if $response['projectid'] matches your project ID
			//@todo: check if SMS with ID $response['id'] was not already confirmed in your system

			echo 'OK Aciu, kad siunciate';
			DB::insert("sms")->values(array("", $data_list, "OK"))->execute();
		}
			catch (Exception $e) {
			echo get_class($e).': '.$e->getMessage();
			DB::insert("sms")->values(array("", $data_list, $e))->execute();
		}

	}
	
}
