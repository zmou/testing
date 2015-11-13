<html>

<head>
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" href="/static/favicon.ico" >
<title>EasySNS环境检测页面</title>
<link rel="stylesheet" type="text/css" href="/static/css/reset.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/base.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/fonts.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/grids.css"> 
<link rel="stylesheet" type="text/css" href="/static/css/styles.css"> 
</head>

<body style="padding:40px;">
<div style="text-align:left;line-height:150%;background:url(/static/images/bg_logo.gif) right top no-repeat">
<img src="/static/images/logo.gif" / >
<div style="padding-left:36px">
<h2 class="green">环境检测页面</h2>

<?php echo '<!-- '; ?>您的空间不支持PHP，请更换支持PHP的空间.<?php echo '-->'; ?>
<!-- 
<?php echo '-' . '->'; ?>
<h2>检查是否支持PHP5</h2>
<p class="green">本空间支持PHP！ 版本为<?php echo phpversion() ;  ?></p>

<?php if( reset( explode( '.' , phpversion() ) ) < 5 ): ?>
<span class="install_red">您的PHP版本过低,无法安装EasySNS.PHP4已经在2008年8月停止维护,我们强烈建议您使用PHP5</span>
<ul>
	<li><a href="http://techblog.moneysns.com/?p=13" target="_blank">查看官方推荐的环境,空间和主机</a></li>
	<li><a href="http://techblog.moneysns.com/?p=9" target="_blank">了解如何安装PHP5</a></li>
	<li><a href="http://techblog.moneysns.com/?p=11" target="_blank">了解如何升级PHP4到PHP5</a></li>
</ul>
<?php die(); ?>

<?php endif; ?>

	
<h2>检查用到的PHP扩展模块</h2>
<?php $ms = get_loaded_extensions(); ?>
<?php if( !in_array( 'gd' , $ms ) ): ?>
	<span class="install_red">PHP图像处理扩展-GD扩展未安装</span>
	<ul>
		<li><a href="http://techblog.moneysns.com/?p=21" target="_blank">了解如何安装PHP扩展模块</a></li>
		<li><a href="http://techblog.moneysns.com/?p=13" target="_blank">查看官方推荐的环境,空间和主机</a></li>
	</ul>
	<?php die(); ?>
<?php else: ?>
<p class="green">GD扩展已安装</p>
<?php endif; ?>

<?php if( !in_array( 'mysql' , $ms ) ): ?>
	<span class="install_red">PHP数据库扩展-Mysql扩展未安装</span>
	<ul>
		<li><a href="http://techblog.moneysns.com/?p=21" target="_blank">了解如何安装PHP扩展模块</a></li>
		<li><a href="http://techblog.moneysns.com/?p=13" target="_blank">查看官方推荐的环境,空间和主机</a></li>
	</ul>
	<?php die(); ?>
<?php else: ?>
<p class="green">Mysql扩展已安装</p>
<?php endif; ?>

<?php if( !in_array( 'mbstring' , $ms ) ): ?>
	<span class="install_red">PHP多字节文本处理扩展-Mbstring扩展未安装</span>
	<ul>
		<li><a href="http://techblog.moneysns.com/?p=21" target="_blank">了解如何安装PHP扩展模块</a></li>
		<li><a href="http://techblog.moneysns.com/?p=13" target="_blank">查看官方推荐的环境,空间和主机</a></li>
	</ul>
	<?php die(); ?>
<?php else: ?>
<p class="green">Mbstring扩展已安装</p>
<?php endif; ?>

<h2>检查安装目录是否为根目录</h2>
<?php if( strpos( $_SERVER['REQUEST_URI'] , '/install/' ) !== 0  ): ?>
	<span class="install_red">EasySNS只能安装在域名根目录下,请调整目录位置</span>
	<ul>
		<li><a href="http://techblog.moneysns.com/?p=34" target="_blank">了解如何将EasySNS上传到正确的目录下</a></li>
	</ul>
	<?php die(); ?>
<?php else: ?>
<p class="green">安装目录位置正确</p>
<?php endif; ?>



<h2>检查目录可写性</h2>
<?php 

$list = file( ROOT . 'install/dir_list.txt' );
if( is_array( $list ) )
{
	foreach( $list as $item )
	{
		if( is_writable( ROOT.trim( $item ) ) ): ?>
		<p class="green">目录<?=trim( $item )?>可写</p>
		<?php elseif( @mkdir( ROOT.trim( $item ) , 0777 , true ) ): ?>
		<p class="green">目录<?=trim( $item )?>不存在,已自动创建</p>
		<?php else: ?>
		<span class="install_red">目录<?=trim( $item )?>不可写</span>
		<ul>
			<li><a href="http://techblog.moneysns.com/?p=31" target="_blank">了解如何通过FTP修改目录权限</a></li>
			<li><a href="http://techblog.moneysns.com/?p=13" target="_blank">查看官方推荐的环境,空间和主机</a></li>
		</ul>
		<?php die(); ?>
		<?php endif;
	
	
	}
}

?>

<h2>您还需要知道的</h2>

<p>您的空间服务商应该已经向您提供了数据库信息，包括<span class="green">数据库服务器地址、数据库用户名、数据库密码和数据库名称</span>。</p>
<p>EasySNS在安装时，通常不会对已有数据产生任何影响，但是我们仍然<span class="red">建议您在安装前备份数据库并将EasySNS安装到一个独立的数据库上</span>。</p>

<p><h2><input type="button" value="下一步" onclick="location='install.php'" class="button" /></h2></p>
<br/><br/><br/>
<?php echo '<!-' . '-'; ?>
-->
</div>
</div>
</body>
</form>
</html>