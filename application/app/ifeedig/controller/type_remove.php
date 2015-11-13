<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '删除分类';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$tid = intval(array_shift($args));
$config_data['titles'] = app_config('titles');
if( !isset( $config_data['titles'][$tid] ) )
{
	info_page('错误的分类ID');
}

unset( $config_data['titles'][$tid] );

save_app_config( $config_data );

info_page( '删除成功!' , '/app/native/'.$GLOBALS['app'].'/feed' , '点击这里返回!' );
?>