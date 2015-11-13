<?php

function get_system_activeuser_data($para = NULL)
{
	$uid = format_uid();
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '活跃会员' ;
	$days = isset($para['days']) && intval($para['days']) > 0 ? intval($para['days']) : 0 ;
	$data['linenum'] = isset($para['linenum']) && intval($para['linenum']) > 0 ? intval($para['linenum']) : 3 ;
	$data['aid'] = $aid = strip_tags($para['aid']) != '' ? strip_tags($para['aid']) : 'all' ;

	if( $aid == 'all' )
	{
		$where = "WHERE 1";
	}
	else
	{
		$where = "WHERE `u2_app_aid` = '".$aid."'";
	}

	if( $days == 0 || $days == '' )
	{
		$days = NULL;
	}
	$day = date( 'Y-m-d H:i:s', strtotime( '- '.$days.' days' ) );
	$days = "AND `u2_time` > '".$day."'" ;
	
	$feed = lazy_get_data("SELECT *,COUNT(`u2_action`) AS `num` FROM `u2_mini_feed` $where $days GROUP BY `u2_uid` ORDER BY `num` DESC");
	
	if( $feed )
	{
		foreach( $feed as $k => $v )
		{
			$uids[] = $v['u2_uid'];
		}
		//print_r( $uids );
		$data['users'] = lazy_get_data( "SELECT * FROM `u2_user` WHERE `id` IN (".join( ',' , $uids ).")" );
	}
	return $data;
}

?>