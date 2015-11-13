<?php
include_once( dirname(__FILE__).'../../../controller/function.php' );

function get_([$folder])_list_data($para = NULL)
{     
	//print_r( $GLOBALS['widget_id']);
	$para = unserialize($para);
	$mid = app_config('mid');
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : app_config('model_name').'列表' ;
	$data['list_type'] = $list_type = intval( $para['list_type'] );
	switch( $list_type )
	{
		case '2':
		$where = "ORDER BY `hit` DESC";
		break;

		case '3':
		$where = "ORDER BY `comnum` DESC";
		break;

		default: 
		$where = "ORDER BY `id` DESC";
	}

	$list_num = ( isset($para['list_num']) && intval( $para['list_num'] ) > 0 ) ? intval( $para['list_num'] ) : 6;
	$data['show_type'] = $show_type = ( isset($para['show_type']) && intval( $para['show_type'] ) > 0 ) ? intval( $para['show_type'] ) : 1;
	
	if( isset( $show_type ) && $show_type == '1' )
	{
		$data['row_num'] = ( isset($para['row_num']) && intval( $para['row_num'] ) > 0 ) ? intval( $para['row_num'] ) : 6;
	}
	$bind = app_config('bind');
	
	foreach( $bind as $k => $v )
	{
		$selected[] = "`$v` as $k";
	}
	
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

	$data['coms'] = lazy_get_data("SELECT `id`,".join(',',$selected)." FROM `app_content_{$mid}` $where LIMIT ".intval( $list_num )." ");
	
	return $data;
}

?>