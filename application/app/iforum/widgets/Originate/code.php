<?php
//include(ROOT.'/application/app/iforum/controller/function.php');
function get_iforum_originate_data($para = NULL)
{
	$uid = format_uid();
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) :'我发起的';
	$data['num'] = $num = isset($para['num']) && intval($para['num']) > 0 ? intval($para['num']) :5;
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

	$list = lazy_get_data( "SELECT * FROM `app_iforum_posts`  WHERE `parent_id` = '0' AND `is_active` = '1' AND `uid` = '".intval($uid)."' $days ORDER BY `time` DESC LIMIT 0 , $num" );

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