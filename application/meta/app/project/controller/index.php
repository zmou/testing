<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$data['ci_top_title'] = app_config('model_name');
if( !is_login() )
{
	info_page('请登录后查看');
}

$data['id'] = '1';
$data['bind'] = $bind;
//print_r($field_lables);

$view_type = intval(array_shift($args));

foreach( $bind as $k => $v )
{
	$selected[] = " `$v` as $k ";
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

$data['index_title'] = array( '1'=>'最新发布的','2'=>'最多点击的','3'=>'最多评论的' );

$index_order = ( $view_type > 0 && $view_type < 4 ) ? $view_type  :1;

switch( $index_order ) 
{
	case '1';
	$order = "ORDER BY `id` DESC";
	break;

	case '2';
	$order = "ORDER BY `hit` DESC";
	break;

	case '3';
	$order = "ORDER BY `comnum` DESC";
	break;
}

$data['index_order'] = $index_order;

$data['list_top'] = lazy_get_data("SELECT `id` , ".join(',',$selected)." FROM `app_content_{$mid}` WHERE `is_active` = '1' $order ");

$data['coms'] = lazy_get_data( "SELECT * FROM `u2_comment` where `mid` = '".intval($mid)."'  ORDER BY `id` DESC LIMIT 5" );
$names = array();
if( $data['coms'] )
{
	foreach( $data['coms'] as $v )
	{
		$uids[$v['uid']] = $v['uid'];
		$cids[$v['tid']] = $v['tid'];
	}
	$data['contents'] = lazy_get_data("SELECT `id` , `{$bind['title']}` as title , `{$bind['pic']}` as pic FROM `app_content_{$mid}` WHERE `is_active` = '1' " , 'id' );
	$names = get_user_names( $uids );
	
}
$data['names'] = $names;

layout( $data , 'default' , 'app' );
?>