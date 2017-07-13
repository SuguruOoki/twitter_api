<?php
//twitteroauth.phpを読み込み
// require_once dirname(__FILE__) .'/twitteroauth.php';
require_once 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
//検索ワード配列
$keyword_list = array("ポケモン","パズドラ");
//最大検索数
$countmax = 10;
//twitterAppsで取得

$consumerKey = 'ZZ2p3Yo30t7aYDiEMrnlmnf4m';
$consumerSecret = 'xqHCdNkYGGD3y33FPO10PREpiZul3kZcTbQpMp5x9eK9FFAr5C';
$accessToken = '786480048765140992-RUpEyFB18F4BwhDQhWen30ffqLTdj4b';
$accessTokenSecret = 'lwT77kqwololm540hZaiAAC2QF0Ft1XLzSuW4k44xsThx';

$connection = new TwitterOAuth(
    $consumerKey,
    $consumerSecret,
    $accessToken,
    $accessTokenSecret
);
//Twitterで検索するワード
//複数の場合はORかANDを使う
//「ポケモン OR パズドラ」のような形になればいい
$key = "";
$size = count($keyword_list);
for($i=0;$i<$size;$i++){
    $keyword = $keyword_list[$i];
    $key .= $keyword;
    if($i<$size-1){
        $key .= " AND ";
    }
}
//オプション設定
//countmaxは最大検索数
$options = array('q'=>$key,'count'=>$countmax,'lang'=>'ja');
//検索
// $json = $to->OAuthRequest(
//     'https://api.twitter.com/1.1/search/tweets.json',
//     'GET',
//     $options
// );
$json = $connection->get("search/tweets", $options);
// print_r($json);

$jset = json_decode(json_encode($json), true);
//print_r($jset['statuses'][8]['text']."\n");
//tweetidを取得
foreach ($jset['statuses'] as $result) {
    //ローマ字の名前
    // $screen_name = $result['user']['screen_name'];
    //ユーザーID(数字)
    $id = $result['user']['id'];
    //ユーザー名
    $name = $result['user']['name'];
    echo $id.":".$name."\n";
    //ユーザーアイコン画像URL
    // $link = $result['user']['profile_image_url'];
    // //該当ツイート
    // $content = $result['text'];
    // //更新日
    // $updated = $result['created_at'];
    // $time = date("Y-m-d H:i:s",strtotime($updated));
    // //不明なものはprint_rで見ればいい
    // //print_r($result);
    // echo "<img src='".$link."''>"." | ".$screen_name." | ".$id." | ".$name." | ".$content." | ".
    //     $time."<br>";
}
?>