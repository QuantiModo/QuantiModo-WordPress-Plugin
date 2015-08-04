<?php
	######## SET POST REQUEST FOR MOOD TRACKER USING CURL ####
	require_once('../../../wp-load.php');
	global $current_user;
	$user_id = $current_user->ID;
	$accessToken = get_user_meta($user_id, 'quantimodo_access_token', true);
	//echo $accessToken; die;
	if(isset($_POST)) {
		$measurements = array(
			'timestamp' => floor(time() / 1000),
			'value' => $_POST['measurements'], // like 1,2,3,4,5 as per mood impression
			'note' => ''
		);
		$params = array(		
			'measurements' => $measurements,
			'name' => 'Overall Mood',
			'source' => 'MoodiModo',
			'category' => 'Mood',
			'combinationOperation' => 'MEAN',
			'unit' => '/5'
		);
		//echo '<pre>'; print_r($params); die;
		$field_string = http_build_query($params);
		$url = 'https://staging.quantimo.do/api/measurements/v2';
		$curl = curl_init();
		$header = array();
		$header[] = "Content-Type: application/json";
		$header[] = 'Authorization: Bearer'.$accessToken;
		//$header = json_encode($header);
		//curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
		//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization', 'Bearer ' . $accessToken));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $field_string);
		$result = curl_exec($curl);
		//echo '<pre>'; print_r($result); die;
		$result = json_decode($result);
		//echo '<pre>'; print_r($result); die;
		echo $result->error->message; 
		curl_close($curl);
		die;		
	}
?>