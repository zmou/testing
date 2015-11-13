<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = 'Feed修改';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$fid = intval(array_shift($args));

if( !isset( $fid ) || $fid < 1 )
{
	info_page("ID错误!");
}

$feed = lazy_get_line("SELECT * FROM `app_feed` WHERE `id` = '".intval( $fid )."' LIMIT 1");
if( !$feed )
{
	info_page('没有此条Feed!');
}

$data['feed'] = $feed;

$data['fid'] = $fid;

//type
foreach( app_config('titles') as $k => $v )
{
	$titles[$k] = $v;
}
unset( $titles[1] );
$data['fselect'] = $titles;


$data['state'] = app_config('state');
layout( $data , 'default' , 'app' );
?> 

