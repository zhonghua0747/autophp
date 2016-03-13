<?php
/////////////////////////////////////////////////////
/// 函数名称： 
/// 函数作用：
/// 函数作者: DH
/// 作者地址: http://dhblog.org 
/////////////////////////////////////////////////////
//header('Content-Type:text/html;charset= UTF-8'); 
////导出数据库设置
//require("../config.php");
//require("common.php");
//require("page_navi.php");
//
//set_time_limit(3600); 


function gen_sitemap($lists)
{
	//一页网页地图使用多少个项
	//资料：Google允许的sitemap数量是1000，现在提升到50000，但是这是理论数据。根据测试，实际情况所对应的以下数据为佳。
	//调整前：理论最大1000条URL，实际500以下为佳，
	//调整后：理论最大50000条URL，实际2500以下为佳。
	$pagecount=2500;
	//向前多生成几页
	$pregen=3;
	
	$list_count=count($lists);
	$times=ceil($list_count/$pagecount);
	$i=$times;
	$j=$times-$pregen;
	$begin=$j>0?$j:0;
	echo $times."--> ";
	echo $begin."\n";

	while($i>$begin)
	{
		echo $i."\n";
		echo $pagecount*($i).'-->'.$pagecount*($i-1)."\n";
		$sql="select * from page where id>".$pagecount*($i-1)." and id <=".$pagecount*$i." order by id desc";
		echo $sql."\n";
		genhtml($sql,sprintf("%03d",$i),$times);
		gen_sitemap_each($sql,date("Y-m-d H:i:s"),'weekly',sprintf("%03d",$i),sprintf("%03d",$times));
		$i--;
	}

	//安月生成每个sitemap
	genhtml2();
	gen_siteindex(date("Y-m-d H:i:s"));
	gen_sitemapall();
}

