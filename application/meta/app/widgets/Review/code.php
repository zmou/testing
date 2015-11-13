<?php
include_once( dirname(__FILE__).'../../../controller/function.php' );
function get_([$folder])_review_data($para = NULL)
{
	//print_r( $GLOBALS['widget_id']);
	$para = unserialize($para);
	$mid = app_config('mid');
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '评论' ;
	$data['show_num'] = isset($para['show_num']) && intval($para['show_num']) != '0' ? intval($para['show_num']) : '10' ;
	$data['show_type'] = isset($para['show_type']) ? intval($para['show_type']) : '1' ;
	
	switch( $data['show_type'] )
	{
		case '1';
		$where = "ORDER BY `time` DESC";
		break;

		case '2';
		$where = "ORDER BY `rcount` DESC";
		break;
		
		case '3';
		$where = "ORDER BY `dig` DESC";
		break;
	}
	
	$data['list'] = lazy_get_data( "SELECT * FROM `u2_comment` where `mid` = '".intval($mid)."' $where  LIMIT ".intval($data['show_num'])."" );
	$names = array();
	if( $data['list'] )
	{
		foreach( $data['list'] as $v )
		{
			$uids[$v['uid']] = $v['uid'];
			$ids[] = $v['id'];
		}
		$names = get_user_names( $uids );
		
	}
	$data['names'] = $names;
	return $data;
}

?>