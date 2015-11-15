<?php
//access_token 由公众号的AppID和appSecret组成，具有识别公众号的作用
//把它比作一个钥匙，通过token，公众号才能调用微信接口，微信服务器通过token判断该公众号是否有权限调用该接口
//全局唯一票据，调用各接口时都需要使用token，有效期为2个小时。
//应该采取一些策略减少调用token：将获取的token保存到数据库，两个小时后更新数据库
//步骤：获取，储存到数据库，查询，看时间是否过期，未过期则直接调用，过期了则重新获取并更新数据库
//$appid = "wxea2364b2dfd8449b";
//$secret = "76c13f50cffba030c594e87d468fbb27";
//$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";

//获取token
//$token = (array)json_decode(file_get_contents($url));
//$output=getToken($url);

//$token = (array)json_decode($output);

//file_get_contents不稳定
function getToken($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22");
	curl_setopt($ch, CURLOPT_ENCODING ,'gzip'); //加入gzip解析
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

//print_r($token);
//echo $token["access_token"];