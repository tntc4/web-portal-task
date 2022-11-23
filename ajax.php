<?php
	include 'global.functions.php';
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['term'])){
			$search_term = trim($_POST['term']);
			
			if(!empty($search_term)){
				// Autorization
				$access_token = autorize();
				
				if($access_token !== false){
					// Get Data
					$result = get_data($access_token);
					
					if($result){
						// Search
						$filtered_data = search($search_term, $result);
					}
				}
			}
			else{
				$filtered_data = false;
			}
			
			header('Content-type: text/plain');
			echo json_encode($filtered_data , JSON_PRETTY_PRINT);
		}
	}
?>