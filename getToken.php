<?php
//access_token �ɹ��ںŵ�AppID��appSecret��ɣ�����ʶ���ںŵ�����
//��������һ��Կ�ף�ͨ��token�����ںŲ��ܵ���΢�Žӿڣ�΢�ŷ�����ͨ��token�жϸù��ں��Ƿ���Ȩ�޵��øýӿ�
//ȫ��ΨһƱ�ݣ����ø��ӿ�ʱ����Ҫʹ��token����Ч��Ϊ2��Сʱ��
//Ӧ�ò�ȡһЩ���Լ��ٵ���token������ȡ��token���浽���ݿ⣬����Сʱ��������ݿ�
//���裺��ȡ�����浽���ݿ⣬��ѯ����ʱ���Ƿ���ڣ�δ������ֱ�ӵ��ã������������»�ȡ���������ݿ�
//$appid = "wxea2364b2dfd8449b";
//$secret = "76c13f50cffba030c594e87d468fbb27";
//$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";

//��ȡtoken
//$token = (array)json_decode(file_get_contents($url));
//$output=getToken($url);

//$token = (array)json_decode($output);

//file_get_contents���ȶ�
function getToken($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22");
	curl_setopt($ch, CURLOPT_ENCODING ,'gzip'); //����gzip����
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

//print_r($token);
//echo $token["access_token"];