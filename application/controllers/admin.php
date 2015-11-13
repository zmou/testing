<?php

class admin extends Controller {

	function admin()
	{
		parent::Controller();
		$this->lang->load('admin');
		$this->load->model('Admin_model', 'admin', TRUE);
	
	}
	function index()
	{
		
		$this->check_admin();
		$this->load->helper('app');
		
		$data['pm_count'] = $this->admin->get_pm_count_today();
		$data['avg_online'] = $this->admin->get_ave_online();
		$data['ip_count'] = $this->admin->get_ip_count();
		$data['feed_count'] = $this->admin->get_feed_count_today();
		$data['new_num'] = $this->admin->get_new_members_num();
		$data['statistics'] = get_backstage_data();
		$this->view('cp',$data);
		
		
		
	}
	function check_admin()
	{
		if( _sess('u2_level') < 5 )
		{
			info_page( _text('admin_error_limits_rights'),'/user/login', _text('admin_info_relogin') );
			die();
		}
	}
	function view($page,$data)
	{
		if( !isset($data['ci_top_title']) || !$data['ci_top_title'] )
			$data['ci_top_title'] = _text('admin_'.$page.'_title');

		$data['page_name'] = 'admin_'.$page;

		layout($data,'admin');
	}
	function get_app_path()
	{
		$this->check_admin();
		
		$path = ROOT.'application/app/';

		$list = array();

		$apps = $this->admin->get_apps();
		foreach( glob( $path . '*') as $item )
		{
			if( is_dir( $item ))
			{
				if( file_exists($item.'/controller/config.php' ) )
				{
					include_once( $item.'/controller/config.php' );
					$app_config['more'] = file_exists($item.'/controller/index.php' )?1:0;
					$app_config['folder'] = str_replace($path,'',$item);
					if( isset( $apps[$app_config['id']] ) )
					{
						if( strtolower($app_config['folder']) == strtolower($apps[$app_config['id']]['u2_folder']) )
						{
							$app_config['installed'] = 1;
						}
						else
						{
							$app_config['installed'] = 2;
						}
					}
					else
					{
						$app_config['installed'] = 0 ;
					}
					$list[] = $app_config;

					unset($app_config);
				}
			}
		
		}
	return $list;
	}
	
	function app_settings( $folder = NULL )
	{
		check_admin();
		$set = NULL;
		$data['app'] = $this->admin->get_app_info_by_folder($folder);
		$data['config'] = NULL;
		$path = ROOT.'application/app/'.$folder.'/controller/';
		if( file_exists( $path.'admin_config.php' ) )
		{
			include_once( $path.'admin_config.php' );
			$data['config'] = $config;
			foreach( $config as $k => $v)
			{
				if( !is_array( $v ) )
					$set .= "set( '$k' , '$v' );"; 
			}
		}
		$data['set'] = $set;
		$data['folder'] = $folder;
		$this->view('app_settings',$data);
	}
	
	function appoper($folder = NULL)
	{
		$this->check_admin();

		if($folder == NULL )
		{
			info_page(_text('system_error_id'));
			
			die();
		}
		$path = ROOT.'application/app/'.$folder.'/';

		include_once( $path.'controller/config.php' );

		$action_key = $this->admin->app_is_install($app_config['id'] , $folder );

		$action = $action_key == 0 ?'install':'uninstall';

		if( file_exists( $path.'controller/operate.php' ) )
		{
			include_once( $path.'controller/operate.php' );
			if( isset($widgets) && is_array( $widgets ) )
			{
				$widgets = array_change_key_case($widgets);
			}
			if(is_array( $$action ) )
				$this->admin->run_sqls($$action);

			if($action_key)
			{
				if( isset( $uninstall['table'] ) )
				{
					$this->admin->drop_app_table($uninstall['table']);
				}
				if( isset( $app_config['id'] ) )
				{
					$this->admin->del_app_and_widget_by_aid($app_config['id']);
				}
				
				header('Location: /admin/applist');
				info_page( _text('admin_app_uninstall_success') ,'/admin/applist' );
				die();
			}
		}
		
		$app_config['folder'] = $folder;
		
		$widget = 0;
		
		if( file_exists( $path . 'widgets/' ) )
		{
			$list = glob( $path . 'widgets/*');

			if(is_array($list))
			{
				foreach($list as $item)
				{
					if( is_dir($item) )
					{
						if(file_exists ($item.'/default.tpl.html') )
						{
							$widget++;

							$widget_folder = $folder.'/'.str_replace($path,'',$item  );
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
							$this->admin->add_widget($config,$app_config['id'],$widget_folder);
						}

					}
				}
			}
		}

		$app_config['widget'] = $widget;	
		
		$this->admin->add_app($app_config);

	
		header('Location: /admin/applist');
		info_page(_text('admin_app_install_success'), '/admin/applist' );
		die();
		
		
	}
	
