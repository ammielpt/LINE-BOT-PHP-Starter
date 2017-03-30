<?php
date_default_timezone_set('Asia/Bangkok');
$access_token = '5xB8I03dwTqRr7bAVZxYaU4FE2C+f9yzpTen4z+B/Q28nL+5Mvio/fsOzJeVmIq0eAeRCsOuw/gxsJdcyMn5+/lPgkpd+VnPWz3YLHP4DSDZiLpaYR6GP9YU/K68+Cf/N5Hr/AfbFGGYpiJ6JOM1ewdB04t89/1O/w1cDnyilFU=';
//$groupId = 'U37d948cd0f83293486fc2b7bd339adc1';
$groupId = 'U37d948cd0f83293486fc2b7bd339adc1'; //pornjeds

$_API_TOKEN = 'cda09c5eac1b8eb5d447385c3b7b0b67cf8e6acc';
$_CITY_NAME_PARAM = '{CITY_NAME}';
$_API_URL = "http://api.waqi.info/feed/{CITY_NAME}/?token=".$_API_TOKEN;


// Get HTTP Get parameter
//Parameter List
//Group ID
//City Name
$cityName = $_GET["city"];
if(isset($_GET['id']) && !is_null($_GET['id'])){
	$groupId = htmlspecialchars($_GET["id"]);
}
$api_request_url = '';

if(isset($cityName) && !is_null($cityName)){
	try{
		$api_request_url = str_replace($_CITY_NAME_PARAM, $cityName, $_API_URL);

		//Get JSON result
		$json = file_get_contents($api_request_url);
		$obj = json_decode($json);

		// if ok construct response
		if($obj -> status === "ok"){

			$pm25 = (float) $obj -> data -> iaqi -> pm25 -> v;
			$airQualityEU = '';
			$airQualityTH = '';

			if($pm25 <= 50){
				$airQualityEU = "Good ";
				$airQualityTH = "คุณภาพดี (high quality)	ไม่มีผลกระทบต่อสุขภาพ (No health effects)";
			}else if($pm25 <= 100){
				$airQualityEU = "Moderate ";
				$airQualityTH = "คุณภาพปานกลาง (medium quality)	ไม่มีผลกระทบต่อสุขภาพ (No health effects)";
			}else if($pm25 <= 150){
				$airQualityEU = "Unhealthy for Sensitive Groups";
				$airQualityTH = "มีผลกระทบต่อสุขภาพ (there are health effects)	ผู้ป่วยโรคระบบทางเดินหายใจ ควรหลีกเลี่ยงการออกกำลังภายนอกอาคาร บุคคลทั่วไป โดยเฉพาะเด็กและผู้สูงอายุ ไม่ควรทำกิจกรรมภายนอกอาคารเป็นเวลานาน (Patients with respiratory depression. Avoid exercising outdoors. Visitors, especially children and the elderly. Avoid prolonged outdoor activities.)";
			}else if($pm25 <= 200){
				$airQualityEU = "Unhealthy";
				$airQualityTH = "มีผลกระทบต่อสุขภาพ (there are health effects)	ผู้ป่วยโรคระบบทางเดินหายใจ ควรหลีกเลี่ยงการออกกำลังภายนอกอาคาร บุคคลทั่วไป โดยเฉพาะเด็กและผู้สูงอายุ ไม่ควรทำกิจกรรมภายนอกอาคารเป็นเวลานาน (Patients with respiratory depression. Avoid exercising outdoors. Visitors, especially children and the elderly. Avoid prolonged outdoor activities.)";
			}else if($pm25 <= 300){
				$airQualityEU = "Very Unhealthy";
				$airQualityTH = "มีผลกระทบต่อสุขภาพมาก (affects health)	ผู้ป่วยโรคระบบทางเดินหายใจ ควรหลีกเลี่ยงกิจกรรมภายนอกอาคาร บุคคลทั่วไป โดยเฉพาะเด็กและผู้สูงอายุ ควรจำกัดการออกกำลังภายนอกอาคาร (Patients with respiratory depression. Avoid outdoor activities. Visitors, especially children and the elderly. Should limit outdoor exercise)";
			}else if($pm25 <= 500){
				$airQualityEU = "Hazardous";
				$airQualityTH = "อันตราย (danger)	บุคคลทั่วไป ควรหลีกเลี่ยงการออกกำลังภายนอกอาคาร สำหรับผู้ป่วยโรคระบบทางเดินหายใจ ควรอยู่ภายในอาคาร (Visitors should avoid exercising outdoors. For patients with respiratory diseases. Should stay indoors)";
			}

			$text = "City: ". $obj -> data -> city -> name ." \n".
							"Quality: ". $airQualityEU ."\n".
							$airQualityTH."\n".
							"AQI: ". $obj -> data -> aqi ."\n".
							"Temperature: ". $obj -> data -> iaqi -> t -> v ."\n".
							"PM10: ". $obj -> data -> iaqi -> pm10 -> v."\n".
							"PM25: ". $obj -> data -> iaqi -> pm25 -> v ."\n".
							"Rain: ". $obj -> data -> iaqi -> r -> v ."\n".
							"SO2: ". $obj -> data -> iaqi -> so2 -> v ."\n".
							"Wind: ". $obj -> data -> iaqi -> w -> v ."\n".
							"Updated: ". $obj -> data -> time -> s;

			//Send response text to target group id
			// $messages = [
			// 	'type' => 'text',
			// 	'text' => $text
			// ];
			$messages = [
					'type':'template',
					'altText': 'Air Quality Index',
					'template':{
						'type': 'buttons',
						'thumbnailImageUrl': 'https://firebasestorage.googleapis.com/v0/b/friendlychat-7162a.appspot.com/o/images%2FAQI%2Fbanner.jpg?alt=media&token=087c92c7-8083-4ca4-8936-f4cdcfd02834',
						'title': $airQualityEU;,
						'text': $cityName . ' AQI: ' . $obj -> data -> aqi .
										"\nTemperature: " . $obj -> data -> iaqi -> t -> v .
										"\nUpdated: " . $obj -> data -> time -> s,
						'actions': [
							{
								'type': 'message',
								'label': 'Detail',
								'text': 'aqi detail ' . $cityName
							}
						]
					}
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/push';
			$data = [
				'to' => $groupId,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);
		}
	}catch(Exception $ex){
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}

}else{
	//Return not found city
	echo 'City Not found';
}
