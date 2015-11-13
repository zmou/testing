<?php
function get_system_textarea_data($para = NULL)
{
	 $para = unserialize($para);

	 $data['title'] = (isset($para['title']) && strip_tags($para['title'])) ?strip_tags($para['title']):'Notice';

	 $data['desp'] = (isset($para['desp']) && $para['desp']) ?$para['desp']:'NO Infomation...';

	 return $data;
}
?>