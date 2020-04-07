<?php
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot984498338:AAHKQ9Jbp1ktnNvKno5Eqykci4a3-c_9L8c/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res,true);
}
function getupdates($up_id){
  $get = bot('getupdates',[
    'offset'=>$up_id
  ]);
  return end($get['result']);

}

function run($update){
$message = $update['message'];
$chat_id = $message['chat']['id'];
$text = $message['text'];
$name = $message['from']['first_name'];
$username = $message['chat']['username'];
$from_id = $message['from']['id'];
$chat_id2 = $update['callback_query']['message']['chat']['id'];
$message_id = $update['callback_query']['message']['message_id'];
$data = $update['callback_query']['data'];
$mid = $message['message_id'];
$get_ids = file_get_contents('memb.txt');
$ids = explode("\n", $get_ids);
$bot ="@yt_dlbot";


if($text == '/start'){
	bot('sendMessage',[
			'chat_id'=>$chat_id,
			'text'=>"𝗪𝗲𝗹𝗰𝗼𝗺𝗲 $name
𝗜 𝗰𝗮𝗻 𝗗𝗼𝘄𝗻𝗹𝗼𝗮𝗱 𝗩𝗶𝗱𝗲𝗼/𝗔𝘂𝗱𝗶𝗼 𝗳𝗿𝗼𝗺 𝗬𝗼𝘂𝗧𝘂𝗯𝗲 𝗮𝗻𝗱 𝘂𝗽𝗹𝗼𝗮𝗱 𝘁𝗵𝗲𝗺 𝘁𝗼 𝗧𝗲𝗹𝗲𝗴𝗿𝗮𝗺 
⚙| 𝗛𝗼𝘄 𝘁𝗼 𝘂𝘀 ? /help",
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
          [['text'=>'𝗖𝗵𝗮𝗻𝗻𝗲𝗹 ~','url'=>'https://t.me/rambo_syr']],
			]	
				])		
	]);
if ($update && !in_array($chat_id, $ids)) {
    file_put_contents("memb.txt", $chat_id."\n",FILE_APPEND);
  }
}
if(preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $text, $matches)){
	
	$s = shell_exec('youtube-dl "'.$text.'" --id --write-info-json --no-progress -f mp4');
	if(file_exists("{$matches[1]}.mp4")){
		$info = json_decode(file_get_contents("{$matches[1]}.info.json"));
		bot('sendPhoto',[
				'chat_id'=>$chat_id,
				'photo'=>$info->thumbnail,
				'caption'=>"{$info->title}
⏳| 𝗗𝘂𝗿𝗮𝘁𝗶𝗼𝗻 ~> {$info->duration}",
				'reply_markup'=>json_encode([
					'inline_keyboard'=>[
						[['text'=>'🎥 𝗩𝗲𝗱𝗶𝗼','callback_data'=>'vi^'.$matches[1]]],
						[['text'=>'🎧 𝗔𝘂𝗱𝗶𝗼 𝗙𝗶𝗹𝗲 𝗠𝗣3','callback_data'=>'au^'.$matches[1]]]
						]	
				])		
		]);
	} else {
		bot('sendMessage',[
			'chat_id'=>$chat_id,
			'text'=>' I can not download this video'
		]);
	}
} elseif($text != null and $text != '/start') {
	$items = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/search?part=snippet&q=".urlencode($text)."&type=video&key=AIzaSyD1g_E1nXxrMfoTKgkCArAgb40KKImezSU&maxResults=10"))->items;
	foreach($items as $item){
		$rep['inline_keyboard'][] = [['text'=>$item->snippet->title,'callback_data'=>'ph^'.$item->id->videoId]];
	}
	bot('sendMessage',['chat_id'=>$chat_id,'text'=>'search result 💡',
		'reply_markup'=>(json_encode($rep))]);
	}

