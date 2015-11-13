<?php
function get_system_vote_data($para = NULL)
{
	//print_r( $GLOBALS['widget_id']);
	$para = unserialize($para);
	
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '投票' ;
	$data['types'] = isset($para['dan']) ? intval($para['dan']) : '1' ;
	$data['vote_name'] = isset($para['vote_name']) && strip_tags(trim($para['vote_name'])) != '' ? strip_tags($para['vote_name']) : '投票一' ;
	$data['vote'] = isset($para['vote']) && $para['vote'] != '' ? $para['vote'] : '' ;
	$data['wid'] = $GLOBALS['widget_id'];
	
	return $data;
}

?>