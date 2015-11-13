<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
// clear data

$type = intval(v('type')); 
$name = z(v('name'));
$desp = n(v('desp'));

if( $type < 1 )
{
	info_page( '请为箱子选择用途' );
}

if( strlen( $type ) < 1 )
{
	info_page( '箱子的名字不能为空哦' );
}

$data = array();


$data['type'] = $type;
$data['name'] = $name;
$data['timeline'] = date("Y-m-d H:i:s");
$data['desp'] = $desp;

$data['uid'] = format_uid();

global $CI;
$CI->load->database();
$CI->db->insert( 'app_icase_case' , $data );


header('Location: /app/native/' . $GLOBALS['app'] . '/index');
?>