<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nigel
 * Date: 2015/11/16
 * Time: 20:04
 */
$textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
$newsTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>%s</ArticleCount>
            <Articles>
            <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[picurl]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>".
//            <item>
//            <Title><![CDATA[%s]]></Title>
//            <Description><![CDATA[%s]]></Description>
//            <PicUrl><![CDATA[%s]]></PicUrl>
//            <Url><![CDATA[%s]]></Url>
//            </item>
            "</Articles>
            </xml> ";
$musicTpl="<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[music]]></MsgType>
            <Music>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
            </Music>
            </xml>";