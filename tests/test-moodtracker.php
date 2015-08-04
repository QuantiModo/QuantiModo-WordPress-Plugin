<?php
class MoodtrackerTest extends WP_UnitTestCase {
     private $clientConfig;
	 public function setUp()
		{
		$measurements = array(
			'timestamp' => floor(time() / 1000),
			'value' => getenv('MOOD_RATE'), // like 1,2,3,4,5 as per mood impression
			'note' => ''
		);
		$this->clientConfig = array(		
			'measurements' => $measurements,
			'name' => 'Overall Mood',
			'source' => 'MoodiModo',
			'category' => 'Mood',
			'combinationOperation' => 'MEAN',
			'unit' => '/5'
		);
    }
	
	public function testMoodTracker(){
		
		$field_string = http_build_query($this->clientConfig);
		$url = 'https://app.quantimo.do/api/measurements/v2';
		$curl = curl_init();
		$header = array();
		$header[] = "Content-Type: application/json";
		$header[] = 'Authorization: Bearer'.getenv('ACCESS_TOKEN');		
		curl_setopt($curl, CURLOPT_HTTPHEADER,$header);		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $field_string);
		$result = curl_exec($curl);		
		$result = json_decode($result);	
		echo '<pre>'; print_r($result);
				
			$this->assertTrue(true);	
		
		curl_close($curl);		
	}
}
?>	