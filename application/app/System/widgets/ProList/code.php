<?php
function get_system_prolist_data($para = NULL)
{
	$para = unserialize($para);

	$page = 1;

	$cid = _Page('cid');

	$CI =&get_instance();

	$CI->load->model('Pro_model', 'pro', TRUE);
	
	$page = intval($page) < 1 ?1:intval($page);
	
	$limit = (isset($para['limit']) && intval( $para['limit'] ) > 0 ) ?intval( $para['limit'] ):c('per_page');

	$start = ($page-1)*$limit;

	$data['pros'] = $CI->pro->plist( intval( $cid ) ,$start,$limit  );

	$data['full'] = (isset($para['full']) && intval( $para['full'] ) > 0 ) ?intval( $para['full'] ):0;

	return $data;
}
?>