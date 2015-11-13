<?php

class upload extends Controller {

	function upload()
	{
		parent::Controller();
	}
	function index()
	{
		die('error');
	}
	function pic( $folder = NULL , $key = NULL )
	{
		check_login();
		if( !is_admin() )
		{
			$this->check_upload( $folder  , $key );
		}
		$field = $key;
		$type = 'pic';
		@include(  APPPATH.'views/layout/upload/upload.tpl.html' );
	}
	function pics( $folder = NULL , $key = NULL )
	{
		check_login();
		$this->check_upload( $folder  , $key );
		$field = $key;
		$type = 'multi-pic';
		@include(  APPPATH.'views/layout/upload/upload.tpl.html' );
	}
	function files( $folder = NULL , $key = NULL )
	{
		check_login();
		$this->check_upload( $folder  , $key );
		$field = $key;
		$type = 'file';
		@include(  APPPATH.'views/layout/upload/upload.tpl.html' );
	}
	function icon()
	{
		header("Content-type: text/html;charset=utf-8");
		if( !is_admin() )
		{
			die( '<script>parent.show_icon_res(\'您没有权限进行此操作\');</script>' );
		}
		$folder = v('name');

		$path = 'static/icon/'.$folder.'.gif';
		if( !file_exists(ROOT.$path ) )
		{
			die( '<script>parent.show_icon_res(\'错误 未找到原始图标文件.\');</script>' );
		}
		$file = $_FILES['icon_file'];
		if( !isset( $file['size'] ) ||  !($file['size'] > 0) )
		{
			die( '<script>parent.show_icon_res(\'错误的文件\');</script>' );
		}
		$file_info = getimagesize($file['tmp_name']);
		$ext = $file_info['mime'];
		$allow_list =  array('image/gif' , 'image/jpeg' ,'image/pjpeg' , 'image/png' );
		if( !in_array( $ext , $allow_list ) )
		{
			die( '<script>parent.show_icon_res(\'您上传的文件类型不正确，请重新选择文件\');</script>' );
		}
		if( !is_writable( ROOT.$path ) )
		{
			die( '<script>parent.show_icon_res(\'错误 目录'.$path.' 不可写\');</script>' );
		}
		$this->load->library('icon');
		$this->icon->path = $file['tmp_name'];
		$this->icon->size = 16;
		$this->icon->dest = ROOT.$path;
		$this->icon->createIcon();
		die( '<script>parent.show_icon_res(\'更新插件图片成功,请<a href="JavaScript:void(0)" onclick="window.location.reload();">刷新</a>页面查看\');</script>' );

	}
	private function check_upload( $folder  , $key )
	{
		if( !$folder )
		{
			die('error');
		}
		$fields = app_config('field_lable' , $folder);
		if( !$fields || !is_array( $fields ) )
		{
			info_page('错误的app');
		}
		if( !isset($fields[$key]) )
		{
			info_page('错误的字段');
		}
	}
	function save($folder = NULL )
	{
		check_login();
		$key = v('input_name');
		$type = v('type');
		if( !is_admin() )
		{
			$this->check_upload( $folder  , $key );
		}
		$file = $_FILES['u2_file'];
		if( $file['error'] == 2 || ( c('max_file_size') > 0 && $file['size'] > c('max_file_size') ) )
		{
			info_page( '您上传的文件超过系统允许的范围(' . intval(intval( c('max_file_size') ) / 1024) . 'k)，请重新选择文件' );
			exit;
		}
		if( !isset( $file['size'] ) ||  !($file['size'] > 0) )
		{
			info_page('错误的文件');
		}
		$end = $ext = end(explode('.' , strtolower( $file['name'] )));
		if( $type == 'pic'  || $type == 'multi-pic' )
		{
			$file_info = getimagesize($file['tmp_name']);
			$ext = $file_info['mime'];
			$allow_list =  array('image/gif' , 'image/jpeg' ,'image/pjpeg' ,'image/png' );
		}
		elseif( $type == 'file')
		{
			$allow_list = explode( '|' , c('web_site_upload_file_type') );
		}
		else
		{
			info_page( '错误的上传类型' );
		}
		if( !in_array( $ext , $allow_list ) )
		{
			info_page( '您上传的文件类型不被允许，请重新选择文件' );
			exit;
		}
		$time = date( "y/m/d H_i_s" );
		$paths = explode( ' ' , $time );
		$file_dir = ROOT . 'static/data/hash/content_attachment/'.$folder.'/' . $paths[0] . '/';
		$web_dir = 'static/data/hash/content_attachment/'.$folder.'/' . $paths[0] . '/';
		$file_name = 'file_' . $paths[1] . rand( 1 , 100000000 ) . '.' .$end ;

		MakeDir( $file_dir );
		if( move_uploaded_file( $file['tmp_name'] , $file_dir . $file_name ) )
		{
			$upload_url=   '/' . $web_dir . $file_name ;
			$this->show_js( $key , $upload_url , $type );

		}
		else
		{
			info_page( '上传的文件不成功，请稍后再试' );
			exit;
		}
	}
	private function show_js( $id , $url , $type )
	{
		if( $type == 'pic' )
		{
			$js = 'window.opener.document.getElementById("' .$id. '").value = "' .$url. '";';
			$js .= 'window.opener.document.getElementById("' . $id . '_pic").src = "' .$url. '";';
		}
		elseif($type == 'multi-pic')
		{
			$js = 'window.opener.document.getElementById("' .$id. '").value += "' .$url.'\r\n";';
			$js .= 'window.opener.show_pic_muti_preview( "' . $id . '" , "' . $id . '_pic" );';
		}
		else
		{
			$js = 'window.opener.document.getElementById("' .$id. '").value = "' .$url. '";';
		}
		$js .= 'window.close()';
		die( '<script>'.$js.'</script>' );
		
	}
	function snap( $folder = NULL , $url = NULL , $size = NULL )
	{	
		$url = $url?base64_decode( $url ):'/static/images/no_image.gif';
		if( !$folder )
		{
			die('error');
		}
		$width = app_config('pic_width' , $folder) > 0 ?app_config('pic_width' , $folder):90;
		$height = app_config('pic_height' , $folder) > 0 ?app_config('pic_height' , $folder):90;
		$size = intval( $size );
		if( $size > 0 )
		{
			$width = $size;
			$height = $size;
		}
		$md5 = md5($url);
		$web_path = "static/data/hash/snaps/".$folder.'/'. myhashstr( $md5 ) ;
		$path = ROOT . $web_path;
		$web_file = $web_path . md5($url) . $width . '_' . $height .  '.gif';
		$file = ROOT . $web_file;
		$source_file = $path . md5($url) . '.source.gif';
		if( !file_exists( $file ) )
		{
			@MakeDir( $path );
		
			if( !file_exists( $source_file ) )
			{
				if( !( $s = snoopy_copy( $url , $source_file ) ) )
				{
					$s = copy( ROOT . 'static/images/no_image.gif' , $source_file );
				}
			}
			if( $width == $height  )
			{
				$this->load->library('icon');
				$this->icon->path = $source_file;
				$this->icon->size = $width;
				$this->icon->dest = $file;
				$this->icon->createIcon();
			}
			else
			{
				$this->load->library('thumbnail');
				$this->thumbnail->setMaxSize( $width , $height  ); 
				$this->thumbnail->setImgSource(	$source_file ); 
				$this->thumbnail->Create( $file );
			}

		}
		readfile( $web_file );

	}
	function antilink( $folder = NULL , $url = NULL , $base = NULL )
	{
		if( !$folder || !$url )
		{
			return;
		}
		if( !is_dir( ROOT.'application/app/'.$folder ) )
		{
			return;
		}
		$url = base64_decode( $url );
		$base = base64_decode( $base );
		if( strtolower(substr( $url , 0, 7 )) != 'http://' )
		{
			$url = $base.$url;
		}
		if( strtolower(substr( $url , 0, 7 )) != 'http://' )
		{
			return;
		}
		$md5 = md5( $url );
		$web_dir = "static/data/hash/snaps/".$folder.'/'. myhashstr( $md5 );
		$web_file = $web_dir .$md5.'.gif' ;
		if( file_exists( ROOT.$web_file ) )
		{
			readfile(  $web_file );
		}
		$parse = parse_url( $url );
		$ref = dirname($url);
		$this->load->library('snoopy');
		$this->snoopy->referer = $ref;
		if( $this->snoopy->fetch( $url ) )
		{
			MakeDir( ROOT.$web_dir );
			$remote_file = $this->snoopy->results;
			file_put_contents( ROOT.$web_file , $remote_file  );
			echo $remote_file;
		}	
	}
}