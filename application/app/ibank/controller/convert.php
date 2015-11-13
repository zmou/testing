<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '仙豆银行';

$tab_type = 'convert';


$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

//$data['user'] = lazy_get_line( "SELECT * FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );
$data['account'] = lazy_get_line( "SELECT * FROM `app_ibank_account` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );


layout( $data , 'default' , 'app' );

?>