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


layout( $data , 'default' , 'app' );
?>