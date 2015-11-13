<?php
function get_system_usersearch_data($para = NULL)
{

	$para = unserialize($para);
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) :'搜索会员';

	return $data;
}
?>