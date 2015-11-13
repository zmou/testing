<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '支付';
if( !is_login() )
{
	info_page('请登录后查看');
}


$data['pay'] = stripslashes(@file_get_contents( dirname( __FILE__ ).'/pay.txt' ));

layout( $data , 'default' , 'app' );
?>