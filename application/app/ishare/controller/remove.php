<?php
include_once( dirname( __FILE__ ) . '/function.php'   );

$fid = intval(array_shift($args));

if( $fid < 1 )
{
	die('wrong fid');
}
else
{
	$sql = "DELETE FROM `app_fav` WHERE `uid` = '" . format_uid() . "' AND `id` = '" . $fid . "' LIMIT 1";
	lazy_run_sql( $sql );
	if( mysql_affected_rows() == 1 )
	{
		$sql = "DELETE FROM u2_restore WHERE u2_app_name = 'ishare' and u2_app_id = '$fid' ";
		lazy_run_sql( $sql );
	}
}


?>