	function pages( $type = 1 )
	{
		$this->check_admin();
		$data['pages'] = $this->admin->get_pages();
		$data['system_pages'] = $this->admin->get_pages(1);
		$data['type'] = $type;
		$this->view('pages',$data);
		
	}
	
	function applist()
	{
		$this->check_admin();

		$data['list'] = $this->get_app_path();

	
		$this->view('applist',$data);
	}
	function admins($page = NULL ,$searchtext = NULL)
	{
		$this-> members($page,$searchtext, 1 );
	}
	function members($page = NULL ,$searchtext = NULL , $is_admin = NULL )
	{
		$this->check_admin();
		
		$page = intval($page) < 1 ?1:intval($page);
		$limit = $this->config->item('per_page');
		$start = ($page-1)*$limit;

		$data['is_admin'] = intval($is_admin);
		$data['members'] = $this->admin->load_members($start,$limit, $is_admin , $searchtext );
		$all = get_count();
		$page_all = ceil($all/$limit);
		$data['page'] = $page;
		if($is_admin)
		{
			$base = '/admin/admins';
		}
		else
		{
			$base = '/admin/members';
		}
		$data['pager'] = get_pager(  $page , $page_all , $base , $searchtext );
		$data['searchtext'] = urldecode($searchtext);
		
		
		$this->view('members',$data);
	}
	function changelevel()
	{
		$this->check_admin();
	
		if( v('clevel') == NULL || !v('ids') )
		{
			info_page( _text('system_error_id') );
			die();
		}
		if(  v('clevel') > _sess('u2_level') )
		{
			info_page( _text('admin_limits_rights'));
			die();
		}
		$this->admin->change_level( v('ids'), v('clevel')  );

		info_page( _text('admin_changelevel_success'),'/admin/members/');
	}
	function data( $id = NULL )
	{
		if(v('action') == 'save')
		{
			$this->admin->save_meta($_POST);
			info_page( _text('admin_data_save_success') ,'/admin/data/'.$id );
			die();
		}
		elseif((v('action') == 'modify'))
		{
			$this->admin->update_data_extra($_POST);
			info_page(_text('admin_data_extra_modify_success'),'/admin/data/'.$id);
			die();
		}
		$this->check_admin();
		$data['cid'] = $id;
		$data['extras'] = $this->admin->get_data_extra($id);
		$data['cates'] = load_cates();

		$this->view('data',$data);

	}
	function setting($action = NULL , $back = NULL)
	{
		if($action == 'save')
		{
			save_config($_POST);

			$back = $back == NULL? '/admin/setting' :'/admin/'.$back ;

			info_page( _text('admin_setting_success'),$back );
			
			return true;
		}
		$data = NULL;

		$this->view('setting',$data);

	}
	function invite()
	{
		$this->check_admin();
		$data = NULL;

		$this->view('invite',$data);
	}
	function payset()
	{
		$this->check_admin();
		$data = NULL;

		$this->view('payset',$data);
	}
	function mailset()
	{
		$this->check_admin();
		$data = NULL;

		$this->view('mailset',$data);
	}
	function contents($key = 'wait' , $page = NULL )
	{
		$this->check_admin();

		$page = intval($page) < 1 ?1:intval($page);
		$limit = $this->config->item('per_page');
		$start = ($page-1)*$limit;
		
		$nav = array('wait','done','accept','forbidden');
		if( !in_array( $key , $nav ) )
		{
			$key = 'wait';
		}
		$data['key'] = $key;

		$data['contents'] = $this->admin->load_manager($start, $limit, $key );

		$page_all = ceil(get_count()/$limit);
		if( $data['contents']  )
		{
			foreach( $data['contents'] as $v )
			{
				$uids[$v['u2_uid']] = $v['u2_uid'];
			}
			$data['names'] = get_name_by_uids( $uids );
		}
		$base = '/admin/contents/'.$key;
	
		$data['nav'] = $nav;

		$data['pager'] = get_pager(  $page , $page_all , $base );

		$this->view('contents',$data);
	}
	function cates($action = NULL)
	{	
		$this->check_admin();
		
		if($action == 'save')
		{
			$this->admin->cate_do_action( v('do_action') , v('cate_id') , v('cate_name') );
		}
		$data['cates'] = load_cates();

		$this->view('cates',$data);
	}

