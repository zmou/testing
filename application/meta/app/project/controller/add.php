<?php


if( !is_login() )
{
	info_page('请登录后查看');
}
if( !level_check( app_config('add_level') ) )
{
	info_page('您没有权限进行此操作');
}
include_once( dirname( __FILE__ ) . '/function.php'   );

$data = array();
$data['ci_top_title'] = '添加'.app_config('model_name');
$width = intval(app_config('pic_width'))?'width:'.intval(app_config('pic_width')).'px;':'90px';
$height = intval(app_config('pic_height'))?'height:'.intval(app_config('pic_height')).'px;':'120px';
$data['pic_style'] = ' style="'.$width.$height.'" ';
layout( $data , 'default' , 'app' );
?>