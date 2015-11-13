<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$data['tab_type'] = 'modify';
$data['tab_array'] = array('modify' => '修改主题' );
$id = intval(array_shift( $args ));
$data['page'] = intval(array_shift( $args ));
$data['return_page'] = intval(array_shift( $args ));
$data['return_key'] = intval(array_shift( $args ));
$line = lazy_get_line("SELECT * FROM `app_iforum_posts` WHERE id = '$id' AND `is_active` = 1 LIMIT 1");
if( !$line || $line['uid'] != format_uid() )
{
	info_page('你没有权限进行此操作');
}
$data['dis_id'] =  $line['parent_id'] == '0'?$id:$line['parent_id'];
$data['line'] = $line;

$data['cate'] = iforum_get_cates($line['fid']);
$data['ci_top_title'] = '修改主题';
layout( $data , 'default' , 'app' );
?>