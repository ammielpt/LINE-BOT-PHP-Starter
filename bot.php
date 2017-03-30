<?php
date_default_timezone_set('Asia/Bangkok');
$access_token = '5xB8I03dwTqRr7bAVZxYaU4FE2C+f9yzpTen4z+B/Q28nL+5Mvio/fsOzJeVmIq0eAeRCsOuw/gxsJdcyMn5+/lPgkpd+VnPWz3YLHP4DSDZiLpaYR6GP9YU/K68+Cf/N5Hr/AfbFGGYpiJ6JOM1ewdB04t89/1O/w1cDnyilFU=';

echo date("Y-m-d H:i:s");
// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			$id = '';
			$type = '';
			if($event['source']['type'] == 'user'){
				$id = $event['source']['userId'];
				$type = 'user';
			}else if($event['source']['type'] == 'group'){
				$id = $event['source']['groupId'];
				$type = 'user';
			}else if($event['source']['type'] == 'room'){
				$id = $event['source']['roomId'];
				$type = 'user';
			}

			//Save to Logger
			try{
				$url = 'https://linechatlogger.firebaseio.com/logger.json';
				$data = [
					'datetime' => date("Y-m-d H:i:s"),
					'event' => [$event],
				];
				$post = json_encode($data);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$result = curl_exec($ch);
				curl_close($ch);
			}catch(Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			// End Save to Logger

			//LAUGH
			if (strpos($text, '555') !== false) {
				$text = '555555555555555555+';
			}
			//END LAUGH

			//Request Image Response
			$text_ex = explode(' ', $text);
			if (strpos($text_ex[0], 'ขอรูป') !== false) {
					$counter = 1;

					if($text == 'ขอรูปเยอะๆ'){
						$counter = 5;
					}

					if(sizeof($text_ex) > 1 && is_numeric($text_ex[1])){
						$counter = $text_ex[1];
					}

					//Circuit breaker
					if($counter > 10)
						$counter = 10;

					try{
						// Get request/response message from firebase
						$url = 'https://friendlychat-7162a.firebaseio.com/images/aGirl.json';
						$content = file_get_contents($url);
						$json = json_decode($content, true);
						$imageList = array();

						//Fill in image url into an Array
						foreach($json as $item){
							array_push($imageList,$item['downloadURLs']);
						}
						$arraySize = sizeof($imageList);
						//echo 'image size: '+ $arraySize;

						//Randomly pick 1 image from the list and reply back
						if($arraySize >= 0){
							for($i = 0; $i < $counter; $i++){
								$randomIndex = rand(0,$arraySize - 1);
								$imgUrl = $imageList[$randomIndex];

								//RESPONSE
								$messages = [
									'type' => 'image',
									'originalContentUrl' => $imgUrl,
									'previewImageUrl' => $imgUrl
								];

								// Make a POST Request to Messaging API to reply to sender
								$url = 'https://api.line.me/v2/bot/message/push';
								$data = [
									'to' => $id,
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
						}else{
							//no result from json.
						}
					}catch(Exception $ex){
							echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
			}
			//End Request Image Response

			//Command Function
			$text = strtolower($text);
			$text_ex = explode(' ', $text);

			if($text_ex[0] == 'อยากรู้' && isset($text_ex[1])){
				$text = CheckWiki(str_replace('อยากรู้ ','',$text));
			}else if($text_ex[0] == 'อากาศ' && isset($text_ex[1])){
				$text = CheckWeather(str_replace('อากาศ ','',$text));
			}else if($text_ex[0] == 'aqi' && $text_ex[1] == 'detail' && isset($text_ex[2])){
				$text = CheckAQI(str_replace('aqi detail ','',$text), $id);
			}else if($text_ex[0] == 'aqi' && isset($text_ex[1])){
				$text = CheckAQITemplate(str_replace('aqi ','',$text), $id);
			}

			// HELP -- Keep this command at the last
			if($text == '!help'){
				$text = "อากาศ ชื่อสถานที่\n".
								"อยากรู้ [keyword]\n".
								"ตัวอย่าง\n".
								"อากาศ เชียงใหม่\n".
								'อยากรู้ แมว';
			}

			if ($text == $event['message']['text']) {
					//ignore
			}else{
				$messages = [
					'type' => 'text',
					'text' => $text
				];

				// Make a POST Request to Messaging API to reply to sender
				$url = 'https://api.line.me/v2/bot/message/reply';
				$data = [
					'replyToken' => $replyToken,
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

				echo $result . "\r\n";
			}
		}
	}
}
echo "OK";

function CheckWiki($q){
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch1, CURLOPT_URL, 'https://th.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$q);
			$result1 = curl_exec($ch1);
			curl_close($ch1);
			$obj = json_decode($result1, true);
		foreach($obj['query']['pages'] as $key => $val){
			$result_text = $val['extract'];
		}
		if(empty($result_text)){//ถ้าไม่พบให้หาจาก en
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch1, CURLOPT_URL, 'https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$q);
			$result1 = curl_exec($ch1);
			curl_close($ch1);
			$obj = json_decode($result1, true);

			foreach($obj['query']['pages'] as $key => $val){
			$result_text = $val['extract'];
			}
		}
		if(empty($result_text)){//หาจาก en ไม่พบก็บอกว่า ไม่พบข้อมูล ตอบกลับไป
			$result_text = 'สัส ไม่รู้โว้ย';
		}
		$response_format_text = ['contentType'=>1,"toType"=>1,"text"=>$result_text];

		$text = $result_text;

		return $text;
}

function CheckWeather($destination){
		$ch1 = curl_init();
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch1, CURLOPT_URL, 'http://api.openweathermap.org/data/2.5/weather?q='.$destination.'&APPID=00583bfaf42c82b44a8f99896720ee8f');
		$result1 = curl_exec($ch1);
		curl_close($ch1);
		$obj = json_decode($result1, true);

		$kelvin_temperature = (float) $obj['main']['temp'];
		$kelvin_max_temp = (float) $obj['main']['temp_max'];
		$kelvin_min_temp = (float) $obj['main']['temp_min'];
		$celsius_degree = $kelvin_temperature - 273.15; // K - 273.15
		$max_celsius_degree = $kelvin_max_temp - 273.15;
		$min_celsius_degree = $kelvin_min_temp - 273.15;

		$weather_array = $obj['weather'];

		$weather_condition = $weather_array[1];
		$weather_description = $weather_array[2];

		$result_text = $obj['name']."\n".
									 'อุณหภูมิ: '.$celsius_degree." C\n".
									 'สูงสุด: '.$max_celsius_degree." C\n".
									 'ต่ำสุด: '.$min_celsius_degree." C\n".
									 'สภาพอากาศ: '.$weather_condition.' '.$weather_description."\n";

		if(empty($result_text)){//หาจาก en ไม่พบก็บอกว่า ไม่พบข้อมูล ตอบกลับไป
			$result_text = 'ไม่พบข้อมูล';
		}

	$text = $result_text;
	return $text;
}

function CheckAQI($city, $id){
	$ch1 = curl_init();
	curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch1, CURLOPT_URL, 'https://krean-chat-bot.herokuapp.com/aqi-bot.php?city='.rawurlencode($city).'&id='.$id);
	$result1 = curl_exec($ch1);
	curl_close($ch1);
}

function CheckAQITemplate($city, $id){
	$ch1 = curl_init();
	curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch1, CURLOPT_URL, 'https://krean-chat-bot.herokuapp.com/aqi-bot-template.php?city='.rawurlencode($city).'&id='.$id);
	$result1 = curl_exec($ch1);
	curl_close($ch1);
}
