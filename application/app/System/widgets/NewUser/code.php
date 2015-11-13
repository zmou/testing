<?php
function get_system_newuser_data($para = NULL)
{
	$para = unserialize($para);
	$limit = (isset($para['limit']) && intval( $para['limit'] ) > 0 ) ?intval( $para['limit'] ):9;
	$cpl = (isset($para['cpl']) && intval( $para['cpl'] ) > 0 ) ?intval( $para['cpl'] ):3;
	$data['title'] = (isset($para['title']) && strip_tags($para['title']) )!= '' ? strip_tags($para['title']):'New Members';
	$CI =&get_instance();

	$CI->db->select('*')->from('u2_user')->orderby('id','desc')->limit($limit);
	$data['members'] = array_chunk( lazy_get_data() , $cpl );
	return $data;
}
?>