//产生百度和google的网站地图
function gen_xml($date,$cycle,$lists,$pagecount)
{	
	global $DH_src_path,$DH_output_path,$DH_home_url,$DH_html_url;
	$sitemappath=$DH_output_path.'sitemapxml/';
	if (!file_exists($sitemappath))  
		mkdir($sitemappath,0777);
	
	$timetmp = strtotime($date);
	$updatetime = date("Y-m-d",$timetmp)."T".date("H:i:s",$timetmp)."+00:00";

	$list_count=count($lists);
	$times=ceil($list_count/$pagecount);	
	
	
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap.xml';
	$DH_sitemap = dh_file_get_contents("$DH_input_html");
	$DH_sitemap = str_replace("%lastdate%",$updatetime,$DH_sitemap);
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap_each.xml';
	$DH_sitemap_each = dh_file_get_contents("$DH_input_html");	
	
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap_baidu.xml';
	$DH_sitemap_baidu = dh_file_get_contents("$DH_input_html");	
	$DH_sitemap_baidu = str_replace("%lastdate%",$updatetime,$DH_sitemap_baidu);
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap_baidu_each.xml';
	$DH_sitemap_baidu_each = dh_file_get_contents("$DH_input_html");		
	
	$sitemap_all='';
	$sitemap_baidu_all='';
	
	//如果是每周更新，那么是第一个页面，就需要添加首页的链接
	if($cycle==='weekly')
	{
		$sitemap_each = str_replace("%url%",$DH_home_url,$DH_sitemap_each);
		$sitemap_each = str_replace("%updatetime%",$updatetime,$sitemap_each);
		$sitemap_each = str_replace("%cycle%",'daily',$sitemap_each);
		$sitemap_each = str_replace("%priority%",'1.0',$sitemap_each);
		$sitemap_all.=$sitemap_each;
		
		$sitemap_baidu_each = str_replace("%url%",$DH_home_url,$DH_sitemap_baidu_each);		
		$sitemap_baidu_each = str_replace("%updatetime%",$updatetime,$sitemap_baidu_each);	
		$title = '二手电影-影视资源导航';	
		$sitemap_baidu_each = str_replace("%title%",$title,$sitemap_baidu_each);		
		$sitemap_baidu_all.=$sitemap_baidu_each;		
	}
	$i=0;
	$rlists=array_reverse($lists);
	//print_r($rlists);
	
	foreach($rlists as $key=>$list)
	{
		$i++;
		$pagesindex=$list_count - $i;
		$htmlpath = output_page_path($DH_html_url,$pagesindex);
		$sitemap_each = str_replace("%url%",$htmlpath,$DH_sitemap_each);
		$timetmp = strtotime($key.'00');
		$updatetime = date("Y-m-d",$timetmp)."T".date("H:i:s",$timetmp)."+00:00";
		
		$sitemap_each = str_replace("%updatetime%",$updatetime,$sitemap_each);
		$sitemap_each = str_replace("%cycle%",$cycle,$sitemap_each);
		$sitemap_each = str_replace("%priority%",'0.2',$sitemap_each);
		$sitemap_all.=$sitemap_each;
				
		$sitemap_baidu_each = str_replace("%url%",$htmlpath,$DH_sitemap_baidu_each);
		$updatetime2=date('Y-m-d H:i:s',strtotime($key.'00'));
		$sitemap_baidu_each = str_replace("%updatetime%",$updatetime2,$sitemap_baidu_each);	
		preg_match('/<\_T>(.*?)<\/\_T>/s',$list,$matchT);
		$sitemap_baidu_each = str_replace("%title%",$matchT[1],$sitemap_baidu_each);		
		$sitemap_baidu_all.=$sitemap_baidu_each;
		if($i%$pagecount==0)
		{
			$page = ceil($i/$pagecount);
			$DH_sitemap = str_replace("%sitemaps%",$sitemap_all,$DH_sitemap);
			$DH_sitemap_baidu = str_replace("%sitemaps%",$sitemap_baidu_all,$DH_sitemap_baidu);
			
			$DH_output_file = $sitemappath.'sitemap'.$page.'.xml';
			dh_file_put_contents($DH_output_file,$DH_sitemap);
			$DH_output_file = $sitemappath.'sitemap_baidu'.$page.'.xml';
			dh_file_put_contents($DH_output_file,$DH_sitemap_baidu);
			
			$sitemap_all='';
			$sitemap_baidu_all='';
		}		
	}	
	
	if($sitemap_all!='')
	{
		$page = ceil($i/$pagecount);
		$DH_sitemap = str_replace("%sitemaps%",$sitemap_all,$DH_sitemap);
		$DH_sitemap_baidu = str_replace("%sitemaps%",$sitemap_baidu_all,$DH_sitemap_baidu);
		$DH_output_file = $sitemappath.'sitemap'.$page.'.xml';
		dh_file_put_contents($DH_output_file,$DH_sitemap);
		$DH_output_file = $sitemappath.'sitemap_baidu'.$page.'.xml';
		dh_file_put_contents($DH_output_file,$DH_sitemap_baidu);
	}
	
	$DH_output_file = $DH_output_path.'sitemap.xml';
	dh_file_put_contents($DH_output_file,$DH_sitemap);
	$DH_output_file = $DH_output_path.'sitemap_baidu.xml';
	dh_file_put_contents($DH_output_file,$DH_sitemap_baidu);	
}

