<?php
//require("../config.php");
//require("config.php");
//require("../common/curl.php");
//require("../common/base.php");
//require("common.php");

//
//header('Content-Type:text/html;charset= UTF-8'); 
//date_default_timezone_set('PRC');
//set_time_limit(9999); 
//
//$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
//mysql_select_db($dbname, $conn) or die('选择数据库失败');
//mysql_query("set names utf8;");

//readerdirect();

class boxoffice
{
	var $title;
	var $link;
	var $weekmoney;
	var $allmoney;
}			
			

function readerdirect()
{
	$url = array('http://movie.mtime.com/boxoffice/#CN/daily',
			'http://movie.mtime.com/boxoffice/#US/weekend',
			'http://movie.mtime.com/boxoffice/#world/weekend');

	$urlcat= array('大陆票房','北美票房','全球票房');			
				
	print_r($url);
	echo "开始获取票房纪录:</br>\n";
	foreach ($url as $key=>$eachurl)
	{
		echo $eachurl."</br>\n";		
		$buff = get_file_curl($eachurl);
		//如果失败，就使用就标记失败次数
		if(!$buff)
			continue;
		preg_match_all("/<h3><a href=\"http:\/\/movie.mtime.com\/([0-9-]+)\" target=\"\_blank\">(.*?)<\/a><\/h3>/s",$buff,$match1);
		print_r($match1);
		preg_match_all("/<p class=\"totalnum\"><strong>(.*?)<\/strong>(.*?)<\/p>/s",$buff,$match2);
		print_r($match2);
		
		$outputPlace = "<ol class=\"list20px bbli1 bc_f\">\n"; 
		$outputPlace .= "\t<li><span class=\"list\">排名</span><span class=\"name\">片名</span><span class=\"week\">周末票房</span><span class=\"allaum\">累计票房</span>\t</li>\n"; 		
		foreach ($match1[2] as $key2=>$eachmovie)
		{
			if($key2>=7)
			{
				continue;
			}
			$outputPlace .= "\t<li>";
			$boxofficeeach = new boxoffice();
			$boxofficeeach->title = $eachmovie;
			//找出对于的 title
			$link = get_movie_url($boxofficeeach->title);
			$boxofficeeach->link = $link;		
			$boxofficeeach->weekmoney = $match2[1][$key2*2].$match2[2][$key2*2];						
			$boxofficeeach->allmoney = $match2[1][$key2*2+1].$match2[2][$key2*2+1];
			print_r($boxofficeeach);
			$outputPlace .= '<span class="list">'.$key2."</span>";			
			$outputPlace .= '<span class="name"><a href="'.$link.'" target="_blank" >'.$boxofficeeach->title."</a></span>";
			$outputPlace .= '<span class="week">'.$boxofficeeach->weekmoney."</span>";
			$outputPlace .= '<span class="allaum">'.$boxofficeeach->allmoney."</span>"; 			
			$outputPlace .= "\t</li>\n";
		}		
		$outputPlace .= "</ol>"; 		
		$outputPlace .= "\t<div style=\"font-size:12px;margin:5px 0\"><span class=\"fl\">"."</span><span class=\"fl\"> <a href=\""."\" target=\"_blank\" rel=\"nofollow\">票房综述</a></span><span class=\"fr\"><a href=\"http://movie.mtime.com/boxoffice\" target=\"_blank\"  rel=\"nofollow\">更多>></a></span> \t</div>\n"; 		

		global $DH_input_path;
		if (!file_exists($DH_input_path.'top/'))  
		{   
			mkdir($DH_input_path.'top/',0777);
		}
		
		dh_file_put_contents($DH_input_path.'top/'.$key.'.top',$outputPlace);		
	}
}

function get_movie_url($title)
{
	global $conn,$DH_html_url;
	echo "</br>\n".$title.':';
	$sql="select * from page where title = '$title'";
	//echo $sql;
	$recs=dh_mysql_query($sql);
	$id = 0;
	if(mysql_num_rows($recs)>0)
	{
		$row = mysql_fetch_array($recs);
		$result =  $row['mediaid'].'-->'. $row['title'];
		$id = $row['id'];
		echo $result;
	}
	else
	{
		echo '查找 title 失败 :'."</br>\n";
		$sql="select * from page where aka like '%/$title/%'";
		$recs=dh_mysql_query($sql);
		if(mysql_num_rows($recs)>0)	
		{
			$row = mysql_fetch_array($recs);
			$result =  ' 2次查找 '.$row['mediaid'].'-->'. $row['title']."</br>\n";
			$id = $row['id'];
			echo $result;
		}
		else
		{	//最后没有办法，只能插入一条记录
			echo '查找 aka 失败 ';
		}	
	}
	return output_page_path($DH_html_url,$id);
}
?>
