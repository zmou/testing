<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$data['ci_top_title'] = '查看'.app_config('model_name');
$id = intval(array_shift( $args ));
$page = array_shift( $args );
$id = $id < 1?1:$id;
$data['id'] = $id;
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;
$limit = 5;
$data['start'] = $start = ($page-1)*$limit;
$data['item'] = lazy_get_line( "SELECT * FROM `app_content_{$mid}` WHERE `id` = '".intval( $id )."'" );
if( !$data['item'] )
{
	info_page('没有此'.app_config('model_name') );
}
if( !is_admin() && !$data['item']['is_active'] )
{
	info_page('此'.app_config('model_name').'正在审核中.' );
}
$data['pic_style'] = NULL;
if( app_config('focus_pic') )
{
	$width = intval(app_config('pic_width'))?'width:'.intval(app_config('pic_width')).'px;':'90px';
	$height = intval(app_config('pic_height'))?'height:'.intval(app_config('pic_height')).'px;':'120px';
	$data['pic_style'] = ' style="'.$width.$height.'" ';
}
([foreach from=$field item=item ])
([if $item.type == 'checkbox' ])
$data['item']['field_([$item.id])'] = $data['item']['field_([$item.id])']?join( ' ',unserialize($data['item']['field_([$item.id])']) ):NULL;
([elseif $item.type == 'multi-pic' ])
$img1 = img1($data['item']['field_([$item.id])']) ?img1($data['item']['field_([$item.id])']):'/static/images/no_image.gif';
$img2 = img2( $data['item']['field_([$item.id])'] );
$style =([if $item.custom_css ])' style= "([$item.custom_css])" '([else])$data['pic_style']([/if]);
$data['field_([$item.id])_html'] = '<a href="'.$img1.'" target="_blank"><img src="'.$img1.'" '.$style.' /></a><br/>';
if( $img2 )
{
	foreach( $img2 as $v )
	{
		if( trim( $v ) )
			$data['field_([$item.id])_html'] .= '<a href="'.trim($v).'" target="_blank"><img src="'.$v.'" width= "50px" style="float:left"/></a>';
	}
	$data['field_([$item.id])_html'] .= '<br clear="all" />';
}
([/if])
([/foreach])
$data['state_html'] = get_state_html_by_id($id);
$data['state_button'] = get_state_button($id);
$data['starts'] = $starts = get_starts_by_id( $id , $mid );
$data['start_all'] = $starts['1'] + $starts['2'] + $starts['3'] + $starts['4'] + $starts['5'] ;
$avg = ( $starts['1'] + $starts['2']*2 + $starts['3']*3 + $starts['4']*4 + $starts['5']*5 )/$data['start_all'] ;
$data['avg'] =  sprintf("%.2f", $avg);
$data['avg_start'] = round( $avg );
foreach( $data['starts'] as $k => $v )
{
	$data['starts_per'][$k] = floor( 50* $v /$data['start_all'] );
}
$data['coms'] = lazy_get_data( "SELECT sql_calc_found_rows * FROM `u2_comment` WHERE `tid` = '".intval($id)."' ORDER BY `id` DESC LIMIT $start , $limit" );
$all = get_count();
lazy_run_sql( "UPDATE `app_content_{$mid}` SET `hit` = `hit`+1 WHERE `id` = '".intval($id)."'" );
$base = '/app/native/'.$GLOBALS['app'].'/display/'.$id;
$page_all = ceil( $all /$limit);
$data['pager'] = get_pager( $page , $page_all , $base );


$names = array();
$uids = get_view_uids( $mid , $data['item']['id'] );
$data['view_people'] = $uids;
if( $data['coms'] )
{
	foreach( $data['coms'] as $v )
	{
		$uids[$v['uid']] = $v['uid'];
		$ids[] = $v['id'];
	}
}
if( $uids )
{
	$names = get_user_names( $uids );
}
$data['names'] = $names;
layout( $data , 'default' , 'app' );
?>