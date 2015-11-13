<?php
function get_system_plugs_data($para = NULL)
{
	$para = unserialize($para);
	
	$page = 1;
	if( isset( $para['args'] ) )
	{
		$page = intval(array_shift( $para['args'] ));
	}
	$page = $page < 1 ? 1 : $page ;
	$data['title'] = isset($para['title']) && strip_tags($para['title']) != '' ? strip_tags($para['title']) : '组件' ;
	$limit = isset($para['limit']) && intval($para['limit']) > 0 ? intval($para['limit']) : 5 ;
	$start = ($page-1)*$limit;
	$data['lines'] = 4;
	$apps = lazy_get_data("SELECT sql_calc_found_rows * FROM `u2_plugs` WHERE `is_active` = 1 ORDER BY `has_widget` DESC LIMIT $start,$limit ");
	$all = get_count();
	
	$wid = intval($GLOBALS['widget_id']);
	$page_all = ceil( $all /$limit);
	$data['pager'] = get_widget_pager( $wid , $page , $page_all );
	
	if( $apps )
	{
		foreach( $apps as $k => $v )
		{
			$aids[$v['aid']] = $v['aid'];
			$uids[$v['uid']] = $v['uid'];
		}
		if( $uids )
		{
			$data['names'] = get_name_by_uids( $uids );
		}
		$data['aids'] = $aids;
		$data['apps'] = $apps;
	}

	$wids = lazy_get_data("SELECT * FROM `u2_plugs_widget`");
	if( $wids )
	{
		$data['wids'] = $wids;
	}
	$domain = _sess('domain');
	if( $domain != '' )
	{
		$data['domain'] = $domain;
	}
	if( is_login() )
	{
		$data['is_login'] = true;
	}
	return $data;
}
?>