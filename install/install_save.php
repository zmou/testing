<?php
define( 'ROOT' , dirname( __FILE__ ).'/../' );
if( file_exists( ROOT.'install.lock' ) )
{
	sys_info('EasySNS已安装,如需重新安装请登录FTP删除根目录下的install.lock文件');
}
if( !$_REQUEST['admin_email'] || !$_REQUEST['admin_password'] || !$_REQUEST['admin_username'] )
{
	sys_info('请正确填写管理账号');
}
if( !is_writable( ROOT ) )
	{
		sys_info( '根目录不可写' );
	}
set_time_limit( 0 );
?><html>

<head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>EasySNS安装页面</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" type="text/css" href="/static/css/reset.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/base.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/fonts.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/grids.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/styles.css"> 
</head>

<body style="padding:40px;">
<div style="text-align:left;line-height:150%;background:url(/static/images/bg_logo.gif) right top no-repeat">
	<table border="0" width="98%" id="table1" cellspacing="0" cellpadding="0" height="40" align="center">
		<tr>
			<td valign="top"><br/><img src="/static/images/logo.gif" / >
			<h2 class="green" style="padding-left:36px">快速安装页面</h2>
			</td>
			<td rowspan="2">
			<p align="right">
			<a href="http://EasySNS.cn/" target="_blank">→EasySNS官方网站</a><br/>
					</td>
		</tr>
		</table>
		<table border="0" width="98%" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td valign="top">
			</td>
			<td align="right" valign="top">
			</td>
		</tr>
	</table>

<div style="padding-left:60px">
<?php
/*
Array
(
    [i] => install_save
    [db_host] => localhost
    [db_user] => root
    [db_password] => 
    [db_name] => webmagik
    [admin_username] => admin
    [admin_email] => admin@admin.com
    [admin_password] => admin
    [cdb_cookietime] => 0
    [cdb_cpcollapsed] => 0
    [cdb_sid] => HJ1uYR
    [PHPSESSID] => 157b23d7a17deb2b759af9f784b7802f
)
*/

// 1 创建数据配置文档
$config['hostname'] = trim($_REQUEST['db_host']);
$config['username'] = trim($_REQUEST['db_user']);
$config['password'] = trim($_REQUEST['db_password']);
$config['database'] = trim($_REQUEST['db_name']);

// 2 初始化数据库和数据表
if(!($connect=@mysql_connect( $config['hostname'] , $config['username'] , $config['password'] ) ))
{
	echo "数据库联接失败,请确认您使用的数据库帐号是否正确,<a href=\"install.php\">点击这里返回</a>";
	exit;
}
// 数据正确,确认安装 


if(mysql_get_server_info( $connect ) > '4.0.1') 
{
    mysql_query( "set names 'utf8'" , $connect );
	
	if( $_REQUEST['force_charset'] == '1' )
	{
		$sql = "ALTER DATABASE `{$config['database']}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		@mysql_query(trim($sql),$connect);
	}
	
	// echo mysql_error();
}



if(mysql_get_server_info( $connect ) >= '5.0.18') 
{
	 mysql_query("SET sql_mode=''" , $connect );
	 //echo 'Mysql 版本偏高，兼容性代码已经启用，但可能存在问题。如果strict mode开启的话，建议您手工关闭它<br/><br/>';

}


$unicode_version = ( mysql_get_server_info( $connect ) > '4.1.12' ? true : false );


if( $unicode_version )
{
	$sql = "CREATE DATABASE IF NOT EXISTS `{$config['database']}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
}
else
{
	$sql = "CREATE DATABASE IF NOT EXISTS `{$config['database']}` ";
}
mysql_query(trim($sql),$connect);

if(!$select=mysql_select_db( $config['database'] , $connect ))
{
	echo "无法选择数据库,或数据库不存在";
	exit;
}