//按照固定的页面数目产生html索引
function gen_html_num($lists,$pagecount)
{
	global $DH_html_url,$DH_home_url,$DH_output_path,$DH_src_path;
	$sitemappath=$DH_output_path.'sitemaphtml/';
	if (!file_exists($sitemappath))  
		mkdir($sitemappath,0777);
		
	$list_count=count($lists);
	$times=ceil($list_count/$pagecount);	
		
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap.html';
	$DH_output_content = dh_file_get_contents("$DH_input_html");
	$DH_output_content = str_replace("%home%",$DH_home_url,$DH_output_content);
	
	$liout='';
	$i=0;
	$rlists=array_reverse($lists);
	
	$sitemaphtml='';
	foreach($rlists as $key=>$list)
	{
		$i++;
		//类别
		$type="";
		preg_match('/<_c>(.*?)<\/\_c>/',$list,$match);
		if(!empty($match[1]))
		{	
			$type = $match[1];
		}
		preg_match('/<\_T>(.*?)<\/\_T>/s',$list,$matchT);
		$pagesindex=$list_count - $i;
		$htmlpath = output_page_path($DH_html_url,$pagesindex);
		$updatetime=date('Y-m-d',strtotime($key.'00'));
		$liout.='<li> '.$i.' ['.$updatetime.']'.$type.'] <a href="'.$htmlpath.'" target="_blank">'.$matchT[1]."</a></li>\n";
		//如果达到达到要求，开始写文件
		if($i%$pagecount==0)
		{
			$page = ceil($i/$pagecount);
			$sitemaphtml = str_replace("%num%",'第 '.$page.' 页',$DH_output_content);
			$sitemaphtml = str_replace("%list%",$liout,$sitemaphtml);		
			$pagenavi = dh_pagenavi(9,$times,$DH_home_url.'sitemaphtml/sitemap',$page);		
			$sitemaphtml= str_replace("%pagenavi%",$pagenavi,$sitemaphtml);
			$DH_output_file = $sitemappath.'sitemap'.$page.'.html';
			dh_file_put_contents($DH_output_file,$sitemaphtml);	
			$liout='';
		}
	}
	//说明正好是整数倍
	if($liout!='')
	{	
		$page = ceil($i/$pagecount);
		$sitemaphtml = str_replace("%num%",'第 '.$page.' 页',$DH_output_content);
		$sitemaphtml = str_replace("%list%",$liout,$sitemaphtml);		
		$pagenavi = dh_pagenavi(9,$times,$DH_home_url.'sitemaphtml/sitemap',$page);		
		$sitemaphtml= str_replace("%pagenavi%",$pagenavi,$sitemaphtml);
		$DH_output_file = $sitemappath.'sitemap'.$page.'.html';
		dh_file_put_contents($DH_output_file,$sitemaphtml);		
	}
	$DH_output_file = $DH_output_path.'sitemap.html';
	dh_file_put_contents($DH_output_file,$sitemaphtml);
}

//按照年份产生索引
function gen_html_date($lists)
{
	global $DH_html_url,$DH_home_url,$DH_output_path,$DH_src_path;
	$sitemappath=$DH_output_path.'sitemaphtml/';
	if (!file_exists($sitemappath))  
		mkdir($sitemappath,0777);
	
	$DH_input_html  = $DH_src_path . 'sitemap/sitemap.html';
	$DH_output_content = dh_file_get_contents("$DH_input_html");
	$DH_output_content = str_replace("%home%",$DH_home_url,$DH_output_content);


	$liout='';
	$updatetimepageold='';
	$i=0;
	foreach($lists as $key=>$list)
	{	
		$i++;
		$updatetimepagenew=date('Y',strtotime($key.'00'));
		//如果有变化，说明需要输出了
		if($updatetimepageold=='')
			$updatetimepageold = $updatetimepagenew;
		else if($updatetimepagenew!=$updatetimepageold)
		{
			$DH_output_file = $sitemappath.'sitemap_'.$updatetimepageold.'.html';
			$sitemaphtml = str_replace("%pagenavi%",'',$DH_output_content);
			$sitemaphtml = str_replace("%num%",$updatetimepageold.' 年份',$sitemaphtml);
			$sitemaphtml = str_replace("%list%",$liout,$sitemaphtml);
			dh_file_put_contents($DH_output_file,$sitemaphtml);
			$updatetimepageold = $updatetimepagenew;
			$liout='';
		}
		//类别
		$type="";
		preg_match('/<_c>(.*?)<\/\_c>/',$list,$match);
		if(!empty($match[1]))
		{	
			$type = $match[1];
		}
		preg_match('/<\_T>(.*?)<\/\_T>/s',$list,$matchT);
		//print_r($matchT);
		$htmlpath = output_page_path($DH_html_url,$i);
		$updatetime=date('Y-m-d',strtotime($key.'00'));
		$liout.='<li> '.$i.' ['.$updatetime.']['.$type.'] <a href="'.$htmlpath.'" target="_blank">'.$matchT[1]."</a></li>\n";			
	}
	$DH_output_file = $sitemappath.'sitemap_'.$updatetimepageold.'.html';
	$sitemaphtml = str_replace("%pagenavi%",'',$DH_output_content);
	$sitemaphtml = str_replace("%num%",$updatetimepageold.' 月份',$sitemaphtml);
	$sitemaphtml = str_replace("%list%",$liout,$sitemaphtml);
	dh_file_put_contents($DH_output_file,$sitemaphtml);	
}

