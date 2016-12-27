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
			$text = ($text == 'จึ๊ก') ? 'หล่อ สัสๆ' : $text;
			$text = ($text == 'บอล') ? 'พ่อเทพบุตร' : $text;
			$text = ($text == 'บอลซัง') ? 'พนักงานดีเด่น' : $text;
			$text = ($text == 'เกรียน') ? 'เหี้ย กรุ๊ปกากส์' : $text;
			$text = ($text == 'ยนน') ? 'เยส แน่ นอน' : $text;
			
			$text = ($text == 'สาว') ? 'เงี่ยน?' : $text;
			
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
			
			$text_ex = explode(':', $text);
			
			//Google
			if($text_ex[0] == "google"){
				$pieces = explode("google ", $text);
				$text = 'https://www.google.co.th/webhp?hl=en&sa=X&ved=0ahUKEwi9_eLi95PRAhXJN48KHQpIA9EQPAgD#hl=en&q='. $text_ex[1];
			}
			
			
			
			//Weather
			////http://api.wunderground.com/api/yourkey/forecast/lang:TH/q/Thailand/%E0%B8%81%E0%B8%A3%E0%B8%B8%E0%B8%87%E0%B9%80%E0%B8%97%E0%B8%9E%E0%B8%A1%E0%B8%AB%E0%B8%B2%E0%B8%99%E0%B8%84%E0%B8%A3.json - See more at: https://www.programmerthailand.com/tutorial/post/view/163/%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%AA%E0%B8%A3%E0%B9%89%E0%B8%B2%E0%B8%87-line-bot-%E0%B9%81%E0%B8%9A%E0%B8%9A%E0%B9%83%E0%B8%8A%E0%B9%89-curl-%E0%B9%83%E0%B8%99-yii-framework-2#sthash.e3ydFVOY.dpuf
			if($text_ex[0] == "อากาศ"){
				$ch1 = curl_init(); 
				curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
				curl_setopt($ch1, CURLOPT_URL, 'http://api.wunderground.com/api/yourkey/forecast/lang:TH/q/Thailand/'.str_replace(' ', '%20', $text_ex[1]).'.json'); 
				$result1 = curl_exec($ch1); 
				curl_close($ch1); 
				$obj = json_decode($result1, true); 
				
				if(isset($obj['forecast']['txt_forecast']['forecastday'][0]['fcttext_metric'])){ 
					$result_text = $obj['forecast']['txt_forecast']['forecastday'][0]['fcttext_metric']; 
				}else{//ถ้าไม่เจอกับตอบกลับว่าไม่พบข้อมูล 
					$result_text = 'ไม่พบข้อมูล'; 
				}
				
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