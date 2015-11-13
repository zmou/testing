<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$fid = intval(array_shift( $args ));
$tab_type = array_shift( $args );
$tab_type = !$tab_type?'basic':$tab_type;
$fid = $fid < 1?1:$fid;
$data = array();
$data['forum'] = lazy_get_line( "SELECT * from `app_iforum_status` where `id` = '$fid' and `is_active` = '1' limit 1");
if(!$data['forum'])
{
	info_page('错误的论坛id');
}
$data['fid'] = $fid;
$data['ci_top_title'] = '修改论坛';
$data['cates'] = iforum_get_cates($fid);

$data['tab_type'] = $tab_type;
$data['tab_array'] = array('basic' => '基础设置','cates'=>'分类设置' );
layout( $data , 'default' , 'app' );
?>