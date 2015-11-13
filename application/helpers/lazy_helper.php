<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

define('ROOT', dirname(FCPATH).'/');
@date_default_timezone_set('Asia/Shanghai'); 

// lazy core common function
$CI =&get_instance();

// 迅捷函数
// =======================
// 单字母懒人函数

function v( $name )
{
	global $CI;
	return $CI->input->post( $name );
}

// for the textarea contaings rich text
function x( $name )
{
	$name = strip_tags( $name , '<a><img><u><b><strong><i><br/><br><p>' );
	global $CI;
	return $CI->input->xss_clean( $name );	
}

// no tag = clear all tag
// and change \r\n into br/
// for the textarea only contains text
function n( $name )
{
	$name = strip_tags( $name );
	$name = str_replace( "\n" , "<br/>" , $name );
	return $name; 
}

function r( $name )
{
	$name = strip_tags( $name , '<a><img><li><ol><ul><em><strong>' );
	$name = str_replace( "\n" , "<br/>" , $name );
	return $name; 
}
// only allow a string; max length is 255 ; clear all \r\n
// for the noraml line text
function z( $name )
{
	$name = strip_tags( $name );
	$name = str_replace( "\n" , " " , $name );
	return $name; 
}

function t( $name )
{
	$name = strip_tags( $name );
	return $name;
}

function s( $name )
{
	global $CI;
	return $CI->db->escape( $name );
}

function u( $name )
{
	return urlencode( $name );
}

function c( $name )
{
	global $CI;
	return $CI->config->item( $name );
}

function b( $name )
{
	return '<a href="javascript:history.back(1)">' . $name . '</a>';
}

//

function lazy_get_data( $sql = NULL , $key = NULL )
{
	global $CI;
	
	if( !isset($CI->db) ) $CI->load->database();
	
	$data = array();
	
	if( $sql != NULL )
	{
		$query = $CI->db->query( $sql );
		$result = $query->result_array(); 
	}
	else
	{
		$result = $CI->db->get()->result_array();
	}
	
	foreach( $result as $line )
	{
		if( isset( $line[$key] ) )
		{
			$data[$line[$key]] = $line;
		}
		else
		{
			$data[] = $line;
		}
	}
	return $data;
}

function lazy_get_line( $sql = NULL )
{
	$data = lazy_get_data( $sql );
	if( isset($data[0]) )
	{
		return $data[0];
	}
}

function lazy_get_var( $sql = NULL )
{
	$data = lazy_get_line( $sql );
	return $data[ @reset(@array_keys( $data )) ];
}
function lazy_get_vars( $sql = NULL )
{
	$data = lazy_get_data( $sql );
	$vars = array();
	if( $data )
	{
		foreach( $data as $v )
		{
			$key = $v[ @reset(@array_keys( $v )) ];
			if( $key )
			{
				$vars[$key] = $key;
			}
		}
	}
	return $vars;
}
function lazy_run_sql( $sql )
{
	global $CI;
	if( !$CI->db ) $CI->load->database();
	
	return $CI->db->simple_query( $sql );
}

function lazy_last_id()
{
	global $CI;
	if( !$CI->db ) $CI->load->database();
	return $CI->db->insert_id();
}





// 控制器相关迅捷函数
// =================================

function method()
{
	global $CI;
	if( $CI->uri->segment(2) )
		return $CI->uri->segment(2);
	else
		return 'index';
}

function module()
{
	global $CI;
	if( $CI->uri->segment(1) )
		return $CI->uri->segment(1);
	else
		return 'index';
}

// 模板相关迅捷函数
// =================================


