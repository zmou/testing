<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '我的衣柜';

$tab_type = 'cloth';

$page = array_shift( $args );
$page = intval($page) < 1 ?1:intval($page);
$limit = '6';
$start = ($page-1)*$limit;
$base = '/app/native/ihome/cloth';
$sql = "SELECT count(*) FROM `app_ihome_shop` WHERE `uid` = '" .format_uid(). "' ";
$count = lazy_get_var($sql);
$page_all = ceil( $count /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base );

$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

//$data['user'] = lazy_get_line( "SELECT * FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );

$data['shop'] = lazy_get_data( "SELECT * FROM `app_ihome_shop` as s LEFT JOIN `app_ishop_items` as i ON ( s.item_id = i.fid )   WHERE `uid` = '" . format_uid() . "' order by s.id desc LIMIT $start , $limit " );



layout( $data , 'default' , 'app' );

?>