<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '分享';

$id = array_shift( $args );

$id = intval($id);
	
$where = " f.id = '$id' ";	


$data['fav'] = lazy_get_data( "SELECT *,f.id as fid  FROM `app_fav` as f LEFT JOIN `u2_user` as u ON ( f.uid = u.id ) WHERE $where LIMIT 1" );

//$data['user'] = lazy_get_data( "SELECT * FROM `u2_user` LIMIT 1" );
//echo '123';
layout( $data , 'default' , 'app' );

?>