//生成所有sitemap的siteindex
function gen_siteindex($date)
{
	global $DH_output_path,$DH_src_path,$DH_home_url,$DH_home_url;
	
	$timetmp = strtotime($date);
	$date = date("Y-m-d",$timetmp)."T".date("H:i:s",$timetmp)."+00:00";	
	
	$DH_input_html  = $DH_src_path . 'sitemap/siteindex.xml';
	$DH_siteindex = dh_file_get_contents("$DH_input_html");
	$DH_input_html  = $DH_src_path . 'sitemap/siteindex_each.xml';
	$DH_siteindex_each = dh_file_get_contents("$DH_input_html");	
	$DH_siteindex_each = str_replace("%home%",$DH_home_url,$DH_siteindex_each);
	
	$DH_input_html  = $DH_src_path . 'sitemap/robots.txt';
	$DH_robots = dh_file_get_contents("$DH_input_html");
		
	$siteindex_all='';
	$siteindex_baidu_all='';
	$siterobots='';
	
	$files = scandir($DH_output_path.'sitemapxml',1);
	echo "<b>Files in " . $DH_output_path.'sitemapxml' . ":</b><br/>\n";
	foreach($files as $key=>$file)
	{
		$ext=strrchr($file,'.');
		if($ext!='.xml')
			continue;
		//得到sitemap的时间	
		preg_match("/([0-9]{8})/",$file,$match);
		//print_r($match);
		
		// 选出是哪个类型的sitemap
		if(strstr($file,'sitemap_baidu'))
		{
			echo "sitemap_baidu:$file<br/>";
			$siteindex_baidu_each = str_replace("%sitemap%",'sitemapxml/'.$file,$DH_siteindex_each);
			if(!empty($match[1]))
			{
				$timetmp = strtotime($match[1]);
				$updatetime = date("Y-m-d",$timetmp)."T".date("H:i:s",$timetmp)."+00:00";
				$siteindex_baidu_each = str_replace("%date%",$updatetime,$siteindex_baidu_each);
			}
			else
			{
				$siteindex_baidu_each = str_replace("%date%",$date,$siteindex_baidu_each);
			}
			$siteindex_baidu_all .=$siteindex_baidu_each;
		}
		if(strstr($file,'sitemap')||(strstr($file,'sitemap_baidu')))
		{
			echo "sitemap:$file<br/>";
			$siteindex_each = str_replace("%sitemap%",'sitemapxml/'.$file,$DH_siteindex_each);
			if(!empty($match[1]))
			{
				$timetmp = strtotime($match[1]);
				$updatetime = date("Y-m-d",$timetmp)."T".date("H:i:s",$timetmp)."+00:00";
				$siteindex_each = str_replace("%date%",$updatetime,$siteindex_each);
			}
			else
			{
				$siteindex_each = str_replace("%date%",$date,$siteindex_each);
			}			
			$siteindex_all .=$siteindex_each;
			$siterobots	.="\nSitemap: ".$DH_home_url.$file;	
		}	
	}	
	
	$o_siteindex = str_replace("%sitemaps%",$siteindex_all,$DH_siteindex);
	$o_siteindex_baidu = str_replace("%sitemaps%",$siteindex_baidu_all,$DH_siteindex);
	$o_siterobots = str_replace("%sitemaps%",$siterobots,$DH_robots);
	
	$DH_output_file = $DH_output_path.'siteindex.xml';
	dh_file_put_contents($DH_output_file,$o_siteindex);	
	$DH_output_file = $DH_output_path.'siteindex_baidu.xml';	
	dh_file_put_contents($DH_output_file,$o_siteindex_baidu);
	$DH_output_file = $DH_output_path.'robots.txt';	
	dh_file_put_contents($DH_output_file,$o_siterobots);	
}

