<?php

class ajax extends Controller {

	function ajax()
	{
		parent::Controller();	
		$this->load->model('Ajax_model', 'ajax', TRUE);
		//$this->ajax_header();
	}
	
	function index()
	{
				
	}
	function widget()
	{		
		$args = func_get_args();
		$id = intval(array_shift( $args ));
		if($id < 1)
			return;
		gobal_page_item('cid',v('cid'));
		gobal_page_item('pid',v('pid'));


		$instance = $this->ajax->load_widget_instance(intval($id));
		$html = get_widget_html( $instance ,  $args , $id );
		die($html);
	
	}
	
	function ajax_header()
	{
		header("Expires: -1");
		header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		header("Pragma: no-cache");
		header("Content-type: application/html; charset=UTF-8");	
	}
	function delmessage($box = NULL,$id = NULL )
	{
		if($box != 'sendbox')
		{
			$box = 'inbox';
		}
		if(intval($id) < 0 )
		{
			echo false;
			return false;
		}
		$this->load->model('User_model', 'user', TRUE);

		if($this->user->del_message($box ,$id ))
		{
			echo  true;
			return true;
		}
		echo false;
		return false;
	}
	function save_widget_location($tag , $location ,$saved )
	{
		$this->is_admin();
		
		$tag = urldecode($tag);
		if(!$tag)
		{
			return false;
		}
		//$page_info = $this->ajax->load_page($tag);

		$page_para = $this->ajax->load_page_data($tag);

		$app = explode('-',$location);

		if($saved)
		{
			$old = $page_para['old'];
		}
		else
		{
			$old = array();

			foreach($page_para['widgets'] as $v)
			{
				$old = array_merge($old,$v);
			}
		}

		foreach($app as $k => $v)
		{
			$app_item = explode(',',$v);
			if( is_array($app_item) )
			foreach( $app_item as $item)
			{
				if( isset($old[$item]) &&  $old[$item] !== NULL )
				$new[$k][] = $old[$item];
			}

		}

		$page_para['old'] = $old;
		$page_para['widgets'] = $new;
	
		$this->ajax->update_page_data( $page_para ,$tag);
	
	}
	function get_app_list()
	{
		$this->is_admin();

		$apps = $this->ajax->get_apps();
		$html = NULL;
		foreach( $apps as $v )
		{
			$html .= '<div class="subMenu-title"><a href="javascript:void(0)" onclick="javascript:show_widget_byaid(\''.$v['aid'].'\')">'.$v['u2_title'].'</a></div><div style="display:none" id="widget_aid_'.$v['aid'].'" class="widget_item"></div>';
		}
		echo $html;
	}
	function get_widget_by_aid($aid)
	{
		$this->is_admin();

		$widgets = $this->ajax->get_widget_by_aid($aid);
		$html = NULL;
		$i=1;
		foreach($widgets as $w)
		{
			$html .= '<div class="subMenu-widget" ><a href="javascript:void(0)" title="'.$w['u2_desc'].'" onclick="add_widget_on_page(\''.$w['id'].'\')">'.$w['u2_name'].'</a></div>';
			$i++;
			if( $i % 2 )
			{
				$html .='<br clear="all"/>';
			}
		}
		echo $html.'<br clear="all"/>';
	}
	function add_widget_by_id( $tag ,$id = NULL)
	{
		$this->is_admin();
		$tag = urldecode($tag);
		$this->ajax->add_widget_to_page($tag ,$id );
		
		
	}
	function del_widget_by_id($tag, $id)
	{
		$this->is_admin();
		
		$tag = urldecode($tag);

		$data = $this->ajax->load_page_data($tag);

		foreach($data['widgets'] as $k => $v)
		{
			if( ($killid = array_search($id,$v )) !== false )
			{
				//echo $killid;
				unset($v[$killid]);
				$data['widgets'][$k] = $v;
			}
		}

		$this->ajax->update_page_data($data , $tag);

		$this->ajax->delete_instance_by_id($id);


	}
	
	function get_cates_html($action=NULL)
	{
		$this->is_admin();

		$cates = $this->ajax->load_cates();
		
		if( $action )
			$html = '<SELECT NAME="cateid" id="cateid" onchange="'.$action.'();void(0);" >';
		else
			$html = '<SELECT NAME="cateid" id="cateid">';
			
		if($cates)
		{
			foreach($cates as $c)
			{
				$html .='<OPTION VALUE="'.$c['id'].'" SELECTED>'.$c['u2_cate_desc'];

				
			}
		}
		$html .= '</SELECT>';
		echo $html;
		
	}
	
	function pro_extra_input($cid)
	{
		$this->load->model('Pro_model', 'pro', TRUE);
		$meta_field = $this->pro->load_meta_field($cid);

		$html = null;

		if($meta_field )
		{
			foreach ($meta_field as $m)
			{
				if($m['u2_type'] = 'text')
				{
					$html .= '<p class="item"><label>'.$m['u2_cn_name'].':</label>&nbsp;<input type="text" class="text" name="'.$m['u2_en_name'].'"></p>';
				}
			}
		}
		echo $html;
	}
	
	function get_widget_extra_html($id)
	{
		$this->is_admin();
	
		$instance = $this->ajax->load_widget_instance(intval($id));

		$data = unserialize($instance['u2_data']);

		$this->load->_ci_view_path = APPPATH.'app/'.$instance['u2_folder'].'/';

		$fliename = 'extra.fields.html';

		$html=$this->load->view( $fliename ,$data , true );
				
		$this->load->_ci_view_path = APPPATH.'views/';

		$cid = intval(v('cid'));

		$pid = intval(v('pid'));

		echo '<span class="r"><a href="JavaScript:void(0);" onclick="cancel_widget_extra()" ><img src="/static/images/cross.gif"></a></span><br clear="all">
		<form action ="/ajax/save_widget_extra/'
			. $id .'" method="post" id="widget_extra_'
			. $id .'"><INPUT TYPE="hidden" NAME="cid" value="'.$cid.'"><INPUT TYPE="hidden" NAME="pid" value="'.$pid.'">
			<div class="boxes"><div class="hd">'
			. _text('riki_edit_widget_settings') 
			.'</div><div class="app">'. $html
			.'<p></p><p><INPUT TYPE="submit" class="button" value="' 
			. _text('system_submit') 
			. '" onclick="send_widget_extra('.$id.');">&nbsp;<INPUT TYPE="button" class="button" value="'._text('system_cancel').'" onclick="cancel_widget_extra()"></p></div></div></form>';
	
	}
	function save_widget_extra($id)
	{
		$this->is_admin();
		$this->ajax->save_widget_extra($_POST,$id);
		$widget_html_path  = APPPATH.'views/layout/riki/widget.tpl.html';
		$widget = $id;
		$cid = intval(v('cid'));
		$pid = intval(v('pid'));
		$html = '<div class="boxes"><div class="hd">载入中</div><h4><img src="/static/images/loading.gif">Loading Please Wait.</h4</div><SCRIPT LANGUAGE="JavaScript">ajax_reload_widget('.$widget.','.$cid.','.$pid.')</SCRIPT>';
		
		die( $html );
		
	}
	function save_page_order($orders)
	{
		$this->is_admin();
		$this->ajax->save_page_order( explode(',',$orders) );
	}
	function is_admin()
	{
		if(!is_admin())
		{
			die('limit right');
		}
	}
	function change_page_link($id )
	{
		$this->is_admin();
		if( !v('link') )
			return;

		$this->ajax->update_page_link($id ,v('link'));
	}
	function admin_del_extra($id)
	{
		$this->is_admin();
		$this->ajax->del_data_extra($id);
	}
	function initialize_page($id)
	{
		$this->is_admin();
		$this->ajax->initialize_page($id);
	}
	
