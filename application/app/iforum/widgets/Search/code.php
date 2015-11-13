<?php
function get_iforum_search_data($para = NULL)
{
	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) :'搜索论坛';
	return $data;
}
?>