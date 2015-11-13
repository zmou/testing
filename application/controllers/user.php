<?php

class user extends Controller {

	function user()
	{
		parent::Controller();
		$this->load->model('User_model', 'user', TRUE);

	}
	
	function login_check()
	{
		if( !is_login() )
			{
				$this->login( NULL ,method() );
				die();
			}
	}
	
	function guide( $id )
	{
		if( $id < 1 ) $id = 1;
		
		$a = array();
		$a['u2_first_time'] = $id;
		switch( $id )
		{
			case '2':
				$f = '/app/native/ihome/cloth';
				break;
				
			case '3':
				$f = '/app/native/ishop';
				break;
				
			case '4':
				$f = '/app/native/ishop/toggery';
				break;
				
			case '5':
				$f = '/app/native/ibank/convert';
				break;
			
			case '6':
				$f = '/app/native/ibank/index';
				break;	
			
			case '7':
				$f = '/app/native/iduoduo/add';
				break;	
				
			case '8':
				$f = '/app/native/imap';
				break;	
				
			case '9':
				$a['u2_first_time'] = NULL;
				$f = '/user/miniblog/';
				break;
				
		}
		
		set_sess( $a );
		header( 'Location: ' . $f );
	}
	
	function newone()
	{
		echo _sess('u2_first_time');
		echo _sess('u2_inviter_uid');
		echo _sess('u2_inviter_nickname');
		//set_sess( 'u2_first_time' , '1' );
		//info_page( 'ok' );
		
		// add money 
		$sql = "INSERT INTO `app_ibank_account` ( `uid` , `g_count` , `gold_count` ) VALUES ( '" . format_uid() . "' , '" . intval(c('user_init_silver')) . "' , '" . intval(c('user_init_gold')) . "' ) ";
		lazy_run_sql( $sql );
		
		// add cloth
		$sql = "INSERT INTO `app_ihome_shop` ( `uid` , `item_id` ) VALUES ( '" . format_uid() . "' , '844'  ) , ( '" . format_uid() . "' , '879' )";
		lazy_run_sql( $sql );
		
		echo mysql_error();
		info_page('初始化完毕');
		
		
		
	}
	
	function index()
	{
		header('Location: /user/setting');
	}
	
	function resetpass($action = NULL)
	{
		$this->login_check();

		$data = NULL;

		if($action == 'save')
		{
			if(v('oldpass') == NULL || v('newpass1') == NULL || v('newpass2') == NULL  )
			{
				info_page( _text('user_resetpass_is_null') );
				
				return;
			}
			if(v('newpass1') != v('newpass2'))
			{
				info_page( _text('user_resetpass_not_same') );
				
				return;
			}
			$useinfo = $this->user->load_user_information_by_uid();
			if($useinfo['u2_password'] !== md5(v('oldpass')) )
			{
				info_page( _text('user_error_resetpass_psw') );

				return;
			}
			$this->user->resetpass_by_uid(v('newpass1'));

			
			info_page( _text('user_resetpass_success') );
			
			return;
		}
		$this->view('resetpass',$data);
	}
	
	function setting($action = NULL)
	{
		$this->login_check();

		$data = NULL;

		if($action == 'save')
		{
			 if($this->user->save_user_profile())
			{
				info_page( _text('user_change_setting_success') , '/user/setting');
				return;
			}
			else
			{
				info_page( _text('user_change_setting_false'));
			}
		}
		
		$data = $this->user->load_user_information_by_uid();

		$this->view('setting',$data);
	}
	