if($data != null){
	$edata = explode('^', $data);
	if($edata[0] == 'ph'){
		$s = shell_exec('youtube-dl "'.$edata[1].'" --id --write-info-json --no-progress -f mp4');
	if(file_exists("{$edata[1]}.mp4")){
		$info = json_decode(file_get_contents("{$edata[1]}.info.json"));
		bot('sendPhoto',[
				'chat_id'=>$chat_id2,
				'photo'=>$info->thumbnail,
				'caption'=>"{$info->title}
⏳| 𝗗𝘂𝗿𝗮𝘁𝗶𝗼𝗻 ~> {$info->duration}",
				'reply_markup'=>json_encode([
					'inline_keyboard'=>[
						[['text'=>'🎥 𝗩𝗲𝗱𝗶𝗼','callback_data'=>'vi^'.$edata[1]]],
						[['text'=>'🎧 𝗔𝘂𝗱𝗶𝗼 𝗙𝗶𝗹𝗲 𝗠𝗣3','callback_data'=>'au^'.$edata[1]]]
						]	
				])		
		]);
	}
	}
	if($edata[0] == 'vi'){
		if(file_exists($edata[1].'.mp4')){
			if(filesize($edata[1].'.mp4') <= 52428800){
				bot('deleteMessage',[
					'chat_id'=>$chat_id2,
					'message_id'=>$message_id
				]);
				$m = bot('sendMessage',[
					'chat_id'=>$chat_id2,
					'text'=>'Please wait a few seconds to download...'
				])['result']['message_id'];
				
				$ok = bot('sendVideo',[
					'chat_id'=>$chat_id2,
					'video'=>new CURLFile($edata[1].'.mp4')
				])['ok'];
				if($ok == true){
					bot('deleteMessage',[
						'chat_id'=>$chat_id2,
						'message_id'=>$m
					]);
				} else {
					bot('deleteMessage',[
						'chat_id'=>$chat_id2,
						'message_id'=>$m
					]);
					bot('sendMessage',[
						'chat_id'=>$chat_id2,
						'text'=>' I can not download this video'
					]);
				}
				unlink($edata[1].'.mp4');
				unlink($edata[1].'.info.json');
			} else {
				bot('sendMessage',[
						'chat_id'=>$chat_id,
						'text'=>' I can not download this video'
					]);
					unlink($edata[1].'.mp4');
				unlink($edata[1].'.info.json');
			}
		} else {
			bot('sendMessage',[
			'chat_id'=>$chat_id2,
			'text'=>'I can not download this video please Send me a different link'
		]);
		}
	} elseif($edata[0] == 'au'){
		if(file_exists($edata[1].'.mp4')){
				bot('deleteMessage',[
					'chat_id'=>$chat_id2,
					'message_id'=>$message_id
				]);
				$m = bot('sendMessage',[
					'chat_id'=>$chat_id2,
					'text'=>'Please wait a few seconds to download.'
				])->result->message_id;
				$mp = str_replace('-', '', $edata[1]);
				rename($edata[1].'.mp4',str_replace('-', '', $edata[1].'.mp4'));
				$s = shell_exec('ffmpeg -i "'.$mp.'.mp4" "'.$mp.'.mp3"');
				if(file_exists($mp.'.mp3')){
					$info = json_decode(file_get_contents("{$edata[1]}.info.json"));
					$ok = bot('sendaudio',[
						'chat_id'=>$chat_id2,
						'audio'=>new CURLFile($mp.'.mp3'),
						'title'=>$info->title,
						'performer'=>$info->creator.' - '.$bot,
						'duration'=>$info->duration,
						'thumb'=>$info->thumbnail,
			'caption'=>"$bot - ".sprintf("%4.2f MB", filesize($mp.'.mp3')/1048576).' , '.gmdate('i:s',$info->duration)
					]);
					if($ok['ok'] == true){
						bot('deleteMessage',[
							'chat_id'=>$chat_id2,
							'message_id'=>$m
						]);
					} else {
						bot('deleteMessage',[
							'chat_id'=>$chat_id2,
							'message_id'=>$m
						]);
						bot('sendMessage',[
							'chat_id'=>$chat_id2,
							'text'=>' I can not download this video'.$ok['description']
						]);
					}
				}else {
						bot('deleteMessage',[
							'chat_id'=>$chat_id2,
							'message_id'=>$m
						]);
						bot('sendMessage',[
							'chat_id'=>$chat_id2,
							'text'=>' I can not download this video'
						]);
					}
				unlink($mp.'.mp4');
				unlink($mp.'.mp3');
				unlink($mp.'.info.json');
			
		} else {
			bot('sendMessage',[
			'chat_id'=>$chat_id2,
			'text'=>'I can not download this video please Send me a different link'
		]);
		}
	}
}
if ($text == "/help"){
    bot('sendMessage',[
        'chat_id'=>$chat_id,
      'text'=>"⚙️ How to use ?


➡️ Send me a text message ( search query ) and i will show the most relevant results from Youtube. 

 OR 

➡️ Send me a valid Youtube link.",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,

]);
}


}

while(true){
  $last_up = $last_up??0;
  $get_up = getupdates($last_up+1);
  $last_up = $get_up['update_id'];
  run($get_up);
  sleep(0);
}
