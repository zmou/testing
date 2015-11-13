<?php

include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '添加'.app_config('model_name').'评论';
if( !is_login() )
{
	info_page('请登录后查看');
}
//$fid = intval(array_shift( $id ));
$id = array_shift( $args );
//$type = intval(array_shift( $args ));

if( $id < 1 )
{
	info_page('参数错误!');
}

$data['id'] = $id;

layout( $data , 'default' , 'app' );

?>