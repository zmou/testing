<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '添加分类';
if( !is_login() )
{
	info_page('请登录后查看');
}

if( !is_admin() )
{
	info_page('你没有权限进行操作!');
}

$tname = trim(z(v( 'tname' )));
if( empty($tname) )
{
	info_page('分类名称不能为空!');
}

$config_data['titles'] = app_config('titles');

$config_data['titles'][] = $tname ;

save_app_config( $config_data );

info_page( '添加成功!' , '/app/native/'.$GLOBALS['app'].'/feed' , '点击这里返回!' );
?>