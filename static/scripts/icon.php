<?php
define('BASEPATH',  dirname( __FILE__ ).'/../..' );
function myhashstr( $str )
{
	return $str{0} . $str{1} . '/' . $str{2} . $str{3} . '/' ;
}
function MakeDir($path)
{
	if (!file_exists($path))
	{
	   MakeDir(dirname($path));
	   @mkdir($path, 0777);
	}
}
$size = intval($_REQUEST['size']) > 0 ?intval($_REQUEST['size']) : 80 ;

$url = $_REQUEST['url'];

$md5 = md5($url);

$web_path = "/static/data/hash/pic_icon/" . myhashstr( $md5 ) ;

$path = BASEPATH . $web_path;

$web_file = $web_path . $md5 . $size .  '.gif';

$file = BASEPATH . $web_file;

$source_file =  BASEPATH  .  $url;

if( !file_exists($file)  )
{
	@MakeDir( $path );

	if( file_exists( $source_file ) )
	{
		include_once( BASEPATH.'/application/libraries/Icon.php' );
		$icon = new icon();
		$icon->path = $source_file;
		$icon->size = $size;
		$icon->dest = $file;
		$icon->createIcon();
	}
}

header('Location: '.$web_file );
?>