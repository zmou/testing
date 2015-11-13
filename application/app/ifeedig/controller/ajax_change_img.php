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

if( !is_admin() )
{
	info_page('你没有权限进行此次操作!');
}

$id = intval(array_shift( $args ));

$contents = file_get_contents( dirname(__FILE__) .'/snap.info.txt' );
$snap = unserialize( $contents );

//
if( isset( $snap[$id] ) )
{
	unset( $snap[$id] );
	$content = serialize( $snap );
	file_put_contents( dirname(__FILE__).'/snap.info.txt' , $content );
	echo '2';
	die();
}

$info = lazy_get_line("SELECT * FROM `app_feed` WHERE `id` = '".intval($id)."' LIMIT 1");
if( !$info )
{
	echo "error id";
	die();
}

$snap[$id] = $id;

$content = serialize( $snap );
file_put_contents( dirname(__FILE__).'/snap.info.txt', $content );

echo '1';
die();
?>