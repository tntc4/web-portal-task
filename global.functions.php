<?php
	include 'curl.php';
	
	function autorize(){
		$curl = new Curl;
		
		// Autorization
		$curl->headers['Authorization'] = 'Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz';
		$curl->headers['Content-Type'] = 'application/json';
	
		$vars = array(
			'username' => '365',
			'password' => '1'
		);
		
		$response = $curl->post('https://api.baubuddy.de/index.php/login', json_encode($vars));
		if($response !== false){
			$body = $response->body;
			$body_ = json_decode($body);
			
			$access_token = $body_->oauth->access_token;
			
			return $access_token;
		}
		
		return false;
	}
	
	function get_data($token){
		$curl = new Curl;
		
		// Get Data
		$curl->headers['Authorization'] = 'Bearer ' . $token;
		$response = $curl->get('https://api.baubuddy.de/dev/index.php/v1/tasks/select');
		
		$result = false;
		if($response !== false){
			$result = json_decode($response->body);
			
			return $result;
		}
		
		return false;
	}
	
	function search($term = '', $data = false){
		if($data){
			if(!empty($term)){
				$new_data = array();
				
				foreach($data as $key => $val){
					if(mb_strstr($val->task, $term) || mb_strstr($val->title, $term) || mb_strstr($val->description, $term)){
						array_push($new_data, $data[$key]);
					}
				}
				
				if(!empty($new_data)){
					return $new_data;
				}
			}
			else{
				return $data;
			}
		}
		
		return false;
	}
	
	/**
	* Debug function
	* 
	* @param Mixed $obj
	* @param Boolean $die
	*/
	function d($data, $die = false) {
		if(isset($data) || is_null($data)){
			echo '<pre>';
			
			if(is_array($data)){
				print_r($data);
			}
			else if(is_object($data)){
				var_dump($data);
			}
			else if(gettype($data) == 'boolean'){
				switch($data){
					case true:
						echo 'true';
						break;
					case false:
						echo 'false';
						break;
				}
			}
			else if(gettype($data) == 'NULL'){
				echo 'null';
			}
			else{
				print_r($data);
			}
			
			echo '</pre>';
		}
		
		if ($die) {
			die();
		}
	}
?>