<?php

$tab_array = array
(
	'0' => '所有主题',	
);
$limit = '10';
function iforum_get_names( $array )
{
	if( $array && is_array($array) )
	{
		$names_info = lazy_get_data("select `id`,`u2_nickname` from `u2_user` where `id` IN(".join(',',$array).") ");
		if($names_info)
		{
			foreach($names_info as $v)
			{
				$names_info[$v['id']] = $v['u2_nickname'];
			}
			return $names_info;
		}
	}
}
function show_floor( $key )
{
	if( $key == 0 )
	{
		return '楼主';
	}
	else
	{
		return $key . '楼';
	}
}
function iforum_get_cates( $fid )
{
	$cates = array();
	$list = lazy_get_data("select * from app_iforum_cate where fid = '$fid' order by `key` ");
	if($list)
	{
		foreach($list as $v )
		{
			$cates[$v['key']] = $v;
		}
	}
	return $cates;
}
function iforum_save_cates($fid,$key,$desp)
{
	if($desp)
	{
		$sql = "REPLACE INTO `app_iforum_cate` (`fid`,`key`,`desp`)VALUES('".intval($fid)."','".intval($key)."',".s($desp).")";
		lazy_run_sql($sql);
	}
	else
	{
		$sql = "UPDATE `app_iforum_posts` SET `type` = '0' WHERE `fid` = '".intval($fid)."' AND  `type` = '".intval($key)."'";
		lazy_run_sql($sql);
		$sql = "DELETE FROM `app_iforum_cate` WHERE `fid` = '".intval($fid)."' AND  `key` = '".intval($key)."' ";
		lazy_run_sql($sql);
	}

}
?>