function layout( $data = '' , $layout = 'default' , $base = 'index' )
{
	echo  get_layout( $data , $layout , $base );
}
function get_layout( $data = '' , $layout = 'default' , $base = 'index' )
{
	global $CI;

	if( !isset( $data['ci_module'] ) )
		$data['ci_module'] = module();
	
	if( !isset( $data['ci_method'] ) )
		$data['ci_method'] = method();
	$data['ci_layout'] = $layout;

	return $CI->load->view( 'layout/' . $layout .'/' . basename( $base ) . '.tpl.html' , $data ,true );

}
function is_login()
{

	return _sess('id');

}
function is_admin($level = 5)
{
	
	$slevel = _sess('u2_level');
	if($slevel >= $level)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function level_check( $level = NULL )
{
	if( !is_numeric($level) )
	{
		info_page( "error level" );
	}
	$level = intval( $level );
	$slevel = _sess('u2_level');
	if($slevel >= $level)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
function get_style()
{
	global $CI;
	$CI->load->library('session');
	$style = $CI->session->userdata('u2_style');
	if(!$style)
	{
		$style = 'default';
	}
	return $style;
}
function _sess($key)
{
	global $CI;
	$CI->load->library('session');
	return $CI->session->userdata($key);
}
function set_sess($key,$value = NULL)
{
	global $CI;
	$CI->load->library('session');
	
	
	if(is_array($key))
	{
		$newdata = $key;
	}
	else
	{
		$newdata[$key] = $value;
	}
	$CI->session->set_userdata($newdata);
}
function format_uid($uid = NULL)
{
	if($uid == NULL)
	{
		global $CI;
		$CI->load->library('session');
		$uid = $CI->session->userdata('id');
	}
	else
	{
		$uid = intval($uid);
	}
	return $uid;
}
function _text()
{
	global $CI;

	$numargs = func_num_args();

	$arg_list = func_get_args();

	$out_data = $CI->lang->line($arg_list[0]);

	if( $out_data )
	{
		if ( $numargs > 1 )
		{
			$app = '$out_data = sprintf( $out_data ';

			for ($i = 1; $i < $numargs; $i++)
			{
				$app = $app.",\$arg_list[$i]";
			}

			$app = $app.");";
	
			eval($app);
		}
		
		return $out_data;
	}
	else
	{
		return false;
	}
	
}
function info_page( $info , $url = NULL, $linkword = NULL,$title = NULL , $img = true )
{
	if( $title != NULL ) 

		$data['title'] = $title ; 
	else
		$data['title'] = _text('system_info_title');

	$data['top_title'] = $data['title'];
	
	$data['url'] = $url;

	$data['linkword'] = $linkword;
	
	$data['info'] = $info ;
	$data['img'] = $img;
	
	layout($data,'info');
	exit;

}
function myhash( $id = NULL )
{
	$id = format_uid($id);

	$f1 = $id % 10000 ; 
	$f2 = (int)($id / 10000) ;
	
	$f3 = $f1 % 100;
	$f4 = (int)($f1 / 100);

	return $f2 . '/' . $f4 . '/' . $f3 . '/';
}
function myhashstr( $str )
{
	return $str{0} . $str{1} . '/' . $str{2} . $str{3} . '/' ;
}
function show_user_icon(  $type = NULL, $uid = NULL)
{
	if( file_exists( get_user_icon_path( $type, $uid ) ) )
	{
		return get_user_icon_url($type, $uid);
	}
	else
	{
		$type=format_icon_type($type);

		return '/static/images/user_'.$type.'_icon.default.gif';
	}
}
function get_user_icon_path($type = NULL, $uid = NULL)
{
	$uid = format_uid($uid);

	$type=format_icon_type($type);

	return ROOT.'static/data/hash/user_icon/' . myhash( $uid ) . 'my_'.$type.'_icon.gif';
}
function get_user_icon_url( $type = NULL, $uid = NULL)
{
	$uid = format_uid($uid);

	$type=format_icon_type($type);

	return '/static/data/hash/user_icon/' . myhash( $uid ) . 'my_'.$type.'_icon.gif';
}
function format_icon_type($type)
{
	if($type == 'big' || $type == 'small')
	{
		return $type;
	}
	return 'normal';
}
function MakeDir($path)
{
	if (!file_exists($path))
	{
	   // The directory doesn't exist.  Recurse, passing in the parent
	   // directory so that it gets created.
	   MakeDir(dirname($path));
	   @mkdir($path, 0777);
	}
}
function make_user_icon_dir( $uid=NULL)
{
	$uid = format_uid($uid); 
	MakeDir( ROOT.'static/data/hash/user_icon/'.myhash( $uid ) );
}
function make_pro_icon_dir( $uid=NULL)
{
	$uid = format_uid($uid); 
	MakeDir( ROOT.'static/data/hash/pro_icon/'.myhash( $uid ) );
}
function get_pro_icon_path($uid=NULL)
{
	$uid = format_uid($uid); 
	return 'static/data/hash/pro_icon/'.myhash( $uid );
}
function get_user_old_url($item)
{
	return '/static/data/hash/user_icon/' . myhash( $item['u2_uid'] ) .$item['u2_pic_name'].'_normal.gif';
}

function word_substr($srt,$finish)
{
	if(mb_strlen($srt , 'utf-8') < $finish)
	{
		return $srt;
	}
	return mb_substr($srt,0,$finish,'utf-8').'...';
}


function get_data_by_array( $table , $array , $fkey , $id_field = 'id' , $where = '' )
{
	$ids = array();
	foreach( $array as $item )
	{
		$ids[] = $item[$id_field];
	}
	
	if( count( $ids ) > 0 )
	{
		$sql = "SELECT * FROM `" . $table .   "` WHERE `" . $fkey . "` IN ( " . join( ' , ' , $ids ) . " ) $where";
		$data = lazy_get_data( $sql );
		
		if( is_array( $data ) )
		{
			$ret = array();
			foreach( $data as $item )
			{
				$ret[$item[$fkey]] = $item;
			}
			
			return $ret;
		}
		else
		{
			return false;
		}
		
	}	
	
	
}
function check_login()
{
	if( !is_login() )
	{
		header('Location: /user/login/');
		die();
	}
}
function check_admin()
{
	if( !is_admin() )
	{
		info_page( _text('system_limit_rights'), '/user/login', _text('system_admin_login') );
	}
}
function newpassword()
{
	$password = array_merge( range( 'a' , 'z' ) , range( '0' , '9' ));
	$password = array_rand( $password , 20 );
	return $password = md5( rand( 1 , 10000 ). join( '' ,  $password ).format_uid() );
}
function show_pro_cate( $key )
{
	global $__pro_cates;
	if( $__pro_cates )
	{
		return $__pro_cates[$key]['u2_cate_desc'];
	}
	$sql = "select * from u2_cate ";
	$cates = lazy_get_data($sql);
	$temp = NULL;
	if( $cates )
	{
		foreach ($cates as $v)
		{
			$temp[$v['id']] = $v;
		}
	}
	$__pro_cates = $temp;

	return $__pro_cates[$key]['u2_cate_desc'];
}
function get_count( $save = true )
{
	if($save)
	{
		save_count();
	}
	if( isset($GLOBALS['__sql_count']) )
	{
		return $GLOBALS['__sql_count'];
	}
	return 0;
}
function save_count()
{
	$sql = "select found_rows()";
	$GLOBALS['__sql_count'] = lazy_get_var( $sql );
}
function sendmail( $to ,  $subject , $content ,$from = NULL )
{
	$content = ( strpos( $content , '<html>' ) === false )? '<html><head><meta http-equiv="Content-Language" content="zh-cn"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>'.$content.'</body></html>':$content;
	$to = is_array( $to )?$to:array($to);
	global $CI;
	$CI->load->library('sendmail');
	$CI->sendmail->to = array();
	if( c('stmp_is_used') )
	{
		$CI->sendmail->IsSMTP(); 
		$CI->sendmail->SMTPAuth = true;
		$CI->sendmail->Host = c('stmp_host');
		$CI->sendmail->Username = c('stmp_user');
		$CI->sendmail->Password = c('stmp_psw');
	}
	$from_mail = c('stmp_is_used') ? c('stmp_mail') : ( $from ? $from :c('website_mail') ) ;
	$CI->sendmail->From = $from_mail;
	$CI->sendmail->FromName = c( 'site_name' );
	$CI->sendmail->Charset = "utf-8";
	$CI->sendmail->Encoding = "base64";
	$count = count($to);
	foreach( $to as $v )
	{
		if( $count > 1 )
		{
			$CI->sendmail->AddBCC($v);
		}
		else
		{
			$CI->sendmail->AddAddress($v);
		}		
	}
	$replymail = $from ?$from :c('website_mail') ;
	$CI->sendmail->AddReplyTo( $replymail , c( 'site_name' ) );
  
	$CI->sendmail->WordWrap = 50; 
	$CI->sendmail->IsHTML(true);
  
	$CI->sendmail->Subject = $subject;
	$CI->sendmail->Body = $content;

	return $CI->sendmail->Send();
}

function check_app( $folder )
{
	return true;
}

function snoopy_copy( $url , $path , $ref = NULL)
{
	if( strstr( $url , 'http://' ) )
	{
		global $CI;
		$CI->load->library('snoopy');
		if( $ref != NULL )
			$CI->snoopy->referer = $ref;

		if( $CI->snoopy->fetch( $url ) )
		{
			if( !file_exists( $path ) )
			{
				file_put_contents( $path , $CI->snoopy->results  );	
				return true;
			}
			
		}
		else
		{
			return false;
		}
	}
	else
	{
		copy( ROOT.$url , $path );
		return true;
	}
	
	
}
function format_widget_row($row,$widgets)
{
	if(!is_array( $widgets[$row] ) || !$widgets[$row] )
	{
		return;
	} 
	$ajax = true;
	if( is_login() && !c('login_ajax') )
	{
		$ajax = false;
	}
	elseif( !is_login() && !c('logout_ajax') )
	{
		$ajax = false;
	}
	if( $ajax )
	{
		$widget_html_path  = APPPATH.'views/layout/riki/widget.tpl.html';
		$cid = _Page('cid');
		$pid = _Page('pid');
		foreach($widgets[$row] as $widget)
		{
			require($widget_html_path);
		}
		$GLOBALS['auto_drag'] = false;
	}
	else
	{
		$instances = lazy_get_data("select * from `u2_widget_instance` where `id` IN(".join(',',$widgets[$row]).") " , 'id');
		foreach( $widgets[$row] as $v  )
		{
			$instance = isset( $instances[$v] )?$instances[$v]:array();
			$extra = array();
			echo '<li><div id="widget-'.$v.'">';
			echo get_widget_html( $instance , $extra , $v );
			echo '</div></li>';
		}
		$GLOBALS['auto_drag'] = is_admin()?true:false;		
	}
}
function save_config($data)
{
	if ( ! file_exists(APPPATH.'config/u2'.EXT))
		{
			exit('The configuration file config'.EXT.' does not exist.');
		}
	if( ! is_writeable(APPPATH.'config/u2'.EXT) )
		{
			exit('The configuration file u2'.EXT.' does not writeable.');
		}

	require(APPPATH.'config/u2'.EXT);
	
	$out_data = NULL;
	
	foreach($config as $k => $v)
	{
		if(array_key_exists($k,$data))
			$v = addslashes($data[$k]);

		$out_data .= '$config['."'".$k."'] = '".$v."';\r\n";
	}
	file_put_contents(  APPPATH.'config/u2'.EXT , '<?php ' ."\r\n" . $out_data ."\r\n".  '?>');
}

function add_to_manager($table ,$id ,$desc ,$type , $is_active = 1 , $img = '/static/icon/details.gif' ,$key = 'u2_is_active')
{
	global $CI;
	if( $is_active )
	{
		$data['u2_state'] = 'accept';
	}
	else
	{
		$data['u2_state'] = 'wait';
	}
	$data['u2_table'] = $table;
	$data['u2_tid'] = $id;
	$data['u2_desc'] = $desc;
	$data['u2_type'] = $type;
	$data['u2_doid'] = 0;
	$data['u2_key'] = $key ;
	$data['u2_img'] = $img ;
	$data['u2_uid'] = format_uid();

	$CI->db->insert('u2_manager',$data);

}

function send_to_feed( $uid , $aid , $title , $content = '' , $image = NULL , $reskey = NULL )
{
	global $CI;
	if( $reskey )
	{
		$CI->db->where('u2_reskey' , $reskey );
		$CI->db->where('u2_app_aid' , $aid );
		$CI->db->delete( 'u2_mini_feed' );
	}
	$data = array();
	
	$data['u2_uid'] = intval( $uid );
	$data['u2_action'] = $title;
	$data['u2_desp'] = $content;
	$data['u2_time'] = date("Y-m-d H:i:s");
	$data['u2_img'] = $image;
	$data['u2_reskey'] = $reskey;
	$data['u2_app_aid'] = $aid;
	
	$CI->db->insert( 'u2_mini_feed' , $data );
}

function send_to_notice( $uid , $aid , $title , $content = NULL , $reskey = NULL )
{
	global $CI;
	if( $reskey )
	{
		$CI->db->where('u2_reskey' , $reskey );
		$CI->db->where('u2_app_aid' , $aid );
		$CI->db->delete( 'u2_notice' );
	}
	$data = array();
	
	$data['u2_uid'] = intval( $uid );
	$data['u2_app_aid'] = $aid;
	$data['u2_notice_title'] = $title;
	$data['u2_notice_content'] = $content;
	$data['u2_reskey'] = $reskey;
	$data['u2_time'] = date("Y-m-d H:i:s");
	
	$CI->db->insert( 'u2_notice' , $data );
	
}

function get_unread_pm()
{
	global $CI;
	$CI->db->select('count(*)')->from('u2_pm')->where( 'u2_sid' , format_uid() )->where( 'u2_is_read' , 0 );
	return lazy_get_var();
}

function get_unread_notice()
{
	global $CI;
	$CI->db->select('count(*)')->from('u2_notice')->where( 'u2_uid' , format_uid() )->where( 'u2_is_read' , 0 );
	return lazy_get_var();
}

function get_unread_request()
{
	global $CI;
	$CI->db->select('count(*)')->from('u2_fans')->where( 'u2_uid2' , format_uid() )->where( 'is_active' , 0 );
	return lazy_get_var();
}

function get_unread()
{
	return get_unread_notice() + get_unread_pm() + get_unread_request();
}


function pages_tabs($tag = NULL )
{
	global $CI;

	$CI->db->select('id , u2_tag , u2_link')->from('u2_page')->where('u2_is_system','0')->where('u2_in_tab','1')->orderby('u2_order');

	$pages = lazy_get_data();

	$html = NULL;

	if($pages)
	{
		$admin = is_admin();
		foreach($pages as  $p)
		{
			$nav_js = NULL;
			$nav_act = NULL;
			if($admin && $p['id'] != 1)
			{
				$nav_js = 'onmouseover="$(\'page_nav_'.$p['id'].'\').style.display=\'\'" onmouseout="$(\'page_nav_'.$p['id'].'\').setStyle(\'display\',\'none\')"';
				$nav_act = '<span id="page_nav_'.$p['id'].'" style="display:none;"><a href="#" onclick="link_del_confirm(\'/riki/delpage/'.$p['id'].'\')">&nbsp;<img src="/static/images/cross.gif" /></a></span>';
			}
			if($p['u2_tag'] == $tag)
			{
				$html .= '<li '.$nav_js.' class="out">'.$p['u2_tag'].$nav_act.'</li>';
			}
			elseif($p['u2_link'])
			{
				$add = NULL;
				$u2_link = get_page_link($p['u2_link']);
				if($u2_link['new_window'])
				{
					$add = 'target="_blank" ';
				}
				$html .= '<li '.$nav_js.'><a href="'.$u2_link['link'].'" '.$add.'>'.$p['u2_tag'].'</a>'.$nav_act.'</li>';
			}
			else
			{
				$html .= '<li '.$nav_js.'><a href="/riki/index/'.$p['id'].'">'.$p['u2_tag'].'</a>'.$nav_act.'</li>';
			}
		}
	}

	return $html;
}
function get_page_link($u2_link , $action = NULL)
{
	$u2_link = unserialize( $u2_link );


	if($action == 'link')
	{
		if(isset($u2_link['link']) && $u2_link['link'])
		{
			return $u2_link['link'];
		}
		else
		{
			return NULL;
		}
	}
	else
	{
		return $u2_link;
	}
}
function load_cates()
{
	global $CI;
	$CI->db->select('*')->from('u2_cate')->orderby('u2_cate_num');
	return lazy_get_data();
}
function check_active()
{
	global $CI;
	if( _sess('u2_level') < $CI->config->item('pro_no_check_level') )
	{
		return 0;
	}
	else
	{
		return 1;
	}
}
function gobal_page_item($key,$value)
{
	$GLOBALS['Page_info'][$key] = $value;
}
function global_page_info($info)
{
	$GLOBALS['Page_info']['type'] = isset($info['type'])?$info['type']:1;
	$GLOBALS['Page_info']['layout'] = $info['layout'];
//print_r($info);
	//$GLOBALS['Page_info']['layout'] = 2;
	if($info['type'] == 2)
	{
		if(isset($info['cateid']))
			$GLOBALS['Page_info']['cid'] = $info['cateid'];
	}
	elseif($info['type'] == 3)
	{
		if(isset($info['cateid']))
			$GLOBALS['Page_info']['cid'] = $info['cateid'];
		if(isset($info['cateid']))
			$GLOBALS['Page_info']['pid'] = $info['pro_id'];
	}
	else
	{
		return ;
	}
}
function _Page($str = NULL )
{
	if($str == NULL)
	{
		return $GLOBALS['Page_info'];
	}
	elseif( isset($GLOBALS['Page_info'][$str]) )
	{
		return $GLOBALS['Page_info'][$str];
	}
	else
	{
		return false;
	}
}
function get_pager(  $page , $page_all , $url_base ,$request_url = NULL )
{
	$middle = NULL;
	if( $page_all < 1 ) return false;
		

	if( $page != 1 ) $first = '&nbsp;<a href="' . $url_base . '/1/' . $request_url . '" title="首页"><img src="/static/images/arrow_fat_left.gif"></a>&nbsp;';
	else $first = '&nbsp;<a title="首页"><img src="/static/images/arrow_fat_left.gif"></a>&nbsp;';

	if( $page != $page_all ) $last = '<a href="' . $url_base . '/'.$page_all .'/' .  $request_url . '" title="末页"><img src="/static/images/arrow_fat_right.gif"></a>&nbsp;';
	else $last = '&nbsp;<a title="末页"><img src="/static/images/arrow_fat_right.gif"></a>&nbsp;';

	if( $page > 1 ) $pre = '&nbsp;<a href="' . $url_base . '/' . ($page-1) . '/' .$request_url . '" title="上一页"><img src="/static/images/arrow_dash_left.gif"></a>&nbsp;';
	else $pre = '&nbsp;<a title="上一页"><img src="/static/images/arrow_dash_left.gif"></a>&nbsp;';

	if( $page < $page_all ) $next = '<a href="' . $url_base . '/' . ($page+1) . '/'.$request_url . '"  title="下一页"><img src="/static/images/arrow_dash_right.gif"></a>&nbsp;';
	else $next = '&nbsp;<a title="下一页"><img src="/static/images/arrow_dash_right.gif"></a>&nbsp;';

	$show = 3; // 前后各显示?页
	$long = $show * 2 + 1;
	$begin = $page - $show;
	if( $begin < 1 ) $begin = 1;

	//echo "first begin $begin ";

	$end = $page + $show;

	if( ($t = $end - $begin) < $long )
	{
		$end = $begin+$long-1;
	}

	//echo " first end $end ";

	if( $end > $page_all )
	{
		//echo " end > $page_all ";
		
		// if( ($t = $end - $begin) < $long ) $begin = $begin - $t;
		$moved = $end - $page_all;

		$begin = $begin - $moved;
		
		$end = $page_all;
		
		//echo " the modified end $end , beging $begin";
		
		if( $begin < 1 ) $begin = 1;
	}

	//echo " $begin - $end ";



	for( $i = $begin ; $i <= $end ; $i++ )
	{
		if( $i == $page  )
			$middle .= '<a class="current">&nbsp;' . $i . '&nbsp;</a>';
		else
			$middle .= '<a href="' . $url_base . '/' . $i .'/' .$request_url . '">&nbsp;' . $i . '&nbsp;</a>';
	}

	if( $page_all > $long )
		$middle .= '<a>&nbsp;...&nbsp;</a>';

	return '<div class="pager" >' . $first .  $pre .  $middle . $next . $last . '</div>';

}
function get_ajax_pager(  $page , $page_all , $url_base = NULL ,$extra = NULL , $jsfunc = 'show_ajax_pager' ,$request_url = NULL )
{
	
	$middle = NULL;
	if( $page_all < 1 ) return false;
		

	if( $page != 1 ) $first = '&nbsp;<a href="JavaScript:void(0)" onclick=\''.$jsfunc.'("' . $url_base . '/1/' . $request_url . ' " ," '.$extra.'" )\' title="首页"><img src="/static/images/arrow_fat_left.gif"></a>&nbsp;';
	else $first = '&nbsp;<a title="首页"><img src="/static/images/arrow_fat_left.gif"></a>&nbsp;';

	if( $page != $page_all ) $last = '<a href="JavaScript:void(0)" onclick=\''.$jsfunc.'("' . $url_base . '/'.$page_all .'/' .  $request_url .'","'.$extra.'" )\' title="末页"><img src="/static/images/arrow_fat_right.gif"></a>&nbsp;';
	else $last = '&nbsp;<a title="末页"><img src="/static/images/arrow_fat_right.gif"></a>&nbsp;';

	if( $page > 1 ) $pre = '&nbsp;<a href="JavaScript:void(0)" onclick=\''.$jsfunc.'("' . $url_base . '/' . ($page-1) . '/' .$request_url . '","'.$extra.'" )\' title="上一页"><img src="/static/images/arrow_dash_left.gif"></a>&nbsp;';
	else $pre = '&nbsp;<a title="上一页"><img src="/static/images/arrow_dash_left.gif"></a>&nbsp;';

	if( $page < $page_all ) $next = '<a href="JavaScript:void(0)" onclick=\''.$jsfunc.'("' . $url_base . '/' . ($page+1) . '/'.$request_url .'","'.$extra.'" )\'  title="下一页"><img src="/static/images/arrow_dash_right.gif"></a>&nbsp;';
	else $next = '&nbsp;<a title="下一页"><img src="/static/images/arrow_dash_right.gif"></a>&nbsp;';
	$show = 4; // 前后各显示?页
	$long = $show * 2 + 1;
	$begin = $page - $show;
	if( $begin < 1 ) $begin = 1;

	//echo "first begin $begin ";

	$end = $page + $show;

	if( ($t = $end - $begin) < $long )
	{
		$end = $begin+$long-1;
	}

	//echo " first end $end ";

	if( $end > $page_all )
	{
		//echo " end > $page_all ";
		
		// if( ($t = $end - $begin) < $long ) $begin = $begin - $t;
		$moved = $end - $page_all;

		$begin = $begin - $moved;
		
		$end = $page_all;
		
		//echo " the modified end $end , beging $begin";
		
		if( $begin < 1 ) $begin = 1;
	}

	//echo " $begin - $end ";



	for( $i = $begin ; $i <= $end ; $i++ )
	{
		if( $i == $page  )
			$middle .= '<a class="current">&nbsp;' . $i . '&nbsp;</a>';
		else
			$middle .= '<a href="JavaScript:void(0)" onclick=\''.$jsfunc.'("' . $url_base . '/' . $i .'/' .$request_url .'","'.$extra.'")\' >&nbsp;' . $i . '&nbsp;</a>';
	}

	if( $page_all > $long )
		$middle .= '<a>&nbsp;...&nbsp;</a>';

	return '<div class="pager" >' . $first .  $pre .  $middle . $next . $last . '</div>';

}
function related_time( $t, $o='' )
{	
	$obj = array(
		0=>array('5*60'=>'刚刚'),
		1=>array('60*60'=>'%m分钟前'),
		2=>array('24*60*60'=>'%h小时前'),
		3=>array('7*24*60*60'=>'%d天前'),
		4=>array('30*24*60*60'=>'%w周前'),
		5=>array('365*24*60*60'=>'%F月前'), 
		6=>array('50*365*24*60*60'=>'%y年前'));
	
	$timestamp = strtotime($t);
	$nowstamp = time();
	$passedTime = $nowstamp - $timestamp;
	$m = ceil($passedTime / 60);
	$h = ceil($passedTime / (60*60));
	$d = ceil($passedTime / (24*60*60));
	$w = ceil($passedTime / (7*24*60*60));
	$f = ceil($passedTime / (30*24*60*60));
	$y = ceil($passedTime / (365*24*60*60));
	
	if ($o == '')
	{
		$o =  $obj;
	}
	
	for($i=0; $i<count($o); $i++)
	{
		$ret = '';
		$max = key($o[$i]);
		eval('$timeAge = '.$max.';');
		$ret = current($o[$i]);
	
		if ( $passedTime < $timeAge)
		{
			$ret = current($o[$i]);
			$ret = str_replace("%m",$m, $ret);
			$ret = str_replace("%h",$h, $ret);
			$ret = str_replace("%d",$d, $ret);
			$ret = str_replace("%w",$w, $ret);
			$ret = str_replace("%F",$f, $ret);
			$ret = str_replace("%y",$y, $ret);
			break;
		}
		
	}
	return $ret;
}

function get_left_apps()
{
	if( isset( $GLOBALS['left_apps'] ) && $GLOBALS['left_apps'] )
	{
		return $GLOBALS['left_apps'];
	}
	$GLOBALS['left_apps'] = lazy_get_data(" select * from `u2_app` where `u2_left_nav` = '1' order by `u2_order` asc ");

	return $GLOBALS['left_apps'];

}
function get_app_name_with_aid( $aid )
{
	global $CI;
	
	$CI->db->select('u2_title')->from('u2_app')->where('aid',$aid)->limit('1');
	
	return lazy_get_var();
}
function pm_cron_jobs()
{
	if( is_login() )
	{
		$notice = get_unread_notice();
		$pm = get_unread_pm();
		$req = get_unread_request();

		if( ($notice + $pm + $req) > 0 )
		{
			$GLOBALS['js_jobs'][] = "show_pm_cron_jobs('$notice','$pm','$req')";
		}
	}
}

function hp_cron_jobs()
{
	if( is_login() )
	{
		$sql = "SELECT * FROM `app_ihome_user` WHERE `uid` = '" . format_uid() . "' LIMIT 1";
		$line = lazy_get_line( $sql );
		if( $line['uid'] > 0 )
		{
			if( $line['hp'] < $line['hp_max'] )
			{
				$sql = "UPDATE `app_ihome_user` SET `hp` = `hp` + 1 WHERE `uid` = '" . format_uid() . "' LIMIT 1";
				lazy_run_sql( $sql );
			}
			
		}
	}
}

function app_config( $key , $appname = NULL )
{
	$return = NULL;
	$appname = $appname?$appname:(isset( $GLOBALS['app'] )?$GLOBALS['app']:NULL);
	if( $appname )
	{
		if( !isset( $GLOBALS['app_'.$appname.'_config'] ) )
		{
			$path = ROOT.'application/app/'.$appname.'/controller/';
			if( file_exists( $path.'admin_config.php' ) )
			{
				include_once( $path.'admin_config.php' );
				$GLOBALS['app_'.$appname.'_config'] = $config;
			}
			else
			{
				$GLOBALS['app_'.$appname.'_config'] = NULL;
			}
		}
		$return = isset( $GLOBALS['app_'.$appname.'_config'][$key] )?$GLOBALS['app_'.$appname.'_config'][$key]:NULL;
	}
	return $return;
}

function user_value_sub( $field , $value , $fname , $uid = NULL ,$return = NULL )
{
	$uid = format_uid($uid);
		
	$sql = "SELECT `" . $field  . "` FROM `app_ihome_user` WHERE `uid` = '" . intval( $uid ) . "' LIMIT 1 ";
	if( lazy_get_var( $sql ) < $value )
	{
		if( $return )
		{
			return false;
		}
		info_page('您的' . $fname . '不足');
		exit;
	}
	
	$sql = "UPDATE `app_ihome_user` SET `" . $field. "` = `" . $field . "` - " . intval( $value ) . " WHERE `uid` = '" . intval( $uid ) . "' LIMIT 1 " ;
	lazy_run_sql( $sql );
	return true;
}

function hp_sub( $hp , $uid = NULL , $return = NULL )
{
	return user_value_sub( 'hp' , $hp , '行动力' , $uid ,$return );
}

function money_sub( $money , $uid = NULL , $return = NULL )
{	
	return user_value_sub( 'g' , $money , '现金' , $uid , $return );		
}

function money_add( $money , $uid = NULL  )
{
	if( $uid == NULL ) 	$uid = format_uid();
	$sql = "UPDATE `app_ihome_user` SET `g` = `g` + " . intval( $money ) . " WHERE `uid` = '" . intval( $uid ) . "' LIMIT 1 " ;
	lazy_run_sql( $sql );
	
}

function elog( $error )
{
	file_put_contents( 'elog.txt' , $error ."\r\n" , FILE_APPEND );
}
function img1( $str )
{
	return trim( reset( explode( "\n" , $str ) ) );
}
function img2( $str )
{
	$m = explode( "\n" , $str );
	array_shift($m);
	return $m;
}
function jsnl2br( $str )
{
	$str = str_replace( "\r" , "" , $str );
	$str = str_replace( "\n" , "' + \"\\r\\n\" +'" , $str );
	return $str;
}
function add_slashes_on_quote( $str )
{
	return str_replace( "'" , "\\'" , $str );
}
function sys_info( $info , $url = NULL, $linkword = NULL,$title = NULL , $img = true )
{
	if( $title != NULL ) 

		$data['title'] = $title ; 
	else
		$data['title'] = '系统消息';

	$data['top_title'] = $data['title'];
	
	$data['url'] = $url;

	$data['linkword'] = $linkword;
	
	$data['info'] = $info ;
	$data['img'] = $img;

	@extract( $data );
	require( ROOT. 'application/views/layout/info/sys.tpl.html' );

	exit;

}

function get_name_by_uids( $uids )
{
	if( is_array( $uids ) )
	{
		return lazy_get_data("SELECT `id`,`u2_nickname` FROM `u2_user` WHERE `id` IN(".join(',',$uids).") " , 'id');
	}
}
function deldir( $dir ) 
{ 
	if ( @rmdir( $dir )==false && is_dir($dir) ) 
	{ 
		$all = glob( $dir. '*');
		if( $all )
		{
			foreach( $all as $item )
			{
				if( !is_dir( $item ) )
				{
					@unlink($item);
				}
				else
				{
					deldir( $item.'/' );
				}
			}
			@rmdir( $dir );
		}
	 }
}
function format_cate_order( $cid , $parent , $root = 0 )
{
	asort( $parent[$cid] ); 
	foreach( $parent[$cid] as $id => $order )
	{
		$info['id'] = $id;
		$info['root'] = $root;
		$ids[] = $info;
		if( isset( $parent[$id] ) )
		{
			$pids = format_cate_order( $id , $parent ,$root + 1 );
			$ids = array_merge( $ids , $pids );
		}
	}
	return $ids;
}
function save_app_config(  $data , $folder = NULL )
{
	if($data)
	{
		if( $folder === NULL  )
		{
			$folder = isset( $GLOBALS['app'] )?$GLOBALS['app']:NULL;
		}
		if( $folder === NULL )
		{
			info_page( ('system_error_app_folder') );
		}
		$out_data = NULL;
		$path = ROOT.'application/app/'.$folder.'/controller/';
		if( !isset( $GLOBALS['app_'.$folder.'_config'] ) )
		{
			include_once( $path.'admin_config.php' );
		}
		else
		{
			$config = $GLOBALS['app_'.$folder.'_config'];
		}
		
		if( !file_exists( $path.'admin_config.php' ) )
		{
			info_page( $path.'admin_config.php'._text('system_no_file') );
		}
		if( !is_writeable( $path.'admin_config.php' ) )
		{
			info_page( $path.'admin_config.php'._text('system_is_not_writeable') );
		}
		foreach( $config as $k => $v )
		{
			if( isset( $data[$k] ) )
			{
				if( is_array( $v ) )
				{
					if( is_array($data[$k]) )
						$v = $data[$k];
				}
				else
				{
					if( !is_array($data[$k]) )
						$v = $data[$k];
				}
			}
			$config[$k] = $v;
			$out_data .= '$config['."'".$k."'] = ".var_export( $v , 1 ).";\r\n";
		}
		file_put_contents(  $path.'admin_config.php' , '<?php ' ."\r\n" . $out_data ."\r\n".  '?>');
		$GLOBALS['app_'.$folder.'_config'] = $config;
	}

}
function get_shop_cates()
{
	$cates = array();
	$all = lazy_get_data(" select * from `u2_shop_cate` " , 'id');
	$parent = array();
	if( $all )
	{
		foreach( $all as $v )
		{
			$parent[$v['pid']][$v['id']] =  $v['orders'];
		}
		$ids = format_cate_order( 0 , $parent );
		foreach( $ids as $v )
		{
			$cates[$v['id']] = $all[$v['id']];
			$cates[$v['id']]['root'] = $v['root'];
		}
	}
	return $cates;
}
function get_child_cids( $id , $parent  )
{
	if( isset( $parent[$id] ) )
	{
		$cids[] = $id;
		foreach( $parent[$id] as $k => $v )
		{
			if(  isset( $parent[$k] ) )
			{
				$cids = array_merge( $cids , get_child_cids( $k , $parent  ) );
			}
			else
			{
				$cids[] = $k; 
			}
		}
	}
	else
	{
		$cids[] = $id; 
	}
	return $cids;
}
function get_widget_vars( $instance = NULL , $extra = NULL )
{
	$data = NULL;
	if( isset( $instance['u2_folder'] )  && file_exists(APPPATH.'app/'.$instance['u2_folder'].'/code.php')  )
	{
		include_once( APPPATH.'app/'.$instance['u2_folder'].'/code.php' );

		$app = explode('/', $instance['u2_folder'] );
		
		$wfname = 'get_'.strtolower($app[0]).'_'.strtolower($app[2]).'_data';

		if(function_exists($wfname) && isset($instance['u2_data']) )
		{
			if( $extra )
			{
				$para = unserialize( $instance['u2_data'] );
				$para['args'] = $extra;
				$para = serialize( $para );
				$data = $wfname( $para );	
			}
			else
			{
				$data = $wfname( $instance['u2_data'] );
			}
		}
	}

	return $data;

}
function add_widget_tools($html,$instance)
{
	$new = array();
	if( is_admin() )
	{
		$setting =  NULL;

		if(file_exists(APPPATH.'app/'.$instance['u2_folder'].'/extra.fields.html'))
		{
			$setting = '<a href="javascript:void(0)" onclick="javascript:ajax_widget_setting('.$instance['id'].','.intval(_Page('cid')).','.intval(_Page('pid')).')"><img src="/static/images/list_unordered.gif"></a>&nbsp;';
		}
		$new = array('<span>'.$setting.'<a href="javascript:void(0)" onclick="javascript:del_page_widget('.$instance['id'].')">&nbsp;<img src="/static/images/cross.gif" /></a></span><img src="/static/images/movearrow.gif" class="Drag">&nbsp;');
	}
	$old = array('<!--{tool bar}-->');

	$html = str_replace($old,$new,$html);

	return $html;
}
function get_widget_html( $instance , $extra , $id )
{
	if( isset( $instance['u2_folder']) )
	{
		$u2_folder = explode( '/', $instance['u2_folder'] );
		$GLOBALS['app'] = array_shift( $u2_folder );
	}
	if($instance)
	{
		$GLOBALS['widget_id'] = $instance['id'];
		$data = get_widget_vars($instance ,  $extra );
		
		$fliename  =  'default.tpl.html' ;

		if( file_exists( APPPATH.'app/'.$instance['u2_folder'].'/'.$fliename ) )
		{
			global $CI;

			$CI->load->_ci_view_path = APPPATH.'app/'.$instance['u2_folder'].'/';

			$html=$CI->load->view( $fliename ,$data , true );

			$CI->load->_ci_view_path = APPPATH.'views/';
		}
		else
		{
			$html = '<div class="boxes"><div class="hd"><!--{tool bar}-->error</div><div class="bd">Can not find default.tpl.html</div></div>';
		}

	}
	else
	{
		$instance['u2_folder'] = NULL;
		$instance['id'] = $id;
		$html = '<div class="boxes"><div class="hd"><!--{tool bar}-->error</div><div class="bd">Can not find widget instance</div></div>';
	}
	return add_widget_tools($html,$instance);
}
function get_widget_pager( $wid , $page , $page_all , $extra = NULL )
{
	
	$pid = _Page('pid');
	$base = 'JavaScript:ajax_widget_page( '.intval($wid).' , '.intval( _Page('cid') ).' , '.intval( _Page('pid') ).', \'';
	$extra = $extra."')";
	return get_pager( $page , $page_all , $base , $extra );
}
function app_files( $path )
{
	$list = array();
	$all = glob( $path . '/*');
	if( $all )
	{
		foreach( $all as $item )
		{
			if( is_dir( $item ) )
			{
				$list = array_merge( $list , app_files( $item ) );
			}
			else
			{
				$list[] = $item; 
			}
		}
	}

	return $list;
}
?>