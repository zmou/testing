<?
include_once( dirname( __FILE__ ) . '/function.php'   );

if( !is_login() )
{
	info_page('请登录后查看');
}

$data = array();
$data['ci_top_title'] = '宠物银行';

$tab_type = 'petbank';


$data['tab_type'] = $tab_type;
$data['tab_array'] = $tab_array;

$data['duoduo'] =  lazy_get_line("SELECT * FROM `app_iduoduo_duoduo` WHERE `uid` = '" . format_uid() . "' LIMIT 1 ");

if( !$data['duoduo'] )
{
	header('Location: /app/native/iduoduo/add/');
	die();
}
$data['user'] = lazy_get_line( "SELECT * FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1" );

if( !$data['user'] )
{
	$sql = "insert into app_ihome_user ( uid,g,gold,hp,hp_max  )values('".format_uid()."','0','0','0','0') ";
	lazy_run_sql( $sql );
}

layout( $data , 'default' , 'app' );
?>