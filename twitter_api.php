<?php
// twitteroauth の読み込み
require('vendor/autoload.php');
require('secret_info.php');
use Abraham\TwitterOAuth\TwitterOAuth;

if(empty($consumer_key) || empty($consumer_secret) || empty($access_token) || empty($access_token_secret)){
  print("Error:The API access Information is not defined!\n");
  exit(1);
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

function obj2arr($obj)
{
    if ( !is_object($obj) ) return $obj;

    $arr = (array) $obj;

    foreach ( $arr as &$a )
    {
        $a = obj2arr($a);
    }

    return $arr;
}

/*
 * $keyword:検索語
 * $onetime_get_num:一回の検索リクエストで取ってくるツイートの数(APIの１回でのリクエスト上限は１００件)
 * $request_count:APIのリクエストを何回やるかと言うこと。基本的には１００件の検索リクエストを行なった場合一回ごとに15分sleepする必要がある
 * $short_sleep_seconds:１いいねごとのsleepする時間
 * $long_sleep_seconds:$onetime_get_numごとにsleepする時間
*/
if(count($argv) == 2){
  $keyword = $argv[1];
} else if(count($argv) > 2){
  print("Error:Too many arguments!\n");
  exit(1);
} else {
  print("Error:Too few arguments!\n");
  exit(1);
}


$short_sleep_seconds = 1; //秒数で指定。
$long_sleep_seconds = 901; //秒数で指定。default:901 < $long_sleep_seconds
$request_count = 10;
$onetime_get_num = 100; //default:100
$favorites_count = 0;

$t = 0;
while($t < $request_count){
	$statuses = $connection->get("search/tweets", ["q" => $keyword, "count" => $onetime_get_num]);
  $result = obj2arr($statuses);
  # print_r($result['errors'][0]->code);

	if(!isset($result['error'])){

		$size = sizeof($result['statuses']);

		//ツイートにいいね
		for ($i = 0;$i<$size;$i++) {
		    $connection->post('favorites/create', ['id' => $result['statuses'][$i]->id]);
		    sleep($short_sleep_seconds);
		    $favorites_count++;
		    print($favorites_count."件目のいいね\n");
		}

		$nextresults = $result['search_metadata'];

		// 100件取った後の100件があるかどうかをnext_resultsで判定し、無ければ処理を終了
		if (!$nextresults) {
			break;
		}

		print(($t+1)."周目終了\n");
		sleep($long_sleep_seconds);
		++$t;
	} else {
		print("Error: can not get tweets data\n");
    print("status:"+(string)$statuses);
    exit(1);
	}
}

// for ($t = 0; $t < $request_number; $t++) {
// 	$statuses = $connection->get("search/tweets", ["q" => $keyword, "count" => 100]);
// 	$result = obj2arr($statuses);

// 	//ツイートにいいね
// 	for ($i = 0;$i<sizeof($result['statuses']);$i++) {
// 	    $connection->post('favorites/create', ['id' => $result['statuses'][$i]->id]);
// 	    sleep(1);
// 	    print((string)(($t+1)*($i+1))."件目のいいね\n");
// 	}

// 	$nextresults = $result['search_metadata'];
// 	// next_results が無ければ処理を終了
// 	if (!$nextresults) {
// 		break;
// 	}
// 	print($i."周目終了");
// }