function gen_sitemapall()
{
	global $DH_output_path,$DH_src_path,$DH_home_url,$DH_home_url;
	
	$DH_input_html  = $DH_src_path . 'sitemap/sitemapindex.html';
	$DH_siteindex = dh_file_get_contents("$DH_input_html");	
	$DH_siteindex = str_replace("%home%",$DH_home_url,$DH_siteindex);
		
	$siteindex1='';
	$siteindex2='';
	$siteindex3='';
	$siteindex4='';
	// 用 opendir() 打开目录，失败则中止程序
//	$handle = @scandir($DH_output_path.'sitemaphtml',1) or die("Cannot open " . $dir);
	$files = scandir($DH_output_path.'sitemaphtml',1);
	echo "<b>Files in " . $DH_output_path . ":</b><br/>\n";
	// 用 readdir 读出文件列表
	//while($file = readdir($handle))
	//{
	foreach($files as $key=>$file)
	{
		$ext=strrchr($file,'.');
		if($ext!='.html')
			continue;
			
		// 选出是哪个类型的sitemap
		if(strstr($file,'sitemap_'))
		{
			echo "sitemap mouth :$file<br/>";
			$siteindex2 .='<li><a href="'.$DH_home_url.'sitemaphtml/'.$file.'">'.$file.'</a></li>';
		}
		else
		{
			echo "sitemap 10000 :$file<br/>";
			$siteindex1 .='<li><a href="'.$DH_home_url.'sitemaphtml/'.$file.'">'.$file.'</a></li>';		
		}
	}
// 关闭目录读取
//	closedir($handle);

	$files = scandir($DH_output_path.'sitemapxml',1);
	echo "<b>Files in " . $DH_output_path . ":</b><br/>\n";
	foreach($files as $key=>$file)
	{
		$ext=strrchr($file,'.');
		if($ext!='.xml')
			continue;
			
		// 选出是哪个类型的sitemap
		if(strstr($file,'sitemap_baidu'))
		{
			echo "sitemap baidu xml :$file<br/>";
			$siteindex4 .='<li><a href="'.$DH_home_url.'sitemapxml/'.$file.'">'.$file.'</a></li>';
		}
		else
		{
			echo "sitemap google xml :$file<br/>";
			$siteindex3 .='<li><a href="'.$DH_home_url.'sitemapxml/'.$file.'">'.$file.'</a></li>';	
		}
	}
	
	$DH_siteindex = str_replace("%list1%",$siteindex1,$DH_siteindex);
	$DH_siteindex = str_replace("%list2%",$siteindex2,$DH_siteindex);
	$DH_siteindex = str_replace("%list3%",$siteindex3,$DH_siteindex);
	$DH_siteindex = str_replace("%list4%",$siteindex4,$DH_siteindex);
	
	$DH_output_file = $DH_output_path.'siteindex.html';	
	dh_file_put_contents($DH_output_file,$DH_siteindex);	
}
?>
