<?php
include_once( dirname( __FILE__ ) . '/function.php'   );
$sql ="select `u2_folder` from `u2_app` where `aid` = 'ishopcart' limit 1 ";
$folder = lazy_get_var( $sql );
$id = intval(array_shift( $args ));
$html = NULL;
if( $folder && isset($bind['price']) )
{
	$html = '<INPUT TYPE="button" class="button" onclick="location=\'/app/native/'.$folder.'/insert/'.$GLOBALS['app'].'/'.$id.'\'" value=" 加入购物车 ">';
}
echo $html;

?>