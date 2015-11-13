<?php
//include( dirname( __FILE__ ).'../../../controller/function.php');
function get_iforum_newrestore_data($para = NULL)
{
	$uid = format_uid();
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) :'最新回复的';
	$limit = isset($para['limit']) && intval($para['limit']) > 0 ? intval($para['limit']) :5;
	$data['new_page'] = isset($para['new_page']) && intval($para['new_page']) > 0 ? intval($para['new_page']) : 0 ;
	$days = isset($para['days']) && intval($para['days']) > 0 ? intval($para['days']) : 0 ;
	if( $days == 0 || $days == '' )
	{
		$days = NULL;
	}
	else
	{
		$day = date( 'Y-m-d H:i:s' , strtotime( ' - '.$days.' days ' ) );
		$days = " AND `last_post_time` > '$day' ";
	}

	
	$list = lazy_get_data("SELECT * FROM `app_iforum_posts` WHERE `parent_id` = 0 AND `is_active` = '1' $days ORDER BY `last_post_time` DESC LIMIT $limit");
	if( $list )
	{
		foreach($list as $v)
		{
			$uids[$v['uid']] =  $v['uid'];
			$uids[$v['last_uid']] =  $v['last_uid'];
		}
		$data['names'] = get_name_by_uids($uids);
	}

	//print_r( $data['names'] );

	$data['list'] =$list;
	
	return $data;
}
?>