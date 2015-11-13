<?php

include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = '查看'.app_config('model_name').'评论';
if( !is_login() )
{
	info_page('请登录后查看');
}
$id = intval(array_shift( $args ));
$page = array_shift( $args );
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;
$limit = 99;
$data['start'] = $start = ($page-1)*$limit;
$id = $id < 1 ? 1 : $id;
$data['id'] = $id;
$data['userid'] = $uid = format_uid();


$data['com'] = $com = lazy_get_line( "SELECT * FROM `u2_comment` WHERE `id` = '".intval( $id )."' AND `mid` =  '".intval($mid)."'" );

$data['tid'] = $tid = $com['tid'];
if( !$com )
{
	info_page('参数错误!');
}

$data['coms'] = $coms = lazy_get_data( "SELECT sql_calc_found_rows * FROM `u2_comment_reply` WHERE `cid` = '".intval($id)."' ORDER BY `id` ASC LIMIT $start , $limit" );
$all = get_count();
$base = '/app/native/'.$GLOBALS['app'].'/show/'.$id;
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager(  $page , $page_all , $base);


$data['vote_user'] = $vote_user = lazy_get_data("SELECT `uid` FROM `u2_comment_vote` WHERE `cid` = '".intval($id)."' ORDER BY `id` DESC LIMIT 8");

$unames = array();

if( $coms || $vote_user )
{
	foreach( $coms as $v )
	{
		$users[$v['uid']] = $v['uid'];
	}
	foreach( $vote_user as $k )
	{
		$users[$k['uid']] = $k['uid'];
	}
}

$users[$com['uid']] = $com['uid'];
$data['unames'] = get_user_names($users);

$num = lazy_get_var( "SELECT COUNT(*) FROM `u2_comment_vote` WHERE `cid` = '".intval( $id )."' AND `uid` = '".intval( $uid )."' " );
if( $num == '0' )
{
	$data['votes'] = '0'; 
}
else
{
	$data['votes'] = '1'; 
}

//$data['bind'] = $bind;

foreach( $bind as $k => $v )
{
	$selected[] = " `$v` as $k ";
}

$data['item'] = lazy_get_line("SELECT ".join( ',' , $selected )." FROM `app_content_{$mid}` WHERE `id` = '".intval($tid)."'");

$pic_width = app_config('pic_width');
$pic_height = app_config('pic_height');
$pic_style = NULL;
if( $pic_width || $pic_height )
{
	$width = $pic_width?'width:'.$pic_width.'px;':NULL;
	$height = $pic_height?'height:'.$pic_height.'px;':NULL;
	$pic_style = ' style="'.$width.$height.'" ';
}
$data['pic_style'] = $pic_style;

layout( $data , 'default' , 'app' );
?>