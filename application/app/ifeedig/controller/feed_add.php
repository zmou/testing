<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '添加Feed';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}


$feed_id = intval(array_shift($args));


if( $feed_id != '0' )
{
	$feed = lazy_get_var("SELECT `feed` FROM `app_feed_recommend` WHERE `id` = '".$feed_id."' LIMIT 1");
}
else
{
	
	$feed = v('fblog');
	if( !$feed || $feed == 'http://' )
	{
		info_page('你提交的博客地址错误');
	}

	if( strpos( $feed , 'http://' ) === false )
	{
		$feed = 'http://'.$feed;
	}
}

$CI =&get_instance();
$CI->load->library('simplepie');
MakeDir(ROOT.'static/data/cache');
$CI->simplepie->set_cache_location(ROOT.'static/data/cache'); 
$CI->simplepie->set_feed_url( $feed ); 
$CI->simplepie->init();

$data['ftitle'] = $CI->simplepie->get_title();  //标题
$flink = $CI->simplepie->get_link(); //连接地址
if( !$flink )
{
	$flink = $feed;
}

$data['flink'] = $flink;

//delete feed
lazy_run_sql("DELETE FROM `app_feed_recommend` WHERE `feed` = '".$feed."'");


//type list
foreach( app_config('titles') as $k => $v )
{
	$titles[$k] = $v;
}
unset( $titles[1] );
$data['fselect'] = $titles;


$data['state'] = app_config('state');

layout( $data , 'default' , 'app' );
?>