	function chang_page_display($pid,$display)
	{
		$this->is_admin();
		$this->ajax->chang_page_display($pid,$display);
		if($display)
		{
			$html =' <a href="javascript:void(0)" onclick="admin_change_page_display(\''.$pid.'\',\''.intval(!$display).'\')">' ._text('system_no').'</a>';
		}
		else
		{
			$html =' <a href="javascript:void(0)" onclick="admin_change_page_display(\''.$pid.'\',\''.intval(!$display).'\')">' ._text('system_yes').'</a>';
		}
		echo $html;
	}
	
	function updatestatus( $msg )
	{
		$msg = z( $msg );
		
		if( $msg != '' )
		{
			$this->load->model('User_model', 'user');
			$this->user->save_user_staus( $msg );
			$this->session->set_userdata( 'u2_desp' , $msg );

			$title = '<a href="/user/space/' . format_uid() . '">' . _sess('u2_nickname') . '</a>:' . $msg;
			send_to_feed( format_uid() , 'system_miniblog' , $title );
		}
	}
	
	function friend_request( $buddy , $to )
	{
		$uid = format_uid();
		$this->load->model('User_model', 'user');
		$this->user->update_friend_request( $uid , $buddy , $to );
		if( $to == 1 )
		{
			$buddyinfo = $this->user->load_user_information_by_uid( $buddy );
			
			$title = '<a href="/user/space/' . format_uid() . '">' 
			. _sess('u2_nickname') . '</a>和<a href="/user/space/' . $buddyinfo['id'] . '">' . $buddyinfo['u2_nickname'] . '</a>成为好友了';
			send_to_feed( format_uid() , 'system_friends' , $title );
			$title = '<a href="/user/space/' . format_uid() . '">' 
			. _sess('u2_nickname') . '</a>通过了您的好友请求.';
			send_to_notice( $buddyinfo['id'] , 'system_friends' , $title );
		}
		//echo 'hi' . $uid;
	}
	
	function minifeed_remove( $mid )
	{
		$uid = format_uid();
		$this->load->model('User_model', 'user');
		$this->user->remove_minifeed( $mid );
	}
	
	
	function wall_remove( $wid )
	{
		$uid = format_uid();
		$this->load->model('User_model', 'user');
		$this->user->remove_wall( $wid );
	}
	
	function chat( $uid )
	{
		$this->load->model('User_model', 'user');
		
		$data = array();
		$data['user'] =  $this->user->load_user_information_by_uid( $uid );
		$data['history'] = $this->user->get_chat_history( $uid );
		layout( $data , 'ajax' );
	}
	
	function pm_save()
	{
		$data = array();
		$data['u2_uid'] = format_uid();
		$data['uname'] = _sess('u2_nickname');
		$data['u2_sid'] = intval( v('sid') );
		$data['sname'] = z(v('sname'));
		
		$info = n(v('info'));
		$data['u2_pm_title'] = mb_substr( z($info) , 0 , 10 , 'utf-8' );
		$data['u2_pm_info'] = $info;
		
		$data['time'] = date("Y-m-d H:i:s");
		
		$this->db->insert( 'u2_pm' , $data );
	}
	function copyintive( $id )
	{
		$uid = format_uid($uid);
		$sql = "update u2_invite set u2_is_copied = '1' where id = '$id' and u2_uid = '$uid' limit 1";
		lazy_run_sql($sql);
	}
	
	public function cron_jobs( $module )
	{
		
		$job_ids = array();
		$GLOBALS['js_jobs'] = array();
		// corntab 实现
		$sql = "SELECT * FROM `u2_crontab` LIMIT 100";
		$data = lazy_get_data( $sql );
		
		if( is_array( $data ) )
		{
			// 对cron 进行 分类
			
			foreach( $data as $item )
			{
				if( $item['page_name'] == '' ) $item['page_name'] = 'EVERYWHERE';
				$jobs[strtolower($item['page_name'])][] = $item; 
			}
			
			// 首先执行全域cron
			if( is_array( $jobs['everywhere'] ) )
			{
				foreach( $jobs['everywhere'] as $job )
				{
					$this->exec_job( $job , $job_ids );
				}
			}
			// 然后执行当前页面触发的cron
			$module = strtolower( $module );
			
			if(  isset( $jobs[$module] ) && is_array( $jobs[$module] ) )
			{
				foreach( $jobs[$module] as $job )
				{
					$this->exec_job( $job , $job_ids );
				}
			}
			
			
		}
		if( count(  $job_ids ) > 0 )
		{
			$job_ids = array_unique( $job_ids );
			
			$sql = "UPDATE `u2_crontab` SET `last_exetime` = '".date("Y-m-d H:i:s")."' WHERE `id` IN ( " . join( ' , ' , $job_ids ) . " ) ";
			
			lazy_run_sql( $sql );
		}

		$js_jobs = NULL;

		if( $GLOBALS['js_jobs'] )
		{
			$js_jobs ='<SCRIPT LANGUAGE="JavaScript">'.join(';',$GLOBALS['js_jobs'] ).'</SCRIPT>';
		}

		echo $js_jobs;
		
	}
	
