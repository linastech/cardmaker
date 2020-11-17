<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Template{
	public $template = "index";

	public function action_index() {
		$view = View::Factory("admin_login");
		
		//load admin script
		$this->template->scripts = array('media/js/admin.js');
		//load admin css
		$this->template->styles = array('media/css/admin.css'=>'screen');
		$this->template->admin = $view;
		
		$build_list = Model::factory('rendergalery');
		$build_list->build_list();
		$list = $build_list->render();
		$this->template->presetsList = $list;

	}

	public function action_login(){
		$this->auto_render = false;
		
		if(!$this->request->is_ajax()) return;
		
		$post = $this->request->post();
		$auth = Auth::instance();
		if($auth->login($post['username'], $post['password'], false) || $auth->logged_in()){
			$status = true;
			$error = false;
			$html = View::factory("admin_cp")->render();
		}else{
			$status = false;
			$error = "Username or password is incorect";
			$html = false;
		}
		
		echo json_encode(array(
					"status" => $status,
					"errors" => $error,
					"html" => $html
				));
	}
	
	public function action_logout(){
		$this->auto_render = false;
		Auth::instance()->logout();
	}
	
	public function action_manage_users(){
		$this->auto_render = false;
		
		//load html
		$view = View::factory("admin_manage_users");
		//get user data
		$user = DB::select()->from("users")->execute()->as_array();
		
		$list = "";
		//render list
		foreach($user as $value){
			//get last login
			if($value['last_login'] != null)
				$lastLogin = date("Y-m-d", $value['last_login']); else $lastLogin = "Never logged in.";
			//check if user is disabled
			if($value['disabled'] == 1) $accStatus = "enable"; else $accStatus = "disable";
			
			$list .= "
				<li class='AdminUserListItem'>
					<div class='displayInline adminUsersListTitle'>".$value['username']."</div>
					<div class='displayInline adminUsersListTitle'>".$lastLogin."</div>
					<div class='displayInline adminUsersListTitle'>".$value['last_ip']."</div>
					<div class='displayInline adminUsersListTitle'>
						<div class='displayInline manageImgDeleteUser adminIcon' style='margin-top:3px;' title='After you delete this account it will not be possible to recover it.'></div>
					</div>
					<div class='displayInline adminUsersListTitle'>
						<div class='displayInline manageUser".$accStatus." adminIcon' style='margin-top:3px;' action='".$accStatus."'></div>
					</div>					
					<div class='displayInline adminUsersListTitle' style='width:132px;'>
						<input type='text' class='adminUpdateUsername textInput' />
						<div class='displayInline manageUserChangePassword adminIcon' style='margin-top:3px;' ></div>
					</div>
				</li>
				";
		}
				
		$view->list = $list;
		echo $view->render();
	}
	
	public function action_manage_preset(){
		$this->auto_render = false;
		$post = $this->request->post();
		
		//load html
		$view = View::Factory("admin_manage_preset");
		
		$path = "media/js/presetsdata/";
		
		$handle = opendir($path);
		$list = null;
		
		//check each file and build a list
		while(($file = readdir($handle)) !== false){
			//check if it's a file
			if(filetype($path.$file) == "file"){
					//file is saved in this format:
					//template name -> template-name_0319190001343078576
					//numbers is a timestamp
					
					$get_name = explode("_", $file);
					
					$tmp_name = str_replace(array(".js", "-"), " ", $get_name[0]);
					
					//add new list item
					$list .= "<option value='".$file."'>".$tmp_name."</opton>";
			}
		}
		//close dir
		closedir($handle);
		
		//add list to html
		if($list == null)
			$view->list = "<option>No templates were found!</option>";
		else
			$view->list = $list;
		
		//render preview
		$img = Model::Factory("renderimage");
		$path = $img->set_data($post['cardData'], "save_preset_preview");
		
		$view->preview_path = $path[0];
		
		//render html
		echo $view->render();
	}
	
	public function action_is_logged_in(){
		$this->auto_render = false;
		if(Auth::instance()->logged_in() != 0){
			echo json_encode(array("loggedIn" => true, "html" => $html = View::factory("admin_cp")->render()));
		}else{
			echo json_encode(array("loggedIn" => false));
		}
	}
	
	public function action_manage_images(){
		$this->auto_render = false;
		$post = $this->request->post();
		$view = View::Factory("admin_manage_img");
		
		//render list for categorys
		$excludeList = array(".", "..");
		
		if($post['requestType'] == "bg")
			$path = "media/images/designs/backgrounds/";
		else
			$path = "media/images/designs/floating-images/";
		
		if ($handle = opendir($path)){
			$list = "";
			
			while (false !== ($entry = readdir($handle))){
				if(in_array($entry, $excludeList)) continue;
				$list .= "<option value='".$entry."'>".$entry."</option>";
			}

			closedir($handle);
		}

		$view->categorys_list = $list;
		echo $view->render();
	}
	
	public function action_manage_categorys(){
		$this->auto_render = false;
		
		$post = $this->request->post();
		
		if($post['doFor'] == "bg")
			$path = "media/images/designs/backgrounds/";
		else
			$path = "media/images/designs/floating-images/";
			
		//create new dir
		switch($post['action']){
			case 'create_category':
				if(!is_dir($path.$post['name'])){
					mkdir($path.$post['name'], 0777, true);
					chmod($path.$post['name'], 0777);
					echo json_encode(array('response'	=> true));
				}else{
					echo json_encode(array('response'	=> false, 'error' => "Directory already exists."));
				}	
				
			break;
			
			case 'delete_category':
				$dir = $path.$post['name'];
			
				if (!is_dir($dir)) {
					echo json_encode(array('response'	=> false, 'error' => "Directory does not exists."));
					return;
				}else{
					$objects = scandir($dir); 
					
					foreach ($objects as $object) { 
						if ($object != "." && $object != "..") { 
							if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
						} 
					} 
					reset($objects); 
					if(rmdir($dir))
						echo json_encode(array('response'	=> true));
					else
						echo json_encode(array('response'	=> false, 'error' => "Directory was not deleted!"));
				}
			break;
		}
	}
	
	public function action_save_preset(){
		$this->auto_render = false;
		$post = $this->request->post();
		
		//module for image rendering
		$image = Model::factory("renderimage");
		
		//preset name
		$presetName = $post['name'];
		
		$post['cardData']['preview']['title'] = $post['title'];
		
		$desc = str_replace("\n", "<br />", $post['desc']);
		$post['cardData']['preview']['desc'] = $desc;
		
		//send card data, zt will return rendered images names in array [0] -> full size, [1] -> thumb
		$names = $image->set_data($post['cardData'], "preset", $presetName);
		
		//set preview images names
		$post['cardData']['preview']['fullSize'] = $names[0];
		$post['cardData']['preview']['thumb'] = $names[1];
		
		//get name for preset file
		$name = $presetName . "_" . str_replace(array(" ", "."), "", microtime()) .".js";
				
		$file = fopen("media/js/presetsdata/".$name, "w");
		
		$data = $this->indent(json_encode($post['cardData']));
		fwrite($file, $data);
		
		fclose($file);
		
		if(file_exists($names[0]) && file_exists($names[1]) && file_exists("media/js/presetsdata/".$name))
			echo json_encode(array("response" => true));
		else
			echo json_encode(array("response" => false, "errors" => "One of the files was not created!"));

	}
	
	public function action_delete_preset(){
		$this->auto_render = false;
		
		$post = $this->request->post();
		$errors = array();
		
		//remove all files
		foreach($post['items'] as $file){
			if(!unlink($file))
				$errors['error'] = $file . "Was not deleted!";
		}
		
		if(count($errors) > 0)
			echo json_encode(array("response" => false, "errors" => $errors['error']));
		else
			echo json_encode(array("response" => true));
	}
	
	function indent($json) {

		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
			
			// If this character is the end of an element, 
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}
			
			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element, 
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			
			$prevChar = $char;
		}

		return $result;
	}

	public function action_test(){
		$this->auto_render = false;
		
		$user = ORM::factory("user");
		
		$user->values(array(
			'username'	=> "test",
			"password"	=> "test",
			"email"		=> rand(99, 99999)."testas@email.com"
		));
				
		$user->save();

		//add login and admin roles
		$user->add("roles", array(1, 2));

	}

}
