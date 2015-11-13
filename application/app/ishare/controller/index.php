<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '分享';

$view_type = array_shift( $args );

if( $view_type == 'friends' )
{
	$fid = get_friends_by_uid();
	
	if( $fid )
	{
		$where = " uid IN ( " . join( ' , ' , $fid ) . " ) ";
	}
	else
	{
		$where = " 0 ";
	}
	
}
elseif( $view_type == 'self' )
{
	
	$where = " uid = '" . format_uid() . "' ";
}
else
{
	$view_type = 'all';
	$where = " 1 ";
}

$data['view_type'] = $view_type;
$page = array_shift( $args );
$page = intval($page) < 1 ?1:intval($page);
$limit = '10';
$start = ($page-1)*$limit;

$data['fav'] = lazy_get_data( "SELECT sql_calc_found_rows *,f.id as fid  FROM `app_fav` as f LEFT JOIN `u2_user` as u ON ( f.uid = u.id ) WHERE $where ORDER BY `time` DESC  LIMIT $start , $limit " );
$sql = "select found_rows()";
$all = lazy_get_var( $sql );
$base = '/app/native/ishare/index/'.$view_type;
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base );

//$data['user'] = lazy_get_data( "SELECT * FROM `u2_user` LIMIT 1" );
//echo '123';
layout( $data , 'default' , 'app' );

?>