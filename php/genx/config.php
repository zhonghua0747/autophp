<?php

echo $_SERVER['DOCUMENT_ROOT']

//$DH_output_path= '/web/x2y4/';
//$DH_input_path= '/web/php/';
//$DH_home_url= 'http://v.x2y4.com/';
//$DH_search_input_path="/web/php/share/";
//$DH_search_output_path="/web/s/";

$DH_output_path= $_SERVER['DOCUMENT_ROOT'] . '../x2y4/';
$DH_input_path= $_SERVER['DOCUMENT_ROOT'];
$DH_home_url= 'http://v.x2y4.com/';
$DH_search_input_path=$_SERVER['DOCUMENT_ROOT']."/share/";
$DH_search_output_path=$_SERVER['DOCUMENT_ROOT'] . '../s/';

$DH_src_path= $DH_input_path. 'genx/';
$DH_html_path= $DH_src_path . 'html/';
$DH_output_html_path = $DH_output_path.'html/';
$DH_output_index_path = $DH_output_path.'index/';
$DH_html_url= $DH_home_url.'html/';
$DH_index_url= $DH_home_url.'index/';
$DH_name= '小二影视网';
$DH_name_des= '小二影视网_影视资源(电影/电视剧/动漫/综艺/纪录片)大全';



function dh_get_title($type,$title)
{
    $cat = '';
	if($type==1)
		$cat ='电影《'.$title.'(%pubyear%)》下载等资源链接汇总';
	if($type==2)
		$cat ='电视剧《'.$title.'》全集_在线等资源链接汇总';
	if($type==3)
		$cat ='综艺《'.$title.'》全集_在线等资源链接汇总';
	if($type==4)
		$cat ='动画(动漫)《'.$title.'》全集_在线等资源链接汇总';
	return $cat;
}