	function touser($id)
	{
		$this->check_admin();
		$user = $this->admin->get_user_by_uid($id);
		if(!$user)
		{
			info_page(_text('user_error_login_bad_uid'));
			die();
		}
		elseif($user['u2_level'] >= _sess('u2_level') )
		{
			info_page(_text('system_limit_rights'));
			die();
		}
		set_sess($user);
		header('Location: /');
	}
	function cards($page = NULL)
	{
		$this->check_admin();
		$page = intval($page) < 1 ?1:intval($page);
		$limit = $this->config->item('per_page');
		$start = ($page-1)*$limit;
		$page_all = ceil( $this->admin->get_recharge_card_num() /$limit);
		$base = '/admin/cards';
		$data['list'] =  $this->admin->get_recharge_card($start,$limit);
		
		$data['pager'] = get_pager(  $page , $page_all , $base );

		$this->view('cards',$data);
	}
	function makecard()
	{
		$this->check_admin();
		$data = NULL;
		$this->view('makecard',$data);
	}
	function savecard()
	{
		$this->check_admin();
		$number = intval(v('number'));
		if( $number < 1 )
		{
			info_page( _text('system_input_right_no') );
		}
		$this->admin->make_card( $number );

		info_page( _text('admin_savecard_success'),'/admin/cards' );
	}
	function appset( $folder )
	{
		$this->check_admin();
		$this->admin->save_appsetting($folder);
		$key = $_POST;
		unset( $key['u2_left_nav'] );

		save_app_config( $key , $folder  );

		info_page( _text('admin_appset_success'),'/admin/app_settings/'.$folder );
	}
	function plist( $page = NULL , $search = NULL )
	{
		$this->check_admin();
		$page = intval($page) < 1 ?1:intval($page);
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->admin->pro_list( $search ,$start,$limit );
		$all =  get_count();
		$base = '/admin/plist';
		$page_all = ceil( $all /$limit);
		$data['pager'] = get_pager(  $page , $page_all , $base , $search );
		$data['search'] = $search;
		$this->view('plist',$data);
	}
	function iticket( $page = NULL , $search = NULL )
	{
		$this->check_admin();
		$page = intval($page) < 1 ?1:intval($page);
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->admin->iticket_list( $search ,$start,$limit );
		$all =  get_count();
		$base = '/admin/iticket';
		$page_all = ceil( $all /$limit);
		$data['pager'] = get_pager(  $page , $page_all , $base , $search );
		$data['search'] = $search;
		$this->view('iticket',$data);
	}
	function snotice( $page = NULL )
	{
		$this->check_admin();
		$page = intval($page) < 1 ?1:intval($page);
		$limit = 10;
		$start = ($page-1)*$limit;
		$data['list'] = $this->admin->snotice_list( $start,$limit );
		$all =  get_count();
		$base = '/admin/snotice';
		$page_all = ceil( $all /$limit);
		$data['pager'] = get_pager(  $page , $page_all , $base);
		$data['ci_top_title'] = '系统通知';
		$this->view('snotice',$data);

		
	}
	function snotice_save()
	{
		$data['desp'] = v('desp');
		if( strip_tags( $data['desp']  ) == '' )
		{
			info_page('内容不能为空!');
		}
		$data['title'] = mb_substr( strip_tags( $data['desp'] ), 0,20,'utf-8' );
		$start_time = v('start_time');
		$end_time = v('end_time');
		if( !$start_time || !$end_time )
		{
			info_page('请输入有效时间!');
		}
		$data['start_time'] = date("Y-m-d", strtotime($start_time) );
		$data['end_time'] = date("Y-m-d", strtotime($end_time) );
		if( $data['start_time'] >=  $data['end_time'] || $data['start_time'] <  date("Y-m-d") )
		{
			info_page('请输入正确的有效时间!');
		}
		$this->admin->snotice_save( $data );
		header('Location: /admin/snotice');
	}
	function del_snotice( $id = NULL )
	{
		$this->check_admin();
		if( $id )
		{
			$id = intval($id);
			$this->admin->snotice_del( $id );
		}
	}
	function show_snotice( $id = NULL )
	{
		$this->check_admin();
		$id = intval($id);
		$line = $this->admin->get_snotice_by_id( $id );
		if( !$line )
		{
			info_page( _text('system_error_id') );
		}
		$data['line'] = $line;
		$data['ci_top_title'] = '系统通知';
		$this->view('show_snotice',$data);
	}
	function modify_snotice( $id = NULL )
	{
		$this->check_admin();
		$id = intval($id);
		$line = $this->admin->get_snotice_by_id( $id );
		if( !$line )
		{
			info_page( _text('system_error_id') );
		}
		$data['line'] = $line;
		$data['ci_top_title'] = '系统通知';
		$this->view('modify_snotice',$data);
	}
	function update_snotice( $id = NULL )
	{
		$this->check_admin();
		$id = intval($id);
		$data['desp'] = v('desp');
		if( !strip_tags( $data['desp'] ) )
		{
			info_page('内容不能为空!');
		}
		$data['title'] = mb_substr( strip_tags( $data['desp'] ), 0,20,'utf-8' );
		$start_time = v('start_time');
		$end_time = v('end_time');
		if( !$start_time || !$end_time )
		{
			info_page('请输入有效时间!');
		}
		$data['start_time'] = date("Y-m-d", strtotime($start_time) );
		$data['end_time'] = date("Y-m-d", strtotime($end_time) );
		if( $data['start_time'] >=  $data['end_time']  )
		{
			info_page('请输入正确的有效时间!');
		}
		$this->admin->update_snotice( $data , $id );
		info_page('修改系统通知成功!','/admin/show_snotice/'.$id , '点击查看内容' );

	}
	function update()
	{
		$this->check_admin();
		$max_id = $this->admin->get_update_max_id();
		$old_list = $this->admin->get_update_list();
		
		$list = $old_list?join('-',$old_list):NULL;

		$this->load->library('service');
		$this->service->add( 'max' ,$max_id );
		$this->service->add( 'version' ,c('version') );
		$this->service->add( 'list' , $list );
		$result = $this->service->result( 'getList' );
		if( $result == false )
		{
			$data['error'] = true;
			$data['notice'] = '网络错误';
		}
		elseif(  !isset($result['flag']) || $result['flag'] != 'ok' )
		{
			$data['error'] = true;
			$data['notice'] = '数据传输错误';
		}
		else
		{
			if( $result['error'] )
			{
				$data['error'] = true;
				$data['notice'] = $result['notice'];
			}
			else
			{
				$data['error'] = false;
				if( $result['rmax'] > $max_id )
				{
					$this->admin->update_max_update_id( $result['rmax'] );
				}
				$delids = array();
				$upids = array();
				$delids = array();
				if( $result['list'] )
				{
					foreach( $result['list'] as $k => $v )
					{
						if( isset( $old_list[$v['id']] ) )
						{
							unset( $old_list[$v['id']] );
						}
						if( $v['id'] > $max_id )
						{
							$upids[] = $v['id'];
						}
						
						if( file_exists( ROOT.$v['filepath']) && md5_file( ROOT . $v['filepath']) == $v['filemd5'] )
						{
							$result['list'][$k]['new'] = 1;
							$delids[] = $v['id'];
						}
					}
					if( $upids )
					{
						$this->admin->add_update_id( $upids );
					}
					$data['list'] = $result['list'];
				}
				$delids = array_merge( $delids ,  $old_list );
				if($delids)
				{
					$this->admin->del_update_id( $delids );
				}

			}
		}
		$this->view('update',$data);
	}
	function upgrade()
	{
		header('Location: /design/');
	}
	function record()
	{
		$this->check_admin();
		$tuid = intval(v('tuid'));
		$cid = intval(v('cid'));
		$karma = intval(v('karma'));
		$link = $this->input->server('HTTP_REFERER');
		$app = v('app');
		if( !$app || ! $cid || !$tuid || !$karma || !$link  )
		{
			info_page(_text('system_error_id'));
		}
		$names = get_name_by_uids( array( $tuid ) );
		if( !$names )
		{
			info_page(_text('system_error_id'));
		}
		$data['name'] = $names[$tuid]['u2_nickname'];
		$data['karma'] = $karma;
		$data['tuid'] = $tuid ;
		$data['app'] = $app;
		$data['cid'] = $cid;
		$data['link'] = $link;
		$this->view('record',$data);

	}
	function record_save()
	{
		$this->check_admin();
		$tuid = intval(v('tuid'));
		$cid = intval(v('cid'));
		$karma = intval(v('karma'));
		$link = v('link');
		$app = v('app');
		$type = v('type');
		if( !$app || ! $cid || !$tuid || !$karma || !$link  )
		{
			info_page(_text('system_error_id'));
		}
		$names = get_name_by_uids( array( $tuid ) );
		if( !$names )
		{
			info_page(_text('system_error_id'));
		}
		$data['admin_uid'] = format_uid();
		$data['uid'] = $tuid ;
		$data['app'] = $app;
		$data['cid'] = $cid;
		$data['url'] = $link;
		$data['reason'] = v('reason');
		$data['time'] = date("Y-m-d H:i:s");
		$money = $type == 'gold'?'金币':'银币';
		$data['action'] = $karma > 0 ?'增加'.$karma.$money :'减少'.abs($karma).$money;
		$type = v('type');
		$this->admin->do_karma($karma , $tuid , $type);
		$this->admin->save_record( $data );
		info_page("操作成功" , $link ,'返回');
	}
	function shop()
	{
		$this->check_admin();
		$args = func_get_args();
		$action = array_shift( $args );
		if( $action )
		{
			$sub_nav = array('item' => '商品管理' , 'cate' => '分类管理' , 'brand' => '品牌管理' , 'type' => '类型管理' );
			$nav = array_shift( $args );
			$fun = 'shop_'.$action."_".$nav;
			$data = $this->$fun( $args );
			if( isset( $sub_nav[$nav] ) )
			{
				foreach( $sub_nav as $k => $v )
				{
					if( $k == $nav )
					{
						$nav_links[] = '<a href="/admin/shop/add/'.$k.'"><b style="color:#555">'.$v.'</b></a>';
						$data['ci_top_title'] = $v;
					}
					else
					{
						$nav_links[] = '<a href="/admin/shop/add/'.$k.'">'.$v.'</a>';
					}
				}
				$data['nav_links'] = $nav_links;
			}
			$this->shop_view($action."_".$nav ,$data);
			return;
		}
		$data = array();
		$this->shop_view('cp',$data);
	}
	private function shop_view($page,$data)
	{
		if( $page == 'index' )
		{
			$page = 'cp';
		}
		if( !isset($data['ci_top_title']) || !$data['ci_top_title'] )
			$data['ci_top_title'] = _text('admin_shop_'.$page.'_title');

		$data['page_name'] = $page;

		layout($data,'shop');
	}
	private function shop_add_item( $args )
	{
		$data = array();
		$data['types'] = $this->admin->get_shop_types();
		$data['item'] =  $this->admin->get_shop_draft_item();
		return $data;
	}
	private function shop_modify_item( $args )
	{
		$id = intval( array_shift( $args ) );
		$line = $this->admin->get_shop_item_by_id( $id );
		if( !$line || !$line['is_active'] )
		{
			info_page('错误的ID');
		}
		$data['types'] = $this->admin->get_shop_types();
		$data['item'] =  $line;
		return $data;
	}
	private function shop_update_item( $args )
	{
		$id = intval( array_shift( $args ) );
		$line = $this->admin->get_shop_item_by_id( $id );
		if( !$line || !$line['is_active'] )
		{
			info_page('错误的ID');
		}
		if( $line['type'] && $line['type'] != intval(v('type')) )
		{
			$check = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".$line['type']."' " );
			if( $check )
			{
				lazy_run_sql("delete from `shop_extra_".$line['type']."` where `cid` = '{$line['id']}' ");
			}
		}
		if( z(v('name')) == '' )
		{
			info_page('商品名称不能为空','/admin/shop/modify/item/'.$line['id']);
		}
		if( floatval(v('price')) <= 0 )
		{
			info_page('错误的销售价','/admin/shop/modify/item/'.$line['id']);
		}
		if( z(v('number')) == '' )
		{
			info_page('商品货号不能为空','/admin/shop/modify/item/'.$line['id']);
		}
		else
		{
			$check = lazy_get_var( "select count(*) from `u2_shop_items` where `id` != '{$line['id']}' and `number` = ".s(z(v('number'))).";" );
			if( $check )
			{
				info_page('错误的商品货号,此货号已被占用','/admin/shop/modify/item/'.$line['id']);
			}
		}
		$this->admin->save_shop_item( $id );
		info_page("成功修改商品",'/admin/shop/add/item','添加新商品');
	}
	private function  shop_save_item( $args ) 
	{
		$id = intval( array_shift( $args ) );
		$line = $this->admin->get_shop_item_by_id( $id );
		$is_active = v('is_active');
		if( !$line )
		{
			$text = '错误的ID';
			$is_active?info_page($text , '/admin/shop/add/item' ):die($text);
		}
		if( $line['type'] && $line['type'] != intval(v('type')) )
		{
			$check = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".$line['type']."' " );
			if( $check )
			{
				lazy_run_sql("delete from `shop_extra_".$line['type']."` where `cid` = '{$line['id']}' ");
			}
		}
		if( $is_active)
		{
			if( z(v('name')) == '' )
			{
				info_page('商品名称不能为空','/admin/shop/add/item');
			}
			if( z(v('number')) == '' )
			{
				info_page('商品货号不能为空','/admin/shop/add/item');
			}
			else
			{
				$check = lazy_get_var( "select count(*) from `u2_shop_items` where `id` != '{$line['id']}' and `number` = ".s(z(v('number'))).";" );
				if( $check )
				{
					info_page('错误的商品货号,此货号已被占用','/admin/shop/add/item');
				}
			}
			if( floatval(v('price')) <= 0 )
			{
				info_page('错误的销售价','/admin/shop/add/item');
			}
		}
		$time = $this->admin->save_shop_item( $id );
		if( $is_active == 0 )
		{
			die($time);
		}
		info_page("成功添加商品",'/admin/shop/add/item','继续添加商品');
	}
	private function shop_add_type( $args )
	{
		$data['brands'] = $this->admin->get_shop_brands();
		$data['types'] = $this->admin->get_shop_types();
		return $data;
	}
	private function shop_modify_type( $args )
	{
		$id = intval(array_shift( $args ));
		$line = $this->admin->get_shop_type_by_id( $id );
		if( !$line )
		{
			info_page('此商品类型已被删除','/admin/shop/add/type/','添加商品类型');
		}
		$line['extra'] = unserialize( $line['extra'] );
		$data['line'] = $line;
		$data['types'] = $this->admin->get_shop_types();
		$data['brands'] = $this->admin->get_shop_brands();
		return $data;
	}
	private function shop_save_type( $args )
	{
		$data['name'] = z(v('name'));
		if( !$data['name'] )
		{
			info_page('请填写商品类型名称');
		}
		if( $this->admin->check_shop_type( $data['name'] ) )
		{
			info_page("错误, 已有该分类" );
		}
		$brand_array =v('brand');
		if( v('all_brand') )
		{
			$brand_array = array();
		}
		$extra_field = v('extra_field');
		$field = array();
		$sql_fields = array();
		if( $extra_field )
		{
			$i = 1;
			foreach( $extra_field as $v )
			{
				if( $v )
				{
					$v = unserialize( base64_decode($v) );
					$v['id'] = $i;
					$field[$v['id']] = $v;
					$sql_fields[] = '`extra_'.$i.'` varchar( 255 ) NULL';
					$i++;
				}
			}
		}
		$extra['field'] = $field;
		$extra['brands'] = $brand_array;
		$data['extra'] = serialize($extra);
		$type_id =  $this->admin->save_shop_type( $data );
		if( $sql_fields && $type_id )
		{
			lazy_run_sql('CREATE TABLE IF NOT EXISTS `shop_extra_'.$type_id.'`(`id` int(11) NOT NULL auto_increment,`cid` int(11) NOT NULL ,'.join(',',$sql_fields).' ,PRIMARY KEY  (`id`) ) ENGINE=MyISAM ;');
		}
		info_page("添加分类成功" ,'/admin/shop/add/type' ,'继续添加' );
	}
	private function shop_update_type( $args )
	{
		$id = intval(array_shift( $args ));
		$line = $this->admin->get_shop_type_by_id( $id );
		if( !$line )
		{
			info_page('此商品类型已被删除','/admin/shop/add/type/','添加商品类型');
		}
		$data['name'] = z(v('name'));
		if( !$data['name'] )
		{
			info_page('请填写商品类型名称');
		}
		if( $line['name'] != $data['name'] )
		{
			if( $this->admin->check_shop_type( $data['name'] ) )
			{
				info_page("错误, 已存在此商品类型" );
			}
		}
		$brand_array =v('brand');
		if( v('all_brand') )
		{
			$brand_array = array();
		}
		$extra_field = v('extra_field');
		$field = array();
		$sql_fields = array();
		if( $extra_field )
		{
			foreach( $extra_field as $v )
			{
				if( $v )
				{
					$v = unserialize( base64_decode($v) );
					$field[$v['id']] = $v;
					$sql_fields[] = '`extra_'.$v['id'].'` varchar( 255 ) NULL';
				}
			}
		}
		$extra['field'] = $field;
		$extra['brands'] = $brand_array;
		$data['extra'] = serialize($extra);
		$this->admin->update_shop_type( $data , $id );
		$new = $field;
		$line['extra'] = unserialize( $line['extra'] );
		$old = $line['extra']['field'];
		if( !$new )
		{
			$eid = lazy_get_var( "SHOW TABLES LIKE 'shop_extra_".intval($id)."' " );
			if( $eid )
			{
				lazy_run_sql("DROP TABLE `shop_extra_".intval($id)."`");
			}
		}
		elseif( !$old && $sql_fields )
		{
			lazy_run_sql('CREATE TABLE IF NOT EXISTS `shop_extra_'.$line['id'].'`(`id` int(11) NOT NULL auto_increment,`cid` int(11) NOT NULL ,'.join(',',$sql_fields).' ,PRIMARY KEY  (`id`) ) ENGINE=MyISAM ;');
		}
		else
		{
			$acts = array();
			foreach( $new as $v )
			{
				if( isset($old[$v['id']]) )
				{
					unset( $old[$v['id']] );
				}
				else
				{
					$acts[] = 'ADD `extra_'.$v['id'].'` VARCHAR( 255 ) NULL ';
				}
			}
			if( $old )
			{
				foreach( $old as $v )
				{
					$acts[] = 'DROP `extra_'.$v['id'].'`';
				}
			}
			if( $acts )
			{
				lazy_run_sql( "ALTER TABLE `shop_extra_".intval($id)."` ".join(',',$acts).";" );
			}

		}
		info_page("修改商品类型成功" ,'/admin/shop/modify/type/'.$id  ,'返回' );
	}
	private function shop_add_brand( $args )
	{
		$data['brands'] = $this->admin->get_shop_brands();
		return $data;
	}
	private function shop_save_brand( $args )
	{
		$data['name'] = z( v('name') );
		$data['subname'] =z( v('subname') );
		$data['url'] =z( v('url') );
		$data['logo'] =z( v('blogo') );
		if( !$data['name'] )
		{
			info_page('请填写品牌名称');
		}
		if( $this->admin->check_shop_brand( $data['name'] ) )
		{
			info_page("错误, 已有该品牌" );
		}
		$this->admin->save_shop_brand( $data );
		info_page("添加品牌成功" ,'/admin/shop/add/brand' ,'继续添加' );
	}
	private function shop_modify_brand( $args )
	{
		$id = intval(array_shift( $args ));
		$line = $this->admin->get_shop_brand_by_id( $id );
		if( !$line )
		{
			info_page('此品牌已被删除','/admin/shop/add/brand/','添加品牌');
		}
		$data['line'] = $line;
		$data['brands'] = $this->admin->get_shop_brands();
		return $data;
	}
	private function shop_update_brand( $args )
	{
		$id = intval(array_shift( $args ));
		$data['name'] = z( v('name') );
		$data['subname'] =z( v('subname') );
		$data['url'] =z( v('url') );
		$data['logo'] =z( v('blogo') );
		if( !$data['name'] )
		{
			info_page('请填写品牌名称');
		}
		$line = $this->admin->get_shop_brand_by_id( $id );
		if( !$line )
		{
			info_page('此品牌已被删除','/admin/shop/add/brand/','添加品牌');
		}
		if( $data['name'] != $line['name'] )
		{
			if( $this->admin->check_shop_brand( $data['name'] ) )
			{
				info_page("错误, 已有该品牌" );
			}
		}
		$this->admin->update_shop_brand( $data , $id );
		info_page("修改品牌成功" ,'/admin/shop/modify/brand/'.$id  ,'返回' );
	}
	private function shop_add_cate( $args )
	{
		$data['cates'] = $this->admin->get_shop_cates();
		$data['types'] = $this->admin->get_shop_types();
		return $data;
	}
	private function shop_save_cate()
	{
		$data['cate_desc'] = z( v('cate_desc') );
		if( !$data['cate_desc'] )
		{
			info_page('请填写分类名称');
		}
		$pid = intval( v('pid') );
		if( $pid > 0 )
		{
			$line = $this->admin->get_shop_cate_by_id( $pid );
			if( !$line )
			{
				info_page('错误的上级分类');
			}
		}
		$data['pid'] = $pid;
		$data['cate_type'] = intval( v('cate_type') );
		$this->admin->save_shop_cate( $data );
		info_page("添加分类成功" ,'/admin/shop/add/cate/'  ,'继续添加返' );
	}
	private function shop_modify_cate( $args )
	{
		$id = intval(array_shift( $args ));
		$line = $this->admin->get_shop_cate_by_id( $id );
		if( !$line )
		{
			info_page('此分类已被删除','/admin/shop/add/cate/','添加分类');
		}
		$data['line'] = $line;
		$data['cates'] = $this->admin->get_shop_cates();
		$data['types'] = $this->admin->get_shop_types();
		return $data;
	}
	private function shop_update_cate( $args )
	{
		$id = intval(array_shift( $args ));
		$line = $this->admin->get_shop_cate_by_id( $id );
		if( !$line )
		{
			info_page('此分类已被删除','/admin/shop/add/cate/','添加分类');
		}
		$data['cate_desc'] = z( v('cate_desc') );
		if( !$data['cate_desc'] )
		{
			info_page('请填写分类名称');
		}
		$pid = intval( v('pid') );
		if( $pid == $id )
		{
			info_page('错误的上级分类');
		}
		if( $pid > 0 )
		{
			$line = $this->admin->get_shop_cate_by_id( $pid );
			if( !$line )
			{
				info_page('错误的上级分类');
			}
		}
		$data['pid'] = $pid;
		$data['cate_type'] = intval( v('cate_type') );
		$this->admin->update_shop_cate( $data  , $id);
		info_page("修改分类成功" ,'/admin/shop/modify/cate/'.$id  ,'返回' );
	}
	private function shop_list_orders( $args )
	{
		$data['ci_top_title'] = '订单管理';
		$check = $this->admin->check_shopcate_install();
		if( !$check )
		{
			info_page('您还没有安装购物车');
		}
		$page = intval( array_shift( $args ) );
		$page = $page > 0?$page:1;
		$limit = 10;
		$start = ($page -1 )*$limit;
		$data['list'] = $this->admin->get_shop_orders( $start , $limit);
		$page_all = ceil( get_count()/$limit );
		$base = '/admin/shop/list/order';
		$data['states'] = array( 0 => '未支付' , 1 => '已支付' , 2 => '已发货' , 3 => '完成' );
		$data['pager'] = get_pager($page , $page_all , $base );
		return $data;
	}
	function plugs( $id = NULL )
	{
		$this->check_admin();
		
		$id = intval( $id );

		if( $id < 1 )
		{
			info_page('错误的Id');
		}
		$this->load->library('service');
		$this->service->add( 'id' ,$id );
		$result = $this->service->result( 'plugInfo' );
		if( $result == false )
		{
			$data['error'] = true;
			$data['notice'] = '网络错误';
		}
		elseif(  !isset($result['flag']) || $result['flag'] != 'ok' )
		{
			$data['error'] = true;
			$data['notice'] = '数据传输错误';
		}
		else
		{
			$data = $result;
			//print_r( $data );
		}
		$this->view('plugs',$data);
	}
	function getplugs()
	{
		$this->check_admin();
		$id = intval( v('id') );
		$aid = z( v('aid') );
		if( $id < 1 )
		{
			info_page('错误的Id');
		}
		$folder = z(v('folder'));
		if( $folder == '' )
		{
			info_page('请填写安装目录');
		}
		$update = false;
		$path =  'application/app/'.$folder.'/';
		if( file_exists( ROOT. $path.'controller/config.php' ) )
		{
			include( ROOT. $path.'controller/config.php' );
			if( $app_config['id'] != $aid )
			{
				info_page('此目录已安装其他应用');
			}
			else
			{
				$update = true;
			}
		}
		$this->load->library('service');
		$this->service->add( 'id' ,$id );
		$this->service->add( 'aid' ,$aid );
		$result = $this->service->result( 'getPlug' );
		if( $result == false )
		{
			info_page( '网络错误' );
		}
		elseif(  !isset($result['flag']) || $result['flag'] != 'ok' )
		{
			info_page( '数据传输错误' );
		}
		else
		{
			if( $result['error'] )
			{
				info_page( $result['notice'] );
			}
			else
			{
				@MakeDir($path);
				$file = $path.'install.zip';
				file_put_contents( ROOT.$file , $result['zip'] );
				$this->load->library('pclzip' , $file );
				if ($this->pclzip->extract(  PCLZIP_OPT_REPLACE_NEWER , PCLZIP_OPT_PATH, $path ) == 0) 
				{
					info_page("Error : ".$this->pclzip->errorInfo(true));
				}
				@unlink( $file );
				$all_files = app_files( 'application/app/'.$folder );
				if( $all_files )
				{
					foreach( $all_files as $file )
					{
						@touch( ROOT.$file );
					}
				}
				if( $update )
				{
					info_page('应用内容已更新','/admin/applist' ,'管理应用');
				}
				else
				{
					info_page('应用已下载','/admin/applist' ,'管理应用');
				}
			}
		}
	}
}