$files = preg_replace( "/(#.+[\r|\n]*)/" , '' , file_get_contents( ROOT . 'install/core.sql' ) );
$sqls = split_sql_file( $files );

foreach( $sqls as $sql )
{
	mysql_query(trim($sql),$connect);
}

if( mysql_errno() == 0 )
{
	//echo '安装成功！';
	//rename( './install.php' , './installed-'.md5(rand(1,100).time()).'.php' );

}
else
{
	echo mysql_error();
}
save_db( $config );
// 4 初始化管理账号
$sql = "INSERT INTO `u2_user` (  `u2_email` , `u2_password` , `u2_joindate` , `u2_nickname` , `u2_isactive` , `u2_level`  ) 
VALUES (  '" . _s( $_REQUEST['admin_email'] ) . "', '" .  _s( MD5($_REQUEST['admin_password']) ) . "', NOW( ) , '" . _s( $_REQUEST['admin_username'] ) . "', '1', '9');";

mysql_query( $sql ,$connect );

file_put_contents( ROOT.'install.lock' , 'locked' );
echo mysql_error();

echo '安装完成，<a href="/">点击这里进入网站首页</a>';

function _s( $str )
{
	return mysql_real_escape_string( $str );
}
function save_db( $data )
{
	if( !is_writable( ROOT.'application/config/database.php') )
	{
		sys_info( '文件 application/config/database.php 不可写或者不存在' );
	}
	$contents = file_get_contents( ROOT.'application/config/database.php' );
	$reg ='/\$db\[\'default\'\]\[\'(.+?)\'\].+?;/is';
	//$reg ='/class=\'imgview\'>.+?<img src=".+?" tppabs="(.+?)"/is';
	preg_match_all( $reg ,$contents , $out  );
	if( $out[1] )
	{
		$old = $new = array();
		foreach( $out[1] as $k => $v )
		{
			if( isset( $data[strtolower($v)] ) )
			{
				$old[] = $out[0][$k];
				$new[] = '$db[\'default\'][\''.strtolower($v).'\'] = "'.addcslashes( $data[strtolower($v)] , '"' ).'";';
			}
		}
		if( $new )
		{
			$contents = str_replace( $old , $new , $contents );
			file_put_contents( ROOT.'application/config/database.php' , $contents );
		}
	}
}
function split_sql_file($sql, $delimiter = ';') 
{
	$sql               = trim($sql);
	$char              = '';
	$last_char         = '';
	$ret               = array();
	$string_start      = '';
	$in_string         = FALSE;
	$escaped_backslash = FALSE;

	for ($i = 0; $i < strlen($sql); ++$i) {
		$char = $sql[$i];

		// if delimiter found, add the parsed part to the returned array
		if ($char == $delimiter && !$in_string) {
			$ret[]     = substr($sql, 0, $i);
			$sql       = substr($sql, $i + 1);
			$i         = 0;
			$last_char = '';
		}

		if ($in_string) {
			// We are in a string, first check for escaped backslashes
			if ($char == '\\') {
				if ($last_char != '\\') {
					$escaped_backslash = FALSE;
				} else {
					$escaped_backslash = !$escaped_backslash;
				}
			}
			// then check for not escaped end of strings except for
			// backquotes than cannot be escaped
			if (($char == $string_start)
				&& ($char == '`' || !(($last_char == '\\') && !$escaped_backslash))) {
				$in_string    = FALSE;
				$string_start = '';
			}
		} else {
			// we are not in a string, check for start of strings
			if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string    = TRUE;
				$string_start = $char;
			}
		}
		$last_char = $char;
	} // end for

	// add any rest to the returned array
	if (!empty($sql)) {
		$ret[] = $sql;
	}
	return $ret;
}
function sys_info( $info )
{
	$data['info'] = $info;
	$data['title'] = $data['top_title'] = '系统消息';
	@extract( $data );
	require( ROOT. 'install/sys.tpl.html' );
	exit;
}
?>
</div>
</div>
</body>
</html>