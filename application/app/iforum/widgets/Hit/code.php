<?php
//include( dirname( __FILE__ ).'../../../controller/function.php');
function get_iforum_hit_data($para = NULL)
{
	$uid = format_uid();
	$para = unserialize($para);
	
	$limit = isset($para['limit']) && intval($para['limit']) > 0 ? intval($para['limit']) :5;
	$type = isset($para['type']) && intval($para['type']) != '' ? intval($para['type']) : 1 ; 
	$data['new_page'] = isset($para['new_page']) && intval($para['new_page']) > 0 ? intval($para['new_page']) : 0 ;
	$days = isset($para['days']) && intval($para['days']) > 0 ? intval($para['days']) : 0 ;
	
	if( $days == 0 || $days == '' )
	{
		$days = NULL;
	}
	else
	{
		$day = date( 'Y-m-d H:i:s' , strtotime( ' - '.$days.' days ' ) );
		$days = " AND `time` > '$day' ";
	}

	if( isset($para['title']) && strip_tags($para['title']) != '' )
	{
		$data['title'] = strip_tags($para['title']);
	}
	else
	{
		if( $type == 2 )
		{
			$data['title'] = '回复最多的';
		}
		else
		{
			$data['title'] = '点击最多的';
		}
	}
	
	if( $type == 2 )
	{
		$at = "ORDER BY `app_iforum_posts`.`reply` DESC";
	}
	else
	{
		$at = "ORDER BY `app_iforum_posts`.`hit` DESC";
	}
	$list = lazy_get_data( "SELECT * FROM `app_iforum_posts` WHERE `parent_id` = '0' AND `is_active` = '1' $days $at LIMIT $limit" );
	
	if($list)
	{
		foreach($list as $v)
		{
			$uids[$v['uid']] =  $v['uid'];
			$uids[$v['last_uid']] =  $v['last_uid'];
		}
		$data['names'] = get_name_by_uids($uids);
	}
	$data['list'] =$list;
	
	return $data;
}
?>