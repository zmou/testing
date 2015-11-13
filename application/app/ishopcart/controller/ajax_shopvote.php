<?php
//include_once( dirname( __FILE__ ) . '/function.php'   );
header("Content-Type:text/xml;charset=utf-8");
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

if( !is_login() )
{
	info_page('请登录后查看');
}

$id = intval(array_shift( $args ));


$lines = lazy_get_line("SELECT * FROM `app_shopuser` WHERE `id` = '".intval($id)."' LIMIT 1");

if( !$lines )
{
	echo "0";
	die();
}
else
{
	$lines = json_encode( $lines );
	echo $lines;
	die();
}

?>