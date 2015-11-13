<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(array_shift( $args ));
if( $id < 1 )
{
	info_page('错误的id');
}
$text = urldecode( array_shift( $args ) );
if( !$text )
{
	info_page('错误的参数');
}
$page = intval(array_shift( $args ));
$page = ( $page < 1 )?1:$page;
$limit = 5;
$start = ($page - 1)*$limit;
$base = '/app/native/'.$GLOBALS['app'].'/votepeople/'.$id.'/'.urlencode($text);
$data = array();
$data['text'] = $text;
$data['ci_top_title'] = '谁'.$text.'这'.app_config('model_name');
$uids =  lazy_get_vars( "select sql_calc_found_rows `uid` from `u2_vote` where `text` = '$text' and `tid` = '$id' and `mid` = '$mid' limit $start , $limit" );
$all = get_count();
$page_all = ceil( $all / $limit );
$data['id'] = $id;
if( $uids )
{
	$data['names'] = get_name_by_uids( $uids );
}
else
{
	info_page('错误的参数');
}
$data['pager'] = get_pager( $page , $page_all , $base );
layout( $data , 'default' , 'app' );
?>