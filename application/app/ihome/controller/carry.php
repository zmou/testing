<?php
if( !is_login() )
{
	die('请登陆后操作!');
}
include_once( dirname( __FILE__ ) . '/function.php'   );
$id = array_shift( $args );
$uid = format_uid();
$js_data = array();
$use = '装备';
$count = lazy_get_var("select `count` from `global_user_items` where `uid` = '$uid' and `iid` = '$id' limit 1");
if( $count < 1 )
{
	die('你没有此物品');
}
$item = lazy_get_line("select * FROM `global_items` WHERE `id` = '$id' limit 1 ");
if( !$item )
{
	die('没有找道具资料');
}
//装备检查
$weared = lazy_get_var("select count(*) from `global_items_carry` where `iid`='$id' and `uid` = '$uid' ");
if($weared)
{
	die('你已经携带了此道具');
}
$weared_count = lazy_get_var("select count(*) from `global_items_carry` where `uid` = '$uid' ");
if( $weared_count >= 5 )
{
	die('你已经不能再携带东西了');
}
if( $count == '1' )
{
	lazy_run_sql( "DELETE FROM `global_user_items`  WHERE `uid` ='$uid' and `iid` = '$id' limit 1 " );
	$js_data[] = '$("item_'.$id.'").remove()';
	$js_data[] = '$("baggage_count").value =parseInt($("baggage_count").value) - 1 ';
}
else
{
	lazy_run_sql( "UPDATE `global_user_items` SET `count` = `count` - 1  WHERE `uid` = '$uid' and `iid` = '$id' limit 1 " );
	$js_data[] = '$("item_count_value_'.$id.'").value = parseInt($("item_count_value_'.$id.'").value ) - 1 ';
	$js_data[] = '$("item_count_'.$id.'").innerHTML =$("item_count_value_'.$id.'").value';
}
lazy_run_sql( "INSERT INTO `global_items_carry` (`uid` , `iid` ,`taked` )VALUES('$uid','{$item['id']}' , '0' )  " );
$js_data[] = 'ihome_carry_item("'.$id.'","'.$item['pic'].'" ,"'.$item['desp'].'")';
//$js_data[] = '$("wear_'.$item['type'].'").setHTML("<a href=\"JavaScript:irpg_unset_item('.$item['id'].')\"><img src=\''.$item['pic'].'\' alt=\"'.$item['desp'].'\" /></a>")';


$display = '您携带了'.$item['name'];
echo $display;
if($js_data)
{
	$js_code = '<script>'.join(';',$js_data).';</script>';
	echo $js_code;
}
			
			
?>