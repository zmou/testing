<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$data['tab_type'] = 'add';
$data['tab_array'] = array('add' => '发起新主题' );
$fid = intval(array_shift( $args ));
$fid = $fid < 1?1:$fid;
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '$fid' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的讨论区id');
}
$data['fid'] = $fid;
$data['cate'] = iforum_get_cates($fid);

$data['ci_top_title'] = '发起新主题';
layout( $data , 'default' , 'app' );
?>