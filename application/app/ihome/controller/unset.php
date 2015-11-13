<?php
if( !is_login() )
{
	die('请登录后查看');
}
$id = array_shift( $args );
$uid = format_uid();
$sql = "DELETE FROM `global_items_carry` WHERE `uid` = '$uid' AND `iid` = '" . $id . "' LIMIT 1";
lazy_run_sql( $sql );
if( mysql_affected_rows() != 1 )
{
	die('您没有装备此物品');
}
$item = lazy_get_line("select * FROM `global_items` WHERE `id` = '$id' limit 1 ");
if( !$item )
{
	die('没有找道具资料');
}
$count = lazy_get_var("select count(*) from `global_user_items` where `uid` = '$uid' and `iid` = '$id' ");
if( $count )
{
	$sql = "update `global_user_items` set `count` = `count` + '1'  WHERE `iid` = '$id' AND `uid` = '$uid' limit 1";
	lazy_run_sql($sql);
}
else
{
	$sql = "insert into `global_user_items`(`uid`,`iid`,`count`)values('$uid','$id','1')";
	lazy_run_sql($sql);
}



echo '您卸载了'.$item['name'];
$js_data[] = 'ihome_add_item("'.$id.'","'.$item['pic'].'" ,"'.$item['desp'].'")';

echo '<script>'.join(';',$js_data).';</script>';

?>

