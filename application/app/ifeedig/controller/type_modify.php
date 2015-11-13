<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '分类修改';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$tid = intval(array_shift($args));

foreach( app_config('titles') as $k => $v )
{
	$tits[$k] = $k; 
	$titles[$k] = $v;
}
unset( $titles[1] );
$data['fselect'] = $titles;

if( !in_array( $tid,$tits ) )
{
	info_page('错误的分类ID');
}

$data['tid'] = $tid;



layout( $data , 'default' , 'app' );
?>