	private function exec_job( $item , &$job_ids )
	{
		// 当page_name为native时,只运行当前app

		if( $item['page_name'] == 'app' )
		{
			if( $item['app_name'] != $GLOBALS['app'] )
			return false;
		}

		// app_name不为空时加载app的function.php
		if( $item['app_name'] != '' )
		{
			include_once( APPPATH . 'app/' . basename( $item['app_name'] ) . '/controller/function.php' );
		}

		if( $item['type'] == 0 )
		{

			// 分钟设置
			$minute = date("i" , strtotime( $item['crontime'] ));
			
			if( $minute == '00' )
			{
				// 每分钟执行
				if( time() >= strtotime( $item['last_exetime']  . ' +1 minute'  ) )
				{
					call_user_func( $item['function_name'] );
					$job_ids[] = $item['id'];
				}	
			}
			else
			{
				$next_time = date("Y-m-d H:") + $minute + ':01';

				if( time() >= stototime($next_time) && strtotime($item['last_exetime']) < strtotime( $next_time ) )
				{
					call_user_func( $item['function_name'] );
					$job_ids[] = $item['id'];
				}
			}
		}
		elseif( $item['type'] == 5 )
		{
			// 查询执行
			call_user_func( $item['function_name'] );
		}


	}
	function copycard( $id )
	{
		if( is_admin() )
		{
			$sql = "update u2_recharge_card set u2_is_copied = '1' where id = '$id' limit 1";
			lazy_run_sql($sql);
		}
	}
	function save_app_order($orders)
	{
		$this->is_admin();
		$this->ajax->save_app_order( explode(',',$orders) );
	}
	function getres( $aname , $aid , $uid )
	{
		$html = NULL;
		if( !$aname || !$aid || !$uid)
		{
			die( _text('system_error_id') );
		}
		if( is_login() )
		{
			$res = $this->ajax->get_res_with_aids( $aname ,$aid );
			if( $res )
			{
				foreach( $res as $v )
				{
					$nick = $v['u2_uid'] == $uid ?'楼主':$v['nickname'];
					$html .= '<p id="res_item_'.$v['id'].'"><span class="nickname">'.$nick.'</span>:&nbsp;'.$v['u2_desp'].'</p>';
				}

			}
			$html .= '<TEXTAREA  NAME="res_desp_'.$aid.'" id="res_desp_'.$aid.'" style="WIDTH: 400px; HEIGHT: 80px;overflow:hidden" ></TEXTAREA> <INPUT TYPE="hidden" NAME="res_uid_'.$aid.'" id="res_uid_'.$aid.'"  value="'.$uid.'"><br/><span style="float:left;margin-top:10px;"><INPUT TYPE="button" class="button" value="提交回复" onclick="ajax_save_res(\''.$aname.'\',\''.$aid.'\' )">&nbsp;&nbsp;<INPUT TYPE="button" class="button" value="取消" onclick="cancel_res(\''.$aname.'\',\''.$aid.'\',\''.$uid.'\')" ></span>';
		}
		else
		{
			$html = _text('system_plz_login');
		}
		echo $html;
	
	}
	function saveres()
	{
		$aname = v('aname');
		$aid = v('aid');
		$uid = v('uid');
		$desp = z(v('desp'));
		if( is_login() )
		{
			if( !$aname || !$aid || !$uid || !$desp )
			{
				die( _text('system_error_id') );
			}
			$resid = $this->ajax->res_save( $aname , $aid , NULL , $desp );
			if( $uid !=  format_uid() )
			{
				$appname = get_app_name_with_aid( $aname );
				$title = '<a href="/user/space/' . format_uid() . '" target="_blank">' . _sess('u2_nickname') . '</a>回复了您的<a href="/app/native/'.$aname.'/show/'.$aid.'/" target="_blank">一篇'.$appname.'</a>';
				send_to_notice( format_uid($uid) , $aname , $title );
				$pic = NULL;
				if( $aname == 'icase' )
				{
					$url = $this->ajax->get_unique_pic_url($aid);
					if($url)
					{
						$pic = '/static/scripts/icon.php?url='.urlencode($url);
					}
				}
				$title = '<a href="/user/space/' . format_uid() . '" target="_blank">' . _sess('u2_nickname') . '</a>对<a href="/app/native/'.$aname.'/show/'.$aid.'/" target="_blank">'.$appname.'</a>发表了回复';
				send_to_feed( format_uid() , 'system_miniblog' , $title ,$desp , $pic );
			}
			
		}
		$this->getres( $aname , $aid , $uid );
	}
	function ticket($id)
	{
		$this->is_admin();
		$ticket = $this->ajax->get_ticket_by_id($id);
		if( !$ticket )
		{
			die( _text('system_error_id'));
		}
		$uid = format_uid();
		$appname = '意见反馈';
		if($ticket['action'] == '0' )
		{
			$data['action'] = '1';
			$data['auid'] = $uid;
			$title = '<a href="/user/space/' . $uid . '" target="_blank">' . _sess('u2_nickname') . '</a>正在处理您的<a href="/app/native/iticket/show/'.$id.'/" target="_blank">'.$appname.'</a>';
			send_to_notice( $ticket['uid'] , 'iticket' , $title );
		}
		elseif( $ticket['action'] == '1')
		{
			if( $ticket['auid'] != $uid )
			{
				die( _text('app_ticket_doing') );
			}
			else
			{
				$data['action'] = '2';
				$title = '<a href="/user/space/' . $uid . '" target="_blank">' . _sess('u2_nickname') . '</a>处理了您的<a href="/app/native/iticket/show/'.$id.'/" target="_blank">'.$appname.'</a>';
				send_to_notice( $ticket['uid'] , 'iticket' , $title );
			}
		}
		else
		{
			die( 'error' );
		}
		$this->ajax->update_ticket_by_id($id,$data);
	}
	function show_pics_icon( $limit , $cpl , $page )
	{
		$page = intval($page) < 1 ?1:intval($page);
		$list = $this->ajax->get_unique_pics($limit  , $page);
		$html = NULL;
		if( $list )
		{
			$all = get_count();
			$page_all = ceil($all / $limit );
			$url_base = '/ajax/show_pics_icon/'.$limit.'/'.$cpl ;
			$pager = get_ajax_pager(  $page , $page_all , $url_base ,'piclist' );
			$html = '<center>'.$pager.'</center>';
			$html .= '<TABLE width="100%">';
			$list = array_chunk( $list , $cpl  );
			foreach($list as $row )
			{
				$html .='<TR>';
				foreach($row as $m )
				{
				 $html .= '<TD align = "center" valign="top">
				<a href="/app/native/icase/show/'.$m['id'].'" target="_blank"><img src="/static/scripts/icon.php?url='.urlencode($m['url']).'"></a>
				</TD>';
				}
			$html .='</TR>';
			}
			$html .='</TABLE>';
			$html .= '<center>'.$pager.'</center>';
		}

		echo $html;
	}
	function icode()
	{
		$this->load->model('User_model', 'user', TRUE);
		$icode = z(v('icode'));
		$line = $this->user->check_invite_code( $icode );
		if( !$line )
		{
			die('错误的邀请函防伪码,请<a href="JavaScript:void(0)" onclick="$(\'notice\').setHTML(\'\');$(\'icode_input\').style.display=\'\';">重新输入</a>.');
		}
		$user = $this->user->load_user_information_by_uid( $line['u2_uid'] );
		$html = '<TABLE width="100%">
		<TR>
			<TD>原来是　<a href="/user/space/'.$user['id'].'" target="_blank">'.$user['u2_nickname'].'</a>　大人邀请的贵宾<br/><br/><a href="/gate/makeid/'.$icode.'">'.$user['u2_nickname'].'大人正在王国签证署等着您呢．赶快过去吧</a></TD>
			<TD><img src="'.show_user_icon('big',$user['id']).'" class="icon" /></TD>
		</TR>
		</TABLE>';
		echo $html;
	}
	function register()
	{
		$nickname = z(v('nickname'));
		$email = z(v('email' ));
		$psw1 = z(v('psw1' ));
		$psw2 = z(v('psw2' ));
		$icode = z(v('icode'));
		if( !$nickname || !$email || !$psw1 )
		{
			die('<center style="font-size:12px;">用户名,E-mail,密码不能为空</center>');
		}
		if( $psw1 != $psw2 )
		{
			die('<center style="font-size:12px;">2次密码输入不一致</center>');
		}
		$psw = $psw1;
		$this->load->model('User_model', 'user', TRUE);
		$invite = $this->user->check_invite_code($icode);
		if( !$invite )
		{
			die('<center style="font-size:12px;">邀请函防伪码已经使用过了</center>');
		}
		if( !$this->user->register_save($email,$nickname,$psw) )
		{
			die('<center style="font-size:12px;">用户名或者email已被占用</center>');
		}
		$this->user->marked_invite_code( $invite['id']) ;
		$user_info = $this->user->get_user_by_email( $email );
		$title = '<a href="/user/space/' . $user_info['id'] . '">' . $user_info['u2_nickname'] . '</a>加入了' . c('site_name');
		send_to_feed( $user_info['id'] , 'system_user' , $title );
		
		$invuid = $invite['u2_uid'];
		if($invuid && ( $olduser = $this->user->load_user_information_by_uid($invuid)) )
		{
			$this->user->add_friend( $invuid , $user_info['id'] );
			$title = '<a href="/user/space/' . $user_info['id'] . '">'.$user_info['u2_nickname'] . '</a>和<a href="/user/space/' . $olduser['id'] . '">' . $olduser['u2_nickname'] . '</a>成为好友了';
			send_to_feed( $user_info['id'] , 'system_user' , $title );
		}
		
		$new_one = array();
		$new_one['u2_first_time'] = 1;
		$new_one['u2_inviter_uid'] = $olduser['id'];
		$new_one['u2_inviter_nickname'] = $olduser['u2_nickname'];
		
		
		$this->user->login_confirm( $email , $psw , $new_one );
		
		if( isset( $_FILES['picfile']['size'] ) && ($_FILES['picfile']['size'] > 0) )
		{
			make_user_icon_dir();
			$this->load->library('icon');
			$this->icon->path = $_FILES['picfile']['tmp_name'];
			$this->icon->size = 16;
			$this->icon->dest = get_user_icon_path('small');
			$this->icon->createIcon();
			$this->icon->size = 48;
			$this->icon->dest = get_user_icon_path();
			$this->icon->createIcon();
			$this->icon->size = 100;
			$this->icon->dest = get_user_icon_path('big');
			$this->icon->createIcon();
			$time = time();
			$source_image = ROOT.'static/data/hash/user_icon/' . myhash() .$time.'.gif';
			copy(get_user_icon_path('small'),ROOT.'static/data/hash/user_icon/' . myhash() .$time.'_small.gif' );
			copy(get_user_icon_path(),ROOT.'static/data/hash/user_icon/' . myhash() .$time.'_normal.gif' );
			copy(get_user_icon_path('big'),ROOT.'static/data/hash/user_icon/' . myhash() .$time.'_big.gif' );
			move_uploaded_file( $_FILES['picfile']['tmp_name'] , $source_image  );
			$this->user->add_user_upload_pic($time);
			$title = '<a href="/user/space/' . format_uid() . '">' . _sess('u2_nickname') . '</a>更换了新头像';
			send_to_feed( format_uid() , 'system_user' , $title , NULL ,  show_user_icon() );
		}
		
		$uid = $user_info['id'];
		$place = $uid + 10000;
		
		// add the inviter's info 
		//set_cookie( 'x123' , 'er' );
		
		
		// add money to user bank account
		// add money 
		
		
		$sql = "INSERT INTO `app_ibank_account` ( `uid` , `g_count` , `gold_count` ) VALUES ( '" . $uid . "' , '" . intval(c('user_init_silver')) . "' , '" . intval(c('user_init_gold')) . "' ) ";
		lazy_run_sql( $sql );

		// add cloth
		$sql = "INSERT INTO `app_ihome_shop` ( `uid` , `item_id` ) VALUES ( '" . $uid . "' , '844'  ) , ( '" . $uid . "' , '879' )";
		lazy_run_sql( $sql );
		
		// add money to inviter
		$sql = "UPDATE `app_ibank_account` SET `g_count` = `g_count` + " . intval( c('user_invite_g') ) . " WHERE `uid` = '" . intval( $olduser['id'] ) . "' LIMIT 1 ";
		
		lazy_run_sql( $sql );
		
		
		// header("Content-type: text/xml; charset=$charset"); 
		header("Content-type: text/html;charset=utf-8");
		echo '<SCRIPT LANGUAGE="JavaScript">window.parent.$("id_icon").setHTML(\'<img src="'.show_user_icon('',$user_info['id']).'" class="icon"/><br/>'.$nickname.'\');window.parent.$("id_info").setHTML(\'NO. '.$uid.'<br/>仙豆王国居民东区'.$place.'#\');</SCRIPT>';
		echo('<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body><center style="font-size:12px;"><a href="/user/miniblog/" target="_parent">申请成功,赶快到你的小屋去看看吧</a></center></body></html>');
	}
	function show_snotice( $id , $name )
	{
		if( is_login() )
		{
			$id = intval($id);
			if( $id )
			{
				lazy_run_sql( "update `u2_user` set `u2_snotice` = '$id' where `id` = '".format_uid()."' limit 1" );
				$data['u2_snotice'] = $id;
				set_sess($data);
			}
			$nowid = _sess('u2_snotice');
			$now = date("Y-m-d");
			$line = lazy_get_line("select * from `u2_snotice` where `id` > '$nowid' and `start_time` <= '$now' and `end_time` >= '$now' order by `id` ASC limit 1 ");
			if( $line )
			{
				$newid = $line['id'];
				$html = '<span class="r"><a href="JavaScript:void(0)" onclick="show_snotice(\''.$name.'\',\''.$newid.'\')"><img src="/static/images/cross.gif"></a></span>';
				echo $html.$line['desp'];
			}
		}
	}
	function show_iinvite_box()
	{
		if( is_login() )
		{
			$uid = format_uid();
			$mails = lazy_get_data("select * from app_iinvite_emails where uid = '".format_uid()."' and no_in_site = '1' ");
			if( $mails )
			{
				$html = '<form action="/app/native/iinvite/sendmail" method="post" id="mail_list" name="mail_list" style="margin:0px"><div style="height:240px;overflow:auto">';
				foreach($mails as $v )
				{
					$html .= '<div style="line-height:12px;" ><INPUT TYPE="checkbox" NAME="emails[]" value="'.$v['email'].'" checked>&nbsp;&nbsp;&nbsp;'.$v['email'].'</div>';
				}
				$html .= '<br/><P><INPUT class=button onclick="ajax_send_mails(\'mail_list\');" type=button value=发送邀请邮件></P></div></div>';
			}
			else
			{
				$html = '<INPUT TYPE="hidden" NAME="type" id="type" value="msn">
						<INPUT TYPE="hidden" NAME="domain" id="domain" value="">
						<P style="FONT-SIZE: 13px; MARGIN: 10px 0px"><B>导入MSN联系人</B></P>
						<P style="MARGIN: 5px 0px">MSN帐号：<INPUT  name="email"  id="email" style="width:115px"></P>
						<P style="MARGIN: 5px 0px">MSN密码：<INPUT  name="psw" id="psw" type="password" style="width:115px" value=""></P>
						<P style="PADDING-LEFT: 60px; PADDING-TOP: 8px"><INPUT class="button" onclick="ajax_send_msn_account()" type="button" value="导入"></P>';
			}
			echo $html;
		}
	}
	function update_code( $id )
	{
		if( !is_admin() )
		{
			die('你没有权限进行此操作');
		}
		$id = intval($id);
		if( $id < 1 ) die('<img src="/static/images/cross.gif" />&nbsp;错误的文件ID');
		$this->load->library('service');
		$this->service->add( 'id' ,$id );
		$result = $this->service->result( 'getFile' );
		if( $result['flag'] == 'ok' )
		{
			if( $result['error'] )
			{
				die('<img src="/static/images/cross.gif" />&nbsp;'.$result['notice'] );
			}
			if( md5($result['content']) != $result['info']['filemd5']  )
			{
				die('<img src="/static/images/cross.gif" />&nbsp;数据传输出错');
			}

			$this->load->model('Admin_model', 'admin', TRUE);

			$local_file = ROOT . $result['info']['filepath'];
			if( file_exists( $local_file ) )
			{
				if( md5_file( $local_file ) == $result['info']['filemd5'] )
				{
					$this->admin->del_update_id( $result['info']['id'] );
					die('<img src="/static/images/tick.gif">&nbsp;已经最新');
				}
				
				if( !is_writable( $local_file ) ) die( '<img src="/static/images/cross.gif" />&nbsp;请修改属性为可写' );
			
				// 备份本地代码
				@copy( $local_file , ROOT . 'upgrade/' . $result['info']['id'] . '.backfile.sns' );
			
			}
			
			if( @file_put_contents( $local_file , $result['content'] ) )
			{
				 $this->admin->del_update_id( $result['info']['id'] );
				die('<img src="/static/images/tick.gif">&nbsp已更新');
			}
			else
			{
				die('<img src="/static/images/cross.gif" />&nbsp;文件写入错误');
			}
		}
		else
		{
			die('<img src="/static/images/cross.gif" />&nbsp;网络查询错误');
		}
	
	}
	function votesave() 
	{

		$wid = intval( v('wid') );
		$uid = format_uid();
		$con =  v('radios') ;
		
		if( !is_login() )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\''._text('system_plz_login').'\')</SCRIPT>');
		}
		if( !isset($con) || empty($con) )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'请选择后投票\')</SCRIPT>');
		}
		$check = lazy_get_var("SELECT count(*) FROM `u2_vote_value` WHERE `wid` = '".intval( $wid )."' AND `uid` = '".intval( $uid )."' ");
		if( $check )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'您已经投过票了\')</SCRIPT>');
		}

		foreach( $con as $v )
		{
			$temp = lazy_get_var("select count(*) from `u2_vote_count` where `wid` = '$wid' and `key` = '".intval($v)."' ");
			if( $temp )
			{
				lazy_run_sql("update `u2_vote_count` set `count` = `count`+ 1  where `wid` = '$wid' and `key` = '".intval($v)."' ");
			}
			else
			{
				lazy_run_sql("insert into `u2_vote_count`(`wid`,`key`,`count`)values('$wid','".intval($v)."','1') ");
			}
		}
		lazy_run_sql( "INSERT INTO `u2_vote_value` ( `wid`, `uid`, `value`, `time` ) VALUES ( '".intval($wid)."' , '".intval($uid)."' , '".serialize($con)."' , '".date('Y-m-d H:i:s')."')" );
		$this->voteshow( $wid );
	}

	function voteshow( $wid = NULL ) 
	{
		$widget  = lazy_get_var("SELECT `u2_data` FROM `u2_widget_instance` WHERE `id` = '".intval($wid)."'") ;
		if( !$widget ) 
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'错误的ID\')</SCRIPT>');
		}
		$widget_info = unserialize($widget);
		$vote = $widget_info['vote'];
		if( !is_array($vote) ) 
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'错误的ID\')</SCRIPT>');
		}
		$count = 0;
		$results = array();
		foreach( $vote as $k => $v )
		{
			//print_r($vote);
			$v_count = lazy_get_var("SELECT `count` FROM `u2_vote_count` WHERE `wid` = '".intval($wid)."' and `key` = '".intval($k+1)."' ");
			$v_count = intval( $v_count );
			$count += $v_count;
			$res['name'] = $v;
			$res['count'] = $v_count;
			$results[] = $res;
		}

		$sum_num = lazy_get_var( "SELECT COUNT(*) FROM `u2_vote_value` WHERE `wid` = '".intval($wid)."'" );
		$html = "<div>共有".$sum_num."人参与投票</div>";
		
		$i = 0;
		foreach( $results as $v )
		{	
			$i++;
			$v['name'] = empty($v['name']) ? "选项".$i."号" : $v['name'] ;
			$num2 = floor(($v['count']/$count)*100);
			$html .= "<DIV id=update><DIV style=\'width=100%;\'>".$v['name']."&nbsp;&nbsp;(".$v['count']."人)<DIV style=\'border:1px solid #ccc;height:10px;width:80%\'><DIV style=\'WIDTH: ".$num2."%; HEIGHT: 98%; BACKGROUND-COLOR: #acd6ff\'></DIV></DIV><span class=l></span></DIV></DIV>";
		}

		die('<SCRIPT LANGUAGE="JavaScript">$(\'widget_info_'.$wid.'\').setHTML(\''.$html.'\')</SCRIPT>');
		
	}
	function contents()
	{
		$this->is_admin();
		$action = v('action');
		$ids = v('ids');
		$action_array = array('accept','forbidden');
		if( !in_array( $action , $action_array ) )
		{
			die('error action');
		}
		if( !$ids )
		{
			die('error id');
		}
		$ids = v('ids');
		if( !is_array( $ids ) )
		{
			$ids = array( intval($ids) );
		}
		$this->ajax->contents_manage($ids ,$action);
	}
	function u_do_manage()
	{
		$this->is_admin();
		$this->ajax->u_do_manager();
		
	}
	function karma( $app = NULL, $cid = NULL , $uid = NULL  )
	{
		$cid = intval( $cid );
		if( !$app || !$cid || !$uid )
		{
			return;
		}
		$records = lazy_get_data("select * from `u2_karma_record` where `app` = '$app' and `cid` = '$cid' order by `id` desc ");
		if( !is_admin() && !$records )
		{
			return;
		}
		$html = '<FIELDSET style="padding:10px;border:1px solid #ccc;line-height:150%;color:gray"><LEGEND>奖励记录:</LEGEND>';
		if( is_admin() )
		{
			$html .= '<span class="r"><form action="/admin/record/" id="karma_form" method="post"><INPUT TYPE="hidden" NAME="app" value="'.$app.'"><INPUT TYPE="hidden" NAME="cid" value="'.$cid.'"><INPUT TYPE="hidden" NAME="tuid" value="'.$uid.'"><SELECT name="karma" onchange="$(\'karma_form\').submit();"><OPTION value=0 selected>请选择</OPTION> <OPTION value=50>+50</OPTION> <OPTION value=10>+10</OPTION> <OPTION value=5>+5</OPTION> <OPTION value=-5>-5</OPTION> <OPTION value=-10>-10</OPTION> <OPTION value=-50>-50</OPTION></SELECT></form></span>';
		}
		if( $records )
		{
			foreach( $records as $v )
			{
				$uids[$v['admin_uid']] = $v['admin_uid'];
			}
			$names = get_name_by_uids( $uids );
			foreach( $records as $v )
			{
				$html .= '<font color="green">'.$v['action'].'</font> By <a href="/user/space/'.$v['admin_uid'].'" target="_blank">'.$names[$v['admin_uid']]['u2_nickname'].'</a>'.strip_tags( $v['reason'] ).' At '.$v['time'].'<br/>';
			}
		}
		$html .= '</FIELDSET>';
		echo $html;

	}
	function update_widgets( $folder )
	{
		$this->is_admin();
		$new_widgets = array();
		$path = ROOT.'application/app/'.$folder.'/';
		if( file_exists( $path.'controller/operate.php' ) )
		{
			$widgets = array();
			include_once( $path.'controller/operate.php' );
			$widgets = array_change_key_case($widgets);
		}
		else
		{
			die('error app folder!');
		}
		if( file_exists( $path . 'widgets/' ) )
		{
			include_once( $path.'controller/config.php' );
			$old_widgets = lazy_get_data("select * from `u2_widget` where `u2_aid` = '".$app_config['id']."'  " , 'u2_folder' );

			$path_length = strlen( $path );			
			$list = glob( $path . 'widgets/*');
			if(is_array($list))
			{
				foreach($list as $item)
				{
					if( is_dir($item) )
					{
						if(file_exists ($item.'/default.tpl.html') )
						{
							$widget_folder = $folder.'/'.substr( $item , $path_length );
							$base_folder = strtolower(trim(end(explode( '/', $widget_folder ))));
							if( file_exists ($item.'/config.php') )
							{
								include_once( $item.'/config.php' );
							}
							else
							{
								if( isset( $widgets[$base_folder] ) && $widgets[$base_folder] )
								{
									$config['name'] = $widgets[$base_folder];
									$config['desc'] = $widgets[$base_folder];
								}
								else
								{
									$config['name'] = $base_folder;
									$config['desc'] = $base_folder;
								}
								$config['stats'] = 0;
							}
							$new = array();
							$new['u2_name'] = $config['name'];
							$new['u2_desc'] = $config['desc'];
							$new['u2_stats'] = $config['stats'];
							$new['u2_aid'] = $app_config['id'];
							$new['u2_folder'] = $widget_folder;
							$new_widgets[$widget_folder] = $new;
						}

					}
				}
			}
			$res = $this->ajax->update_widgets( $old_widgets , $new_widgets );
			lazy_run_sql("update `u2_app` set `u2_has_widgets` = '{$res['count']}' where LCASE(`u2_folder`) = '".strtolower($folder)."' limit 1");
			echo $res['update'];
		}
		else
		{
			die('no app folder!');
		}
	}
	function rate( $folder = NULL , $cid = NULL , $star = NULL )
	{
		if( !is_login() )
		{
			die( _text('system_plz_login') );
		}
		$cid = intval( $cid );
		$star = intval( $star );
		$mid = intval( app_config( 'mid' , $folder ) );
		if( $mid < 0 || $cid < 0 || $star < 0 ||  $star > 5 )
		{
			die( _text('system_error_id') );
		}
		$uid = format_uid();
		$sql = "replace into `u2_rate` (`uid` , `mid` , `cid` , `rate` , `time` )values('$uid','$mid','$cid','$star' , '".date("Y-m-d H:i:s")."')";
		lazy_run_sql( $sql );
	}
	function del_app_snap( $folder = NULL )
	{
		$this->is_admin();
		if(  !$folder || !is_dir( ROOT.'static/data/hash/snaps/'.$folder  ) )
		{
			die('error data');
		}
		deldir( 'static/data/hash/snaps/'.$folder );
	}
	function show_shop_cate( $id = NULL , $itype = NULL )
	{
		$this->is_admin();
		$itype = intval( $itype );
		$html = NULL;
		$all_cate = lazy_get_data("select * from `u2_shop_cate` ", 'id');
		if( !$all_cate )
		{
			$html = '没有分类,请先<a href="/admin/shop/add/cate/">增加分类</a><INPUT TYPE="hidden" NAME="type" id="type" value="0">';
			die( $html );
		}
		else
		{
			foreach( $all_cate as $v )
			{
				$parent[$v['pid']][$v['id']] =  $v['orders'];
			}
			$ids = format_cate_order( 0 , $parent );
			foreach( $ids as $v )
			{
				$cates[$v['id']] = $all_cate[$v['id']];
			}
			$id = intval( $id );
			if( !isset( $cates[$id] ) )
			{
				$cate = current($cates); 
				$id = $cate['id'];
			}
			$type = 0;
			foreach( $cates as $v )
			{
				$html = '分类&nbsp;&nbsp;<SELECT NAME="cate" id="cate" onchange="show_shop_cate(this.value , 0 )">';
				foreach( $cates as $v )
				{
					if( $id == $v['id'] )
					{
						$type = intval( $v['cate_type'] );
						$html .= '<OPTION VALUE="'.$v['id'].'" SELECTED>'.$v['cate_desc'];
					}
					else
					{
						$html .= '<OPTION VALUE="'.$v['id'].'">'.$v['cate_desc'];
					}
				}
				$html .= '</SELECT>';
			}
			$type = $itype > 0 ? $itype : $type ;
			$html .= '<SCRIPT>set_shop_type('.$type.')</SCRIPT>';
			die( $html );
		}
	}
	function show_shop_extra_input( $id = NULL , $cid = NULL )
	{
		$this->is_admin();
		$id = intval( $id );
		$cid = intval( $cid );
		$html = NULL;
		$line = lazy_get_line( "select * from `u2_shop_type` where `id` = '$id' limit 1 " );
		if( !$line )
		{
			die("error shop type!");
		}
		$extra = unserialize( $line['extra'] );
		$field = (isset( $extra['field'] ) && $extra['field'] )?$extra['field']:NULL;
		if( $field )
		{
			$check = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".$id."' " );
			$ex_data = array();
			if( $check )
			{
				$ex_data = lazy_get_line("select * from `shop_extra_".$id."` where `cid` = '$cid' limit 1 ");
			}
			$html = '<TABLE width="100%">';
			foreach( $field as $v )
			{
				$html .='<TR><TD width="70px">'.$v['label'].'</TD><TD>';
				if( $v['type'] == 'line' )
				{
					$value = isset( $ex_data['extra_'.$v['id']] ) && $ex_data['extra_'.$v['id']]  ?addslashes($ex_data['extra_'.$v['id']]):NULL;
					$html .='<INPUT TYPE="text" NAME="extra['.$v['id'].']" class="text" value="'.$value.'">';
				}
				elseif( $v['type'] == 'checkbox' )
				{
					$values = isset( $ex_data['extra_'.$v['id']] ) && $ex_data['extra_'.$v['id']]  ?unserialize( $ex_data['extra_'.$v['id']] ):array();
					foreach( $v['tvalue']['name'] as $key => $value )
					{
						if( in_array( $v['tvalue']['value'][$key] , $values ) )
						{
							$html .= '<INPUT TYPE="'.$v['type'].'" NAME="extra['.$v['id'].'][]" value="'.$v['tvalue']['value'][$key].'" checked >'.$value.'&nbsp;&nbsp;';
						}
						else
						{
							$html .= '<INPUT TYPE="'.$v['type'].'" NAME="extra['.$v['id'].'][]" value="'.$v['tvalue']['value'][$key].'">'.$value.'&nbsp;&nbsp;';
						}
					}
				}
				elseif( $v['type'] == 'radio' )
				{
					$values = isset( $ex_data['extra_'.$v['id']] )  ?$ex_data['extra_'.$v['id']]:NULL;
					foreach( $v['tvalue']['name'] as $key => $value )
					{
						if( $value == $values  )
						{
							$html .= '<INPUT TYPE="'.$v['type'].'" NAME="extra['.$v['id'].']" value="'.$v['tvalue']['value'][$key].'" checked >'.$value.'&nbsp;&nbsp;';
						}
						else
						{
							$html .= '<INPUT TYPE="'.$v['type'].'" NAME="extra['.$v['id'].']" value="'.$v['tvalue']['value'][$key].'">'.$value.'&nbsp;&nbsp;';
						}
					}
				}
				elseif(  $v['type'] == 'dropdown' )
				{
					$values = isset( $ex_data['extra_'.$v['id']] ) && $ex_data['extra_'.$v['id']]  ?addslashes($ex_data['extra_'.$v['id']]):NULL;
					$html .= '<SELECT NAME="extra['.$v['id'].']">';
					foreach( $v['tvalue']['name'] as $key => $value )
					{
						if( $value == $values )
						{
							$html .= '<OPTION VALUE="'.addslashes($v['tvalue']['value'][$key]).'" SELECTED >'.$value.'&nbsp;&nbsp;';
						}
						else
						{
							$html .= '<OPTION VALUE="'.addslashes($v['tvalue']['value'][$key]).'">'.$value.'&nbsp;&nbsp;';
						}
					}		
					$html .= '</SELECT>';
				}
				$html .= '</TD><TD width="90px"></TD></TR>';
			}
			$html .= '</TABLE>';
		}
		die( $html );
	}
	function shop_extra_set()
	{
		$this->is_admin();
		$id = v('id');
		$values = v('value');
		if( $values == '0' )
		{
			$info['type'] = v('type');
			$info['label'] = NULL;
		}
		else
		{
			$info = unserialize( base64_decode( $values ) );
			if( v('type') != '0' )
				$info['type'] = v('type');
		}
		$html = '<form onsubmit="return false;" action="/ajax/shop_extra_save/" method="post" id="shop_extra"><INPUT TYPE="hidden" NAME="id" value="'.$id.'"><div style="text-align:left;padding:10px;"><table width="100%"><tr><td colspan="2">标题<br/><input type="text" class="text" name="label" value="'.$info['label'].'" class="fitem_full" /></td></tr>';
		$all_types = array('line' => '单行文字' , 'radio' => '单选', 'checkbox' => '多选' , 'dropdown' => '下拉框');

		$html .= '<tr><td colspan="2">字段类型<br/><SELECT NAME="type" onchange="shop_extra_field('.$id.', this.value )">';
			
		foreach( $all_types as $k => $v  )
		{
			if( $k == $info['type'] )
			{
				$html .= '<OPTION VALUE="'.$k.'" SELECTED>'.$v;
			}
			else
			{
				$html .= '<OPTION VALUE="'.$k.'">'.$v;
			}
		}
		$html .= '</SELECT></td></tr>';
		$html .= '</table>';
		if( $info['type'] != 'line' )
		{
			$info['tvalue'] = isset( $info['tvalue'] )?$info['tvalue']:NULL;
			$count = isset( $info['tvalue']['name'] )?count( $info['tvalue']['name']):0;
			$html .= '<div id="mutli_input"><SCRIPT LANGUAGE="JavaScript">format_mutli_input( \''.json_encode($info['tvalue']).'\' ,'.$count.' )</SCRIPT></div>';
		}
		$html .= '<table><tr><td><INPUT TYPE="button" class="button" value="确定" onclick="shop_extra_send(\'shop_extra\');"></td><td><INPUT TYPE="button" class="button" value="取消" onclick="$(\'notice_box\').remove();"></td></tr></table>';
		$html .= '</div>';
		die($html);
	}
	function shop_extra_save()
	{
		$this->is_admin();
		print_r( $_POST );
		$id = intval(v('id'));
		if( $id < 1 )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'错误的参数\');</SCRIPT>');
		}
		$label = z(v('label'));
		if( !$label )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'请填写字段名\');</SCRIPT>');
		}
		$type = z(v('type'));
		$all_types = array('line'  , 'radio' , 'checkbox'  , 'dropdown' );
		if( !in_array( $type , $all_types ) )
		{
			die('<SCRIPT LANGUAGE="JavaScript">alert(\'错误的类型\');</SCRIPT>');
		}
		$type_values = v('type_values');
		$html = '<INPUT TYPE="text" NAME="extra['.$id.']" class="text">';
		if( $type != 'line' )
		{
			$tvalue = array();
			if( is_array( $type_values['name']  ) )
			{
				$i = 1;
				foreach( $type_values['name'] as $k => $v )
				{
					$name = z($v);
					$value = z($type_values['value'][$k]);
					if(  $v || $value )
					{
						$tvalue['name'][$i] = $name;
						$tvalue['value'][$i] = $value;
						$i++;
					}
				}
				if( !$tvalue )
				{
					die('<SCRIPT LANGUAGE="JavaScript">alert(\'请填写选项值\');</SCRIPT>');
				}
				$data['tvalue'] = $tvalue;
				$json_code['tvalue'] = $tvalue;
			}
		}
		$data['id'] = $id;
		$data['label'] = $label;
		$data['type'] = $type;
		$json_code['label'] = $label;
		$json_code['type'] = $type;
		$json_code['field'] = base64_encode(serialize($data));
		die('<SCRIPT LANGUAGE="JavaScript">update_shop_extra_table('.$id.',\''.json_encode($json_code).'\');</SCRIPT>');
	}
	function save_shop_order( $action , $order )
	{
		$this->is_admin();
		$array = array( 'brands' , 'type' , 'cate');
		if( in_array( $action , $array ) )
		{
			$order = explode(',',$order);
			if( $order  )
			{
				$i = 1;
				foreach( $order as $id )
				{
					$id = intval( $id );
					lazy_run_sql("update `u2_shop_".$action."` set `orders` = '$i' where `id` = '$id' limit 1");
					$i++;
				}
			}
		}
	}
	function admin_shop_del($action , $id )
	{
		$this->is_admin();
		$array = array( 'brands' , 'type' );
		if( in_array( $action , $array ) )
		{
			$id = intval( $id );
			lazy_run_sql("delete from `u2_shop_".$action."` where `id` = '$id' limit 1");
		}
		if( $action == 'type' )
		{
			lazy_run_sql("delete from `u2_shop_items` where `type` = '$id'");
			
			$eid = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".intval($id)."' " );
			if( $eid )
			{
				lazy_run_sql("DROP TABLE `shop_extra_".intval($id)."`");
			}
		}
		if( $action == 'cate' )
		{
			$count = lazy_get_var("select count(*) from `u2_shop_cate` where `pid` = '$id' limit 1");
			if( $count )
			{
				die('此分类下有其他分类,不能删除此分类');
			}
			$count = lazy_get_var("select count(*) from `u2_shop_items` where `type` = '$id' limit 1");
			if( $count )
			{
				die('此分类下有商品,不能删除此分类');
			}
			lazy_run_sql("delete from `u2_shop_cate` where `id` = '$id' limit 1");
		}

	}
	function shop_cate_list( $id = NULL )
	{
		$this->is_admin();
		$id = intval( $id );
		$all_cate = lazy_get_data("select * from `u2_shop_cate` ", 'id');
		if( !$all_cate )
		{
			die('<div class="notice">暂无分类</div>');
		}
		foreach( $all_cate as $v )
		{
			$parent[$v['pid']][$v['id']] =  $v['orders'];
		}
		if( !isset( $parent[$id] ) )
		{
			$id = 0;
		}
		$ids = format_cate_order( 0 , $parent );
		foreach( $ids as $v )
		{
			$cates[$v['id']] = $all_cate[$v['id']];
			$cates[$v['id']]['root'] = $v['root'];
		}
		$html = NULL;
		$ul_start = false;
		if( $id == 0 )
		{
			$html ='<ul id="cate_'.$id.'" class="shop">';
			$ul_start = true;
			
		}
		$li_start = false;
		foreach( $cates as $v )
		{
			$blank = NULL;
			$style = NULL;
			$extra = NULL;
			if( $v['id'] != $id && isset( $parent[$v['id']] ) && $parent[$v['id']] )
			{
				$extra = '<a href="JavaScript:void(0)" onclick="shop_cate_list('.$v['id'].');" title="调整分类顺序"><img src="/static/images/plus.gif"></a>';
			}
			if(  $v['root'] )
			{
				$blank = '└' ;
				$style = ' style="padding-left:'.($v['root']*10 - 1).'px" ';
			}
			if( isset( $parent[$id][$v['id']] ) )
			{
				unset( $parent[$id][$v['id']] );
				if( $li_start)
				{
					$li_start = true;
					$html .= '</li>';
				}
				$html .='<li id="cate_id_'.$v['id'].'"><div '.$style.'><span class="r"><a href="JavaScript:void(0)" onclick="admin_shop_del(\'cate\','.$v['id'].')"><img src="/static/images/cross.gif"></a></span><img src="/static/images/movearrow.gif" class="m" /><a href="/admin/shop/modify/cate/'.$v['id'].'">'.$v['cate_desc'].'</a>'.$extra .'<br clear="all"/></div>';
			}
			else
			{
				$html .='<div '.$style.'><span class="r"><a href="JavaScript:void(0)" onclick="admin_shop_del(\'cate\','.$v['id'].')"><img src="/static/images/cross.gif"></a></span>'.$blank.'<a href="/admin/shop/modify/cate/'.$v['id'].'">'.$v['cate_desc'].'</a>'.$extra .'<br clear="all"/></div>';
			}
			if( $id == $v['id'] )
			{
				$html .='<ul id="cate_'.$id.'" class="shop">';
				$ul_start = true;
				$li_start = false;
			}
			if( $ul_start == true && !$parent[$id] && $id != 0 )
			{
				$ul_start = false;
				$html .='</li></ul>';
			}
		}
		if( $id == 0 )
		{
			$start = false;
			$html .='</ul>';
		}
		$html .= '<script>new Sortables($(\'cate_'.$id.'\') , {handles:$(\'cate_'.$id.'\').getElementsByClassName(\'m\') ,  onComplete:function()
			{
				var order =( this.serialize( function(el)
					{ 
						return el.id.replace("cate_id_" , "" );
					} ));
				var url = \'/ajax/save_shop_order/cate/\'+order ;
				var myajax = new Ajax(url,
				{
					data:foodata,
					method:\'post\' ,
					evalScripts:true,
					onComplete:function()
					{ 
					}
				}).request();
			}}
			);
			</script>';
		echo $html;
	}
	function load_shop_brand( $tid , $bid )
	{
		$this->is_admin();
		$tid = intval( $tid );
		$bid = intval( $bid );
		$type_info = lazy_get_line("select * from `u2_shop_type` where `id` = '$tid' limit 1 ");
		if( !$type_info || $tid == 0 )
		{
			$brands = lazy_get_data("select * from `u2_shop_brands` order by `orders` asc ");
		}
		else
		{
			$extra = unserialize( $type_info['extra'] );
			$bids = $extra['brands'];
			if( !$bids )
			{
				$brands = lazy_get_data("select * from `u2_shop_brands` order by `orders` asc ");
			}
			else
			{
				$brands = lazy_get_data("select * from `u2_shop_brands` where `id` IN(".join(',',$bids).") order by `orders` asc ");
			}
		}
		if( !$brands )
		{
			die('无可用的品牌');
		}
		$html = '<SELECT NAME="brands" id="brands">';
		foreach( $brands as $v )
		{
			if( $v['id'] == $bid )
				$html .= '<OPTION VALUE="'.$v['id'].'" SELECTED>'.$v['name'];
			else
				$html .= '<OPTION VALUE="'.$v['id'].'">'.$v['name'];
		}
		$html .= '</SELECT>';
		
		die( $html );
	}
	function shop_item_tag_list( $id )
	{
		$this->is_admin();
		$id = intval( $id );
		$tags = lazy_get_data("select * from `u2_shop_tags` where `id` IN( select `tid` from `u2_shop_relate_tags` where `cid` = '$id' ) ");
		$html = NULL;
		if( $tags )
		{
			$html = '<b>这篇文章上的标签</b><br/>';
			foreach( $tags as $v )
			{
				$tag[] = '<a href="JavaScript:void(0)" onclick="del_shop_item_tag( '.$id.' , '.$v['id'].' )"><img src="/static/images/cross.gif"></a>'.$v['tag'];
			}
			$html .= join('&nbsp;&nbsp;',$tag);
		}
		die( $html );
	}
	function del_shop_item_tag( $cid , $tid )
	{
		$this->is_admin();
		$cid = intval( $cid );
		$tid = intval( $tid );
		lazy_get_data("delete from `u2_shop_relate_tags` where `cid` = '$cid' and `tid` = '$tid' ");
	}
	function add_shop_item_tag( $cid  )
	{
		$this->is_admin();
		$cid = intval( $cid );
		$tags = z( v('tags') );
		if( $cid > 0 && $tags )
		{
			$add_tags = array();
			$tags = explode(' ',$tags);
			foreach( $tags as $v )
			{
				$v = trim( addslashes($v) );
				if( $v )
					$add_tags[] = $v;
			}
			if( $add_tags )
			{
				$ready = lazy_get_data("select * from `u2_shop_tags` where `tag` IN('".join("','",$add_tags)."') " ,'tag');
				foreach( $add_tags as $v )
				{
					if( isset( $ready[$v] ) )
					{
						lazy_run_sql( "replace into `u2_shop_relate_tags` ( `cid` , `tid` , `is_active` )values('$cid','{$ready[$v]['id']}',0) " );
					}
					else
					{
						lazy_run_sql("replace into `u2_shop_tags` (  `tag` )values('$v') ");
						$tid = lazy_last_id();
						lazy_run_sql( "replace into `u2_shop_relate_tags` ( `cid` , `tid` , `is_active` )values('$cid','$tid',0) " );
					}
				}
			}
			
		}
	}
	function shop_modify_reply( $id = NULL )
	{
		if( !is_login() )
		{
			die('<center>请登陆后操作<a href="JavaScript:void(0)" onclick="cancel_shop_modify()">返回</a></center>');
		}
		$id = intval( $id );
		$line = lazy_get_line("select * from `u2_shop_replys` where `id` = '$id' limit 1 ");
		if( !$line )
		{
			die('<center>错误的参数<a href="JavaScript:void(0)" onclick="cancel_shop_modify()">返回</a></center>');
		}
		if( $line['uid'] != format_uid() || $line['ruid'] > 0 )
		{
			die('<center>你没有权限进行此操作<a href="JavaScript:void(0)" onclick="cancel_shop_modify()">返回</a></center>');
		}
		$html = '<form action="/ajax/shop_update_reply/'.$id.'" method="post" id="reply_ajax_form"><TEXTAREA NAME="modify_info" id="modify_info"  wrap="hard" style="width:600px;height:60px;overflow:hidden">'.$line['info'].'</TEXTAREA><br/><br/>
		<INPUT TYPE="button" class="button" value=" 提交修改 " onclick="ajax_post_shop_reply('.$id.')">&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="button" class="button" value=" 取消修改 " onclick="cancel_shop_modify()"></form>';
		die( $html );

	}
	function shop_update_reply( $id = NULL )
	{
		if( !is_login() )
		{
			die('请登陆后操作');
		}
		$info = trim( strip_tags( v('modify_info') ) );
		if( $info == NULL )
		{
			die('请填写留言内容');
		}
		$line = lazy_get_line("select * from `u2_shop_replys` where `id` = '$id' limit 1 ");
		if( !$line )
		{
			die('错误的参数');
		}
		if( $line['uid'] != format_uid() || $line['ruid'] > 0 )
		{
			die('你没有权限进行此操作');
		}
		lazy_run_sql("update `u2_shop_replys` set `info` = ".s($info)." where `id` = '$id' limit 1");
	}
	function shop_show_rreply( $id )
	{
		if( !is_admin() )
		{
			die('<center>你没有权限进行此操作<a href="JavaScript:void(0)" onclick="cancel_rreply_div()">返回</a></center>');
		}
		$id = intval( $id );
		$line = lazy_get_line("select * from `u2_shop_replys` where `id` = '$id' limit 1 ");
		if( !$line )
		{
			die('<center>错误的参数<a href="JavaScript:void(0)" onclick="cancel_rreply_div()">返回</a></center>');
		}
		$html = '<br clear="all"/><form action="/ajax/shop_update_rreply/'.$id.'" method="post" id="rreply_ajax_form"><TEXTAREA NAME="rinfo" id="rinfo"  wrap="hard" style="width:600px;height:60px;overflow:hidden">'.$line['rinfo'].'</TEXTAREA><br/><br/>
		<INPUT TYPE="button" class="button" value=" 提交回应 " onclick="ajax_post_shop_rreply('.$id.')">&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="button" class="button" value=" 取消 " onclick="cancel_rreply_div()"></form>';
		die( $html );

	}
	function shop_update_rreply( $id )
	{
		if( !is_admin() )
		{
			die("<script>alert('你没有权限进行此操作')</script>");
		}
		$id = intval( $id );
		$line = lazy_get_line("select * from `u2_shop_replys` where `id` = '$id' limit 1 ");
		if( !$line )
		{
			die("<script>alert('错误的参数')</script>");
		}
		$rinfo = trim( strip_tags( v('rinfo') ) );
		if( $rinfo == NULL )
		{
			die("<script>alert('请填写留言内容')</script>");
		}
		lazy_run_sql("update `u2_shop_replys` set `rinfo` = ".s($rinfo)." ,`ruid`='".format_uid()."' , `rtime`='".date("Y-m-d H:i:s")."' where `id` = '$id'  limit 1");
		die("<script>$('rreply_item_".$id."').setHTML('<br/><a href=\"/user/space/".format_uid()."\" target=\"_blank\" style=\"color:orange\">"._sess('u2_nickname')."</a>回复: ".str_replace("\n","",nl2br(addslashes($rinfo)))."<a href=\"JavaScript:void(0)\" onclick=\"show_shop_rreply_div(".$id.",this)\"><img src=\"/static/images/updates.gif\" alt=\"修改\"/></a>')</script>");
	}
	function shop_del_reply( $id )
	{
		if( !is_login() )
		{
			die('请登陆后操作');
		}
		$id = intval( $id );
		$line = lazy_get_line("select * from `u2_shop_replys` where `id` = '$id' limit 1 ");
		if( !$line )
		{
			die('错误的参数');
		}
		if( is_admin() || ( $line['uid'] == format_uid() && intval($line['ruid']) == 0  ) )
		{
			lazy_run_sql("delete from `u2_shop_replys` where `id` = '$id' limit 1");
		}
		else
		{
			die('您没有权限进行此操作');
		}
	}
	function update_shop_wishlist( $cid , $type = 2 )
	{
		if( !is_login() )
		{
			die( _text('system_plz_login') );
		}
		$cid = intval( $cid );
		if( $cid  < 1 )
		{
			die( _text('system_error_id') );
		}
		$type = intval( $type ) == 2 ?2:1;
		$uid = format_uid();
		$time = date("Y-m-d H:i:s");
		lazy_run_sql( "replace into `u2_shop_wishlist` ( `uid` , `cid` , `type` , `time` )values( '$uid' , '$cid' , '$type' , '$time' )" );
		die();
	}
}


?>