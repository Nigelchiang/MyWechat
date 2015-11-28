<?php
require_once 'facepp_sdk.php';
//为什么这个官网的SDK却不行？
//难道是因为这个SDK用的是curl方法吗？用curl来POST请求数据，为什么就是不行呢？
//todo 这个我很难理解啊，请求微软的API也是这样的，到底是哪里出了错误啊……
########################
###     example      ###
########################
$facepp = new Facepp();
$facepp->api_key       ="5ab70241a2a2d6e7a4f10b5f79385526";
$facepp->api_secret    = 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF';

#detect local image 
$params['img']          = 'http://www.faceplusplus.com.cn/wp-content/themes/faceplusplus/assets/img/demo/1.jpg';
$params['attribute']    = 'gender,age,race,smiling,glass,pose';

$response               = $facepp->execute('/detection/detect',$params);
print_r($response);

#detect image by url
$params['url']          = 'http://www.faceplusplus.com.cn/wp-content/themes/faceplusplus/assets/img/demo/1.jpg';
$response               = $facepp->execute('/detection/detect',$params);
print_r($response);

if($response['http_code'] == 200) {
    #json decode 
    $data = json_decode($response['body'], 1);
    
    #get face landmark
    foreach ($data['face'] as $face) {
        $response = $facepp->execute('/detection/landmark', array('face_id' => $face['face_id']));
        print_r($response);
    }
    
    #create person 
    $response = $facepp->execute('/person/create', array('person_name' => 'unique_person_name'));
    print_r($response);

    #delete person
    $response = $facepp->execute('/person/delete', array('person_name' => 'unique_person_name'));
    print_r($response);

}

