<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '邀请好友';

$tab_type = 'index';


$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

layout( $data , 'default' , 'app' );

?>