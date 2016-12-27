<?php
date_default_timezone_set('Asia/Bangkok');
$access_token = '5xB8I03dwTqRr7bAVZxYaU4FE2C+f9yzpTen4z+B/Q28nL+5Mvio/fsOzJeVmIq0eAeRCsOuw/gxsJdcyMn5+/lPgkpd+VnPWz3YLHP4DSDZiLpaYR6GP9YU/K68+Cf/N5Hr/AfbFGGYpiJ6JOM1ewdB04t89/1O/w1cDnyilFU=';

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

			// Build message to reply back
			$text = ($text == 'Kak') ? 'กากส์' : $text;
			$text = ($text == 'เกรียน') ? 'เหี้ย กรุ๊ปกากส์' : $text;
			$text = ($text == 'ยนน') ? 'เยส แน่ นอน' : $text;
			
			$text = ($text == 'บอท') ? 'เงี่ยน?' : $text;
			$text = ($text == 'ควย') ? 'อยากได้?' : $text;
			$text = ($text == 'สัส') ? 'เป็นเหี้ยไร' : $text;
			$text = ($text == 'กาก') ? 'สมกับมึงแล้ว' : $text;
			
			if (strpos($text, 'จึ๊ก') !== false) {
				$text = 'หล่อ สัสๆ';
			}
			
			if (strpos($text, 'บอล') !== false) {
				$text = 'พ่อเทพบุตร';
			}
			
			if (strpos($text, 'บอลซัง') !== false) {
				$text = 'พนักงานดีเด่น';
			}
			
			//LAUGH
			if (strpos($text, '555') !== false) {
				$text = '555555555555555555+';
			}
			
			if (strpos($text, 'ถถถ') !== false) {
				$text = 'ถถถถถถถถถถถถถถถถ';
			}
			
			//DATE TIME
			$text = ($text == '!day') ? date("l",time()) : $text;
			$text = ($text == '!date') ? date("Y-m-d",time()) : $text;
			$text = ($text == '!time') ? date("H:i:s",time()) : $text;
			$text = ($text == 'วันนี้วันอะไร') ? date("l",time()) : $text;
			$text = ($text == 'วันนี้วันที่เท่าไหร่') ? date("Y-m-d",time()) : $text;
			$text = ($text == 'กี่โมงแล้ว') ? date("H:i:s",time()) : $text;
			
			//EATING
			$text = ($text == 'แดกไรดี') ? '1. ปิ้งย่าง  2.อาหารญี่ปุ่น 3.บุฟเฟต์' : $text;
			
			$text = ($text == '1') ? 'เตาถ่าน' : $text;
			$text = ($text == '2') ? 'Tengoku' : $text;
			$text = ($text == '3') ? 'Oishi' : $text;
			
			//HELP
			//$text = ($text == '!help') ? '' : $text;
			
			$text_ex = explode(' ', $text);
			
			//Google
			if($text_ex[0] == "google"){
				$text = 'https://www.google.co.th/webhp?hl=en&sa=X&ved=0ahUKEwi9_eLi95PRAhXJN48KHQpIA9EQPAgD#hl=en&q='. $text_ex[1];
			}

			//Weather
			if($text_ex[0] == "อากาศ"){
				$ch1 = curl_init(); 
				curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
				curl_setopt($ch1, CURLOPT_URL, 'http://api.openweathermap.org/data/2.5/weather?q='.$text_ex[1].'&APPID=00583bfaf42c82b44a8f99896720ee8f'); 
				$result1 = curl_exec($ch1); 
				curl_close($ch1); 
				$obj = json_decode($result1, true); 
				
				/*
				if(isset($obj['weather']['txt_forecast']['forecastday'][0]['fcttext_metric'])){ 
					$result_text = $obj['forecast']['txt_forecast']['forecastday'][0]['fcttext_metric']; 
				}else{//ถ้าไม่เจอกับตอบกลับว่าไม่พบข้อมูล 
					$result_text = 'ไม่พบข้อมูล'; 
				}
				*/
				/*
				foreach($obj['weather'] as $key => $val){ 
					$result_text = $val['main'] .'-'.$val['description']; 
				}
				*/
				/*
				foreach($obj['main'] as $key => $val){ 
					$result_text = $val['temp']; 
				}*/
				
				$result_text = $obj['name'].' lat:'.$obj['coord']['lat'].' lon:'.$obj['coord']['lon'].' -'.$obj['weather']['main'].' -'.$obj['weather']['description'].' - temp:'.$obj['main']['temp'].' - wind speed:'.$obj['wind']['speed'].' - wind deg: '.$obj['wind']['deg'];
				
				if(empty($result_text)){//หาจาก en ไม่พบก็บอกว่า ไม่พบข้อมูล ตอบกลับไป 
					$result_text = 'ไม่พบข้อมูล'; 
				}
				
				$text = $result_text;
			}
			
			//Wikipedia
			if($text_ex[0] == "อยากรู้"){ //ถ้าข้อความคือ "อยากรู้" ให้ทำการดึงข้อมูลจาก Wikipedia หาจากไทยก่อน 
				//https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=PHP 
					$ch1 = curl_init(); 
					curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
					curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
					curl_setopt($ch1, CURLOPT_URL, 'https://th.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$text_ex[1]); 
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
					curl_setopt($ch1, CURLOPT_URL, 'https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$text_ex[1]); 
					$result1 = curl_exec($ch1); 
					curl_close($ch1); 
					$obj = json_decode($result1, true); 
					
					foreach($obj['query']['pages'] as $key => $val){ 
					$result_text = $val['extract']; 
					} 
				} 
				if(empty($result_text)){//หาจาก en ไม่พบก็บอกว่า ไม่พบข้อมูล ตอบกลับไป 
					$result_text = 'ไม่พบข้อมูล'; 
				} 
				$response_format_text = ['contentType'=>1,"toType"=>1,"text"=>$result_text]; 
				
				$text = $result_text;
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