	function resetpic($name)
	{
		$this->login_check();

		if(intval($name) < 1)
		{
			info_page(_text('system_error_id'));
			return false;
		}
		@copy(ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_small.gif',get_user_icon_path('small') );
		@copy(ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_normal.gif',get_user_icon_path() );
		@copy(ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_big.gif' ,get_user_icon_path('big'));
		$title = '<a href="/user/space/' . format_uid() . '">' . _sess('u2_nickname') . '</a>更换了新头像';
		send_to_feed( format_uid() , 'system_user' , $title , NULL ,  show_user_icon() );
		info_page(_text('user_resetpic_success') , '/user/uploadpic');
		return true;
	}
	
	function del_user_pic( $id )
	{
		$this->login_check();
		$name = $this->user->del_user_pic($id);
		if($name)
		{
			@unlink( ROOT.'static/data/hash/user_icon/' . myhash() .$name.'.gif' );
			@unlink( ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_small.gif' );
			@unlink( ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_normal.gif' );
			@unlink( ROOT.'static/data/hash/user_icon/' . myhash() .$name.'_big.gif' );
		}
		header('Location: /user/uploadpic');
	}
	
	function uploadpic($action = NULL)
	{
		$this->login_check();

		$data = NULL;

		if($action == 'save')
		{
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
				info_page( '<a href="/user/space">' . _text('user_uploadpic_success') . '</a>' );
				return;
			}
				info_page( _text('user_error_uploadpic') );
				return;
		}

		$data['pics'] = $this->user->load_user_photos_by_uid();

		$this->view('uploadpic',$data);
	}
	
	function miniblog( $page = NULL , $friend_only = NULL)
	{
		if( $friend_only == NULL && get_cookie('minifeed_view') )
		{
			$friend_only = get_cookie('minifeed_view');
		}
		elseif(  $friend_only != NULL )
		{
			set_cookie('minifeed_view' , $friend_only , 60*60*24*365  );
		}
		
		$this->login_check();
		
		$fids = $this->user->get_frinds_ids_by_uid();
		
		//end_app_shell;

		$data['ofriends'] = $this->user->get_online_by_ids($fids,'online');
	
		$data['friend_only'] = $friend_only == 'friend'?'friend':'all';
		
		// app info - bank
		//$data['bank'] = lazy_get_line( "SELECT * FROM `app_ibank_account` WHERE `uid` = '" . format_uid() . "' LIMIT 1" ) ;
		// end app info -bank
		
		$data['omembers'] = $this->user->get_online_peoples(24);
		
		$page = intval( $page );

		$page = $page < 1 ? 1 : $page ;
		
		$start = ($page - 1)*10;

		$data['feeds'] = $this->user->load_friends_minifeed($data['friend_only'] , NULL , $start );

		$all = get_count();

		$page_all = ceil( $all / 10 );
		
		$base = '/user/miniblog';

		$data['pager'] = get_pager(  $page , $page_all , $base , $friend_only );

		$this->view('miniblog',$data);
	}
	
	function blogset($action = NULL)
	{
		$this->login_check();
		if($action == 'save')
		{
			$this->user->save_blogset(v('type'));
			info_page(_text('user_miniblog_setting_success'),'/user/miniblog');
			die();
		}
		$data['types'] = _sess('u2_miniblog') == NULL?array():unserialize( _sess('u2_miniblog') );

		$this->view('blogset',$data);
	}
	
	function login($action = NULL ,$method = NULL )
	{

		$data = NULL;
		
		if($action == 'save')
		{
			
			//print_r( $_REQUEST );
			if(v('email') == NULL || v('psw') == NULL)
			{
				info_page( _text('user_error_login_no_null') );
				
				return;
			}
			
			if( $this->user->login_confirm(v('email'),v('psw')) )
			{
				$title = '<a href="/user/space/' . format_uid() . '">' . _sess('u2_nickname') . '</a>上线了';
				send_to_feed( format_uid() , 'system_user' , $title ,NULL , NULL , format_uid().'-'.date("Y-m-d").'-login' );
				if( strpos( v('email') ,'@') === false  )
				{
					header('Location: /extra' );
					die();
				}
				
				$this->load->helper('cookie');
				set_cookie('u2_email', v('email') , time()+60*60*24*365);
				
				if( strlen( v('psw') < 20 ) ) 
					$psw = md5(v('psw'));
				else
					$psw = v('psw');
				
				set_cookie('u2_password_md5', $psw , time()+60*60*24*365);
				
				if(v('method'))
				{
					header('Location: /user/'.v('method') );
				}
				else
				{
					header('Location: /');
				}
			
			}
			else
			{
				$this->load->helper('cookie');
				delete_cookie('u2_email');
				delete_cookie('u2_password_md5');
				info_page( _text('user_error_login_no_user') );
				return;
			}
		}
		$data['method'] = $method;
		
		$this->view('login',$data);
	}
	
	
	function register($action = NULL , $extra = NULL )
	{
		if($action == 'save')
		{
			$psw = z(v('psw'));
			$email = z(v('email'));
			$nickname = z(v('nickname'));
			$invitecode = z(v('invitecode'));
			if($psw == NULL || $email == NULL || $nickname == NULL)
			{
				info_page( _text('user_error_register_is_null') );

				return;
			}
			$invuid = intval( v('invuid') );
			if( c('invite_active') && $invitecode )
			{
				if(!($invite = $this->user->check_invite_code($invitecode) ) )
				{
					info_page( _text('invite_code_error') );
				}
				$invuid = $invite['u2_uid'];
			}elseif( !c('register_is_open') )
			{
				info_page( _text('system_need_invite_code') );
				// check register is open
			}
			if(! $this->user->register_save( $email , $nickname , $psw ) )
			{	
				info_page( _text('user_error_register_is_same') );

				return false;
			}

			if( c('invite_active') && $invitecode )
			{
				$this->user->marked_invite_code( $invite['id']) ;
			}
			$user_info = $this->user->get_user_by_email( $email );
			$title = '<a href="/user/space/' . $user_info['id'] . '">' . $user_info['u2_nickname'] . '</a>加入了' . c('site_name');
			send_to_feed( $user_info['id'] , 'system_user' , $title );
			if($invuid && ( $olduser = $this->user->load_user_information_by_uid($invuid)) )
			{
				$this->user->add_friend( $invuid , $user_info['id'] );
				$title = '<a href="/user/space/' . $user_info['id'] . '">'.$user_info['u2_nickname'] . '</a>和<a href="/user/space/' . $olduser['id'] . '">' . $olduser['u2_nickname'] . '</a>成为好友了';
				send_to_feed( $user_info['id'] , 'system_user' , $title );
			}

			$this->user->login_confirm( $email , $psw );


			header('Location: /');
			
			
		}
		if(  !c('register_is_open') && !c('invite_active')  )
		{
			info_page( _text('system_register_is_closed') ,'/');
		}
		$data = array();

		$data['icode'] = $action == 'invite'?$extra:NULL;
		$data['invuid'] = $action == 'invuid'?$extra:NULL;
	
		$this->view('register',$data);
	}
	
	function welcome()
	{	
		$this->login_check();

		$data = NULL;
		
		$this->view('welcome',$data);
	}
	
	function logout()
	{
		$this->load->library('session');
		$this->session->sess_destroy();
		
		$this->load->helper('cookie');
		delete_cookie('u2_email');
		delete_cookie('u2_password_md5');

		header('Location: /riki/');
	}
	
	function view($page,$data)
	{
		$data['ci_top_title'] = _text('user_'.$page.'_title');
		
		$data['page_name'] = $page;

		layout($data);
	}
	
	function friend($page = NULL ,$search = NULL ,$user = NULL )
	{
		$this->login_check();
			
		$data['search'] = $search;
		
		$uid = isset($user['id'])?$user['id']:NULL;
	
		$fids = $this->user->get_frinds_ids_by_uid($uid);
		$page = intval($page) < 1 ?1:intval($page);
		$limit = intval(c('user_per_page'));
		$start = ($page-1)*$limit;
		$data['friends'] = $this->user->get_users_by_ids($fids,$search,$start);
		$all =  get_count();
		$base = '/user/friend';
		$page_all = ceil( $all /$limit);
		$data['pager'] = get_pager(  $page , $page_all , $base , $search );
		if( is_array($user) )
		{
			$data['nickname'] = $user['u2_nickname'];
			$data['see'] = 1;
		}
		else
		{
			$data['see'] = NULL;
		}
		$data['fonline'] = $this->user->get_online_by_ids($fids,'online');

		$this->view('friend',$data);
	}
	
	function seefrinds($uid)
	{
		$user = $this->user->load_user_information_by_uid($uid);
		$this->friend( 1  , NULL ,$user);
	}
	
	function frienddo($action , $fid)
	{
		$this->login_check();

		$this->user->check_uid( intval($fid) );
		$return = $this->user->friend_doaction($action , $fid);
		if($return)
		{
			info_page( _text('user_friend_add_'.$return ) );
		}
		else
		{		
			header('Location: /user/friend/');
		}

	}
	
	function notifications( $page = NULL )
	{
		$this->login_check();
		
		$uid = format_uid();

		$page = intval($page) < 1 ?1:intval($page);
		
		$limit = '10';

		$start = ($page-1)*$limit;

		$base = '/user/notifications/';

		$mark = $page == 1 ?true : false ;

		$data = array();
		
		$fids = $this->user->get_frinds_ids_by_uid();
		$data['friends'] = $this->user->get_online_by_ids( $fids );
		
		$data['notice'] = $this->user->get_notice_by_uid( $uid , $start , $limit , $mark );

		$page_all = ceil( get_count( false ) /$limit);


		$data['pager'] = get_pager(  $page , $page_all , $base );
		
		$this->view('notifications',$data);
	}
	
	function friendrequest( $uid = NULL )
	{
		$this->login_check();
		
		$uid = format_uid( $uid );
		$data = array();
		$fids = $this->user->get_frinds_ids_by_uid();
		$data['friends'] = $this->user->get_online_by_ids( $fids );
		$data['request'] = $this->user->get_friend_request( $uid );
		$data['users'] = $this->user->get_user_info_by_array( $data['request'] , 'u2_uid1' );
		
		
		
		
		$this->view('friendrequest',$data);
	}
	
	function message($box = 'inbox' , $page = 1 )
	{
		$this->login_check();

		$page = intval($page) < 1 ?1:intval($page);
		
		$limit = '5';

		$start = ($page-1)*$limit;

		$base = '/user/message/'.$box;

		$page_all = ceil( $this->user->get_user_message_num_by_uid($box) /$limit);

		$fids = $this->user->get_frinds_ids_by_uid();

		$friends = $this->user->get_users_by_ids($fids);

		$data['friends'] = $this->user->get_online_by_ids( $fids );

		$data['messages'] = $this->user->load_user_message_by_uid($box , $start , $limit );

		$data['pager'] = get_pager(  $page , $page_all , $base );

		$data['box'] = $box;
		
		$this->view('message',$data);
	}
	
	function pmsave()
	{
		$this->login_check();

		$sid = intval( v('sid') );
		$sname = z(v('sname'));
		
		$title = v('title') == NULL ?_text('user_message_send_no_title'):v('title') ;
		$title = z( $title );
		
		$info = n( v('info') );
		
		if( !$sid ||  !$sname )
		{
			info_page(_text('system_error_id'));
			return;
		}



		if(!$info)
		{
			info_page(_text('user_message_send_is_null'));
			return;
		}

		$this->user->save_message( $title , $info , $sid , $sname );

		info_page( _text('user_message_send_success'),'/user/message/inbox/');

	}
	
	function sendmessage($sid = NULL )
	{
		$this->login_check();

		if(intval($sid) < 1)
		{
			info_page(_text('system_error_id'));
			return;
		}
		if( !is_login() )
		{
			$this->login( NULL ,method());

			return false;
		}
		
		$data['s_user'] = $this->user->load_user_information_by_uid($sid);

		if( !is_array($data['s_user']) )
		{
			info_page(_text('system_error_id'));
			return;
		}
		
		$fids = $this->user->get_frinds_ids_by_uid();

		$friends = $this->user->get_users_by_ids($fids);

		$data['friends'] = $this->user->get_online_by_ids( $fids );
		
		$this->view('sendmessage',$data);
	}

	function space($uid = NULL)
	{
		if( !c('can_guest_view_space') )
		{
			$this->login_check();
		}
		$user = $this->user->load_user_information_by_uid($uid);
		if( !$user )
		{
			info_page( _text('user_error_login_bad_uid') );
			die();
		}
		if( is_login() )
		{
			$vid = format_uid();
			$uid = $user['id'];
			if($uid != $vid )
			{
				$this->user->save_view_visitor($uid,$vid);
			}
		}

		$this->user->save_space_hit($user['id']);
		$data['user'] = $this->user->load_user_information_by_uid($user['id']);
		$data['minifeeds'] = $this->user->get_user_minifeeds_by_uid($user['id']);
		$fids = $this->user->get_frinds_ids_by_uid($user['id']);
		$data['friends'] = $this->user->get_last_user_by($fids);
		$vids =  $this->user->get_view_vistor($user['id']);
		$data['vistors'] = $this->user->get_users_by_ids($vids,'','0');
		
		if( $user['id'] == format_uid() )
		{
			$data['self'] = true;
		}
		else
		{
			$data['self'] = false;
		}
		
		$data['wall'] = $this->user->wall_get_by_uid( $uid );
		$this->view('space',$data);
		
	}

	function ulist($action = 'all',$page = 1,$uid = NULL)
	{
		$page = intval($page) < 1?1:intval($page);

		$start = intval( c('user_per_page') )*($page-1);
	
		if($action == 'online')
		{
			$data['users']  =$this->user->get_online_peoples( intval( c('user_per_page') ) , $start);
			$all = get_count( false );
		}
		elseif($action == 'friends')
		{
			$fid = $this->user->get_frinds_ids_by_uid($uid);
			$all = count($fid);
			$data['users']  =$this->user->get_users_by_ids($fid,'',$start);
		}
		elseif($action == 'onlinefriends')
		{
			$fid = $this->user->get_frinds_ids_by_uid($uid);
			$ofids = $this->user->get_online_user_ids_by_ids($fid);
			$all = count($ofids);
			$data['users']  =$this->user->get_users_by_ids($ofids,'',$start);
		}
		elseif($action == 'vistors')
		{
			$vids = $this->user->get_view_vistor($uid);
			$all = count($vids);
			$data['users']  =$this->user->get_users_by_ids($vids,'',$start);
		}
		else
		{
			$data['users']  =$this->user->get_users($start);
			$all = get_count();
		}
		$page_all = ceil($all/ intval(c('user_per_page')));
		$url_base = '/user/ulist/'.$action;
		$data['action'] = $action;
		$data['pager'] = get_pager(  $page , $page_all , $url_base ,$uid );
		$this->view('ulist',$data);
	
	}
	function search( $page = NULL ,$searchtext = NULL )
	{
		$searchtext = urldecode($searchtext);
		$page = intval($page) < 1?1:intval($page);

		$start = intval( c('user_per_page') )*($page-1);
		
		$data['users']  =$this->user->get_users($start , $searchtext );

		$all = get_count();
		
		$page_all = ceil($all/ intval(c('user_per_page')));

		$url_base = '/user/search';

		$data['action'] = 'search';

		$data['searchtext'] = $searchtext;

		$data['pager'] = get_pager(  $page , $page_all , $url_base , $searchtext );

		$this->view('ulist',$data);
	}
	
	function wall()
	{
		$uid = intval( v('uid') ) ;
		$content = n( v('content') );
		
		// check if is empty
		if( $uid > 0 && $content != '' )
		{
			$this->user->wall_save( $uid , format_uid() , $content );

			send_to_notice( $uid , 'system_guestbook' , '<a href="/user/space/' . format_uid() . '">' . _sess('u2_nickname') . '</a>给你留言了 ',
			'<img src="/static/images/quote_left.gif" />&nbsp;<a href="/user/space#">' . word_substr( $content , 15 ) .'</a>&nbsp;<img src="/static/images/quote_right.gif" />');
			
			$uname = $this->user->get_nickname_by_uid( $uid );
			$title = '<a href="/user/space/' . format_uid() . '">'._sess('u2_nickname').'</a>给<a href="/user/space/' . $uid . '#wall">' . $uname . '</a>留言了';
			$desp = word_substr( $content , 15 );
			
			send_to_feed( format_uid() , 'system_miniblog' , $title ,$desp  );

		}
		
		header('Location: /user/space/' . $uid );
		
	}
	function forgetpass( $action = NULL )
	{
		if($action == 'save')
		{
			$email = v('email');
			if( !preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $email ) )
			{
				info_page( _text('user_forgetpass_email_false'));
			}
			$user = $this->user->get_user_info_by_email( $email );
			 if( $user )
			{
				$pincode = $this->user->new_pincode($user['id']);
				$link = 'http://'.$_SERVER['HTTP_HOST'].'/user/newpass/'.$user['id'].'/'.$pincode;
				$mail_title = _text('system_reset_pass_mail_title', $user['u2_nickname'] , c('site_name') );
				$mail_info = _text( 'system_reset_pass_mail_info',  $user['u2_nickname'] , $link , c('site_name') , c('site_name'), c('site_name') , c('site_name').' '.c('site_domain')  );

				if( !sendmail( $email,$mail_title,$mail_info ) )
				{
					info_page( _text('system_mail_unusable') );
				}
				info_page( _text( 'user_forgetpass_success',$email,c('site_name'),c('site_name') ));
				return;
			}
			else
			{
				info_page( _text('user_forgetpass_user_false'));
			}
		}
		
		$data['html'] = '<form action="/user/forgetpass/save" method="post"><p class="item"><label>'._text('user_register_email').':</label>&nbsp;<input type="text" class="text" name="email" value="" ></p><p class="act"><input type="submit" value="'._text('user_forgetpass_title').'" class="button"></p></form>';

		$this->view('forgetpass',$data);
	}
	function newpass( $uid = NULL , $pincode  = NULL )
	{
		if( !$this->user->check_pincode( $uid , $pincode ) )
		{
			info_page( _text('user_forgetpass_check_false' , '<a href="/user/forgetpass">','</a>' ) );
		}
		$data['html'] = '<form action="/user/newpasssave/'.$uid.'/'.$pincode.'" method="post"><p class="item"><label>'._text('user_resetpass_newpass').':</label>&nbsp;<input type="password" class="text" name="psw1" value="" ></p><p class="item"><label>'._text('user_resetpass_newpass_again').':</label>&nbsp;<input type="password" class="text" name="psw2" value="" ></p><p class="act"><input type="submit" value="'._text('user_forgetpass_title').'" class="button"></p></form>';

		$this->view('forgetpass',$data);
	}
	function newpasssave($uid = NULL , $pincode  = NULL)
	{
		if( !$this->user->check_pincode( $uid , $pincode ) )
		{
			info_page( _text('user_forgetpass_check_false' , '<a href="/user/forgetpass">','</a>' ) );
		}
		$psw1 = v('psw1');
		$psw2 = v('psw2');
		if( !$psw1 )
		{
			info_page( _text('user_resetpass_is_null') );
		}
		if( $psw1 != $psw2 )
		{
			info_page( _text('user_resetpass_not_same') );
		}
		$this->user->resetpass_by_uid($psw1,$uid);
		$pincode = $this->user->new_pincode($uid);
		$user = $this->user->load_user_information_by_uid($uid);
		set_sess($user);
		info_page( _text('user_resetpass_success'),'/',_text('system_back_to_index') );
	}
	
}

?>