<?php
require_once 'facepp_sdk.php';
//为什么这个官网的SDK却不行？
//难道是因为这个SDK用的是curl方法吗？用curl来POST请求数据，为什么就是不行呢？
//todo 这个我很难理解啊，请求微软的API也是这样的，到底是哪里出了错误啊……
//todo 解决啦！果然还是debug厉害，那本书怎么不教怎么debug呢，哈哈，这个demo太老了，那个图片失效啦，哈哈,不是图片的问题……还得继续探究
//Curl failed with error #26: couldn't open file "http://www.faceplusplus.com
#.cn/wp-content/themes/faceplusplus/assets/img/demo/1.jpg"
########################
###     example      ###
########################
$facepp = new Facepp();
$facepp->api_key       ="5ab70241a2a2d6e7a4f10b5f79385526";
$facepp->api_secret    = 'pwhInerTEiE2FPQKRgoRZlw5vkzdJ-WF';

#detect local image
//todo 原来是这个出了问题，哈哈哈哈，这个是通过本地的文件来识别的，但是我不知道是什么原理，注释掉，用第二个通过url来定位图片，哈哈，果然就好啦！
//$params['img']          = 'qq.jpg';
$params['attribute']    = 'gender,age,race,smiling,glass,pose';

//$response               = $facepp->execute('/detection/detect',$params);
//print_r($response);

#detect image by url
$params['url']          = 'http://pic9.nipic.com/20100813/5301697_092941923176_2.jpg';
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

