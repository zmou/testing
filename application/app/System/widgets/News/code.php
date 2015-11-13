<?php
function get_system_news_data($para = NULL)
{
	$para = unserialize($para);
	
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '新鲜事' ;
	$days =  isset($para['days']) && intval($para['days']) > 0 ? intval($para['days']) : 0 ;
	$limit =  isset($para['limit']) && intval($para['limit']) > 0 ? intval($para['limit']) : 10 ;
	$where = NULL;
	if( $days > 0 )
	{
		$where = " where `u2_time` > '".date("Y-m-d" , strtotime( " - $days days" ) )."' ";
	}
	$data['items'] = lazy_get_data("select * from `u2_mini_feed` $where order by `id` desc limit $limit ");
	
	return $data;
}

?>