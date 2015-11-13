<?php

include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}
$data['ci_top_title'] = '搜索'.app_config('model_name');
$page = array_shift( $args );
$desp = array_shift( $args );
$desp = urldecode( $desp );
$page = intval($page) < 1 ?1:intval($page);
$data['page'] = $page;
$limit = 10;
$data['start'] = $start = ($page-1)*$limit;
$desps = z(trim( v( 'desp' )) );
$data['desp'] = $desp = empty( $desps )? $desp : $desps ;

foreach( $bind as $k => $v )
{
	$selected[] = " `$v` as $k ";
}
$search_keys = array();
([foreach from=$field item=item ])
([if $item.is_searchable ])
$search_keys[] = 'field_([$item.id])';
([/if])
([/foreach])
if( !$search_keys && $desp )
{
	info_page('错误: 没有可用的搜索字段');
}
$where = 1;
if( $desp )
{
	foreach( $search_keys as $v )
	{
		$orwheres[] = " `$v` LIKE '%".$desp."%' ";	
	}
	$where = join( ' OR ' , $orwheres );
}
$pic_width = app_config('pic_width');
$pic_height = app_config('pic_height');
$pic_style = NULL;
if( app_config('focus_pic') )
{
	$width = $pic_width?'width:'.$pic_width.'px;':NULL;
	$height = $pic_height?'height:'.$pic_height.'px;':NULL;
	$pic_style = ' style="'.$width.$height.'" ';
}
$data['pic_style'] = $pic_style;

$data['items'] = $items = lazy_get_data( "SELECT sql_calc_found_rows `id` , ".join(',',$selected)." FROM `app_content_{$mid}` WHERE $where LIMIT $start , $limit" );

$desp = urlencode( $desp );
$all = get_count();
$base = '/app/native/'.$GLOBALS['app'].'/search';
$page_all = ceil( $all /$limit);

$data['pager'] = get_pager( $page , $page_all , $base , $desp);
$data['bind'] = $bind;
layout( $data , 'default' , 'app' );
?>	