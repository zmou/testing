<?php

class design extends Controller {

	function design()
	{
		parent::Controller();
		$this->lang->load('admin');
		$this->load->model('Form_model', 'form', TRUE);	
		$this->load->model('Item_model', 'item', TRUE);
		$this->load->helper('design');
	}
	
	function index( $page = NULL )
	{
		check_admin();
		$data = array();
		$limit = 10 ;
		$page = intval( $page ) > 0 ? $page : 1;
		$start = ( $page - 1 ) * $limit ;
		$data['froms'] = $this->form->get_forms($start , $limit );
		$page_all = ceil( get_count() / $limit );
		$data['pager'] = get_pager(  $page , $page_all , '/design');
		$this->view( 'index' ,$data);
	}
	
	function init()
	{
		check_admin();
		$data = array();
		$this->view( 'init' ,$data);
	}
	
	function form_save()
	{
		$this->check_admin();
		$name = z(v('name'));
		$title = z(v('title'));
		if( !$name || !$title )
		{
			info_page('英文标示和标题不能为空');
		}
		$path = 'application/app/'.$name.'/';
		if( is_dir( ROOT.$path ) )
		{
			info_page($path.'目录已被占用');
		}
		$count = lazy_get_var("select count(*) from `w2_form` where `name` = '$name' ");
		if( $count )
		{
			info_page('英文唯一标示已被占用!');
		}
		$this->form->save();
		
	}
	
	function form( $fid = NULL )
	{
		check_admin();
		$fid = intval( $fid );
		if( $fid < 1 ) info_page( 'Form参数错误' );
		
		$data = array();
		
		$data['finfo'] = $this->form->get_form_info_by_id( $fid );
		$data['fitems'] = $this->item->get_items_by_fid( $fid );
		
		$this->view( 'form' ,$data);
	}
	function modify( $fid = NULL )
	{
		check_admin();
		$fid = intval( $fid );
		if( $fid < 1 ) info_page( 'Form参数错误' );
		
		$data = array();
		
		$data['finfo'] = $this->form->get_form_info_by_id( $fid );
		if( !$data['finfo'] )
		{
			info_page('错误的组件参数!');
		}
		$this->view( 'modify' ,$data);
	}
	function modify_save($fid = NULL )
	{
		check_admin();
		$fid = intval( $fid );
		if( $fid < 1 ) info_page( 'Form参数错误' );
		$title = z(v('title'));
		if( !$title )
		{
			info_page('标题不能为空');
		}
		$this->form->modify($fid);
		info_page('修改组件信息成功' , '/design','返回组件列表');
	}
	function settings( $fid = NULL )
	{
		check_admin();
		$fid = intval( $fid );
		if( $fid < 1 ) info_page( 'Form参数错误' );

		$data = array();

		$data['finfo'] = $this->form->get_form_info_by_id( $fid );
		
		$this->view( 'settings' ,$data);
	
	}
	
	function settings_update()
	{
		$this->check_admin();
		$this->form->update();
	}
	
	function build( $fid )
	{
		check_admin();
		$this->load->library('builder');
		$this->builder->init( $fid );
		
		$this->builder->make();
		//echo $fid;
		
		// create main app
		// 
		
		
	}
	
	// --------------------------------------
	
	function get_item_html( $fid , $type = 'line' , $iid = NULL )
	{
		$this->check_admin();
		if( $iid == NULL )
		{
			if( intval( $fid ) < 1 )
			{
				echo '错误的form参数';
				return false;
			}
			
			// add 
			$type = basename( $type );
			$iid = $this->item->add( $fid , $type );
		}
		else
		{
			$iinfo = $this->item->get_item_info_by_id( $iid );
			$type = $iinfo['type'];
		}
		
		//echo basename($type);
		
		@include(  APPPATH.'views/layout/design/item/' . $type . '.tpl.html' );
		
		echo '<script>last_li_id = ' . $iid . ';</script>';
	}
	
	function item_settings( $iid )
	{
		check_admin();
		$data = array();
		
		if( intval( $iid ) < 1 )
		{
			echo '错误的item参数';
			return false;
		}
		
		$data['iinfo'] = $this->item->get_item_info_by_id( $iid );
		$data['type'] = $data['iinfo']['type'];
		if( isset( $data['iinfo']['type_values'] ) )
		{
			$data['tvalue'] = unserialize( $data['iinfo']['type_values'] );
		}
		
		layout( $data , 'ajax' );
		//$this->view( 'item_settings' , $data );
	}
	
	function item_update()
	{
		$this->check_admin();
		//print_r( $_POST );
		echo $this->item->update();
	}
	
	function update_item_order( $order )
	{
		$this->check_admin();
		$ods = explode( ',' , $order );
		$ods = array_reverse( $ods );
		
		foreach( $ods as $key => $value )
		{
			$this->item->update_order( $value , ($key+1) );
		}
	}
	
	function item_remove( $iid )
	{
		$this->check_admin();
		$this->item->remove( $iid );
	}
	
	
	function view( $page , $data )
	{
		$data['ci_top_title'] = _text('design_'.$page.'_title');
		$data['page_name'] = $page;

		layout($data , 'design');
	}
	function check_admin()
	{
		if(!is_admin())
		{
			die('limit right');
		}
	}
	function display( $id = NULL , $first = false )
	{
		check_admin();
		$id = intval( $id );
		$data['finfo'] = $this->form->get_form_info_by_id( $id );
		if( !$data['finfo'] )
		{
			info_page( '错误的组件Id' );
		}
		$data['fitems'] = $this->item->get_items_by_fid( $id );
		$items = array();
		if(!$data['fitems'])
		{
			info_page( '此组件无字段.' );
		}
		else
		{
			foreach( $data['fitems'] as $v )
			{
				$items[$v['id']] = $v;
			}
		}
		if( $data['finfo']['order'] )
		{
			$rows = array();
			$data['finfo']['order'] = unserialize( $data['finfo']['order'] );
			$order = $data['finfo']['order']['order'];
			$key = 0 ;
			if( is_array($order) && $order )
			{
				foreach( $order  as $key => $row )
				{
					if( is_array( $row ) &&  $row )
					{
						foreach( $row as $v )
						{
							if( is_numeric($v) && isset($items[$v]) )
							{
								$rows[$key][] = $items[$v];
							}
							else
							{
								$rows[$key][] = $v;
							}
						}
					}
				}
			}
			$data['rows'] = $rows;
		}
		else
		{
			$first = 1;
		}
		//print_r($data['finfo']['order']);
		$data['ci_top_title'] = '设置页面布局';
		$data['first'] = $first;
		$this->view( 'display' ,$data);
	}
	function save_location( $id = NULL )
	{
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$this->check_admin();
		$finfo =  $this->form->get_form_info_by_id( $id );
		if( !$finfo )
		{
			info_page( '错误的组件Id' );
		}
		$fitems= $this->item->get_items_by_fid( $id );
		if(!$fitems)
		{
			info_page( '此组件无字段.' );
		}
		$layout = v('layout');
		$order = v('order');
		$sorder = array();
		if( $layout )
		{
			$data['layout'] =  $layout;
			foreach( $fitems  as $v)
			{
				if( $layout == '4' && ( $v['key'] == 'pic' ||  $v['key'] == 'title' )  )
				{
					$sorder[0][] = $v['id'];
				}
				else
				{
					$id_order[] = $v['id'];
				}
				if( $v['key'] == 'price' )
					$price = true;
			}
			if( isset($price) )
			{
				$id_order[] = 'price';
			}
			if( $finfo['state'] )
			{
				$id_order[] = 'state';
			}
			$sorder = make_desgin_display_order( $id_order , $layout , $sorder );
			$data['order'] = $sorder;
			lazy_run_sql("update `w2_form` set `order` = '".serialize($data)."' where `id` = '{$finfo['id']}' limit 1 ");
			header('Location: /design/display/'.$finfo['id']);
		}
		else
		{
			if( !$order )
			{
				if( $finfo['order'] )
				{
					header('Location: /design/update/'.$finfo['id']);
					die();
				}
				else
				{
					die('error no post data');
				}
			}
			if( !$finfo['order']  )
			{
				$this->display( $finfo['id']  , 1 );
			}
			$old_data = unserialize($finfo['order']);
			$old = array();
			foreach($old_data['order'] as $v)
			{
				$old = array_merge($old,$v);
			}
			$app = explode('-',$order);
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
			$old_data['order'] = $new;
			lazy_run_sql("update `w2_form` set `order` = '".serialize($old_data)."' where `id` = '{$finfo['id']}' limit 1 ");
			header('Location: /design/update/'.$finfo['id']);
		}
	}
	function del_block(  $bid  ,  $key )
	{
		$this->check_admin();
		$id = intval( $bid );
		if( !$id )
		{
			die( '错误的组件Id' );
		}
		$finfo = $this->form->get_form_info_by_id( $id );
		if( !isset( $finfo['order'] ) || !$finfo['order'] )
		{
			die('error no order data');
		}
		$order_info = unserialize( $finfo['order'] );
		foreach( $order_info['order'] as $k => $v)
		{
			if( ($killid = array_search($key,$v )) !== false )
			{
				unset($v[$killid]);
				$order_info['order'][$k] = $v;
			}
		}
		lazy_run_sql("update `w2_form` set `order` = '".serialize($order_info)."' where `id` = '{$finfo['id']}' limit 1 ");
	}
	function init_order( $id )
	{
		check_admin();
		$id = intval( $id );
		$finfo = $this->form->get_form_info_by_id( $id );
		if( !$finfo )
		{
			info_page( '错误的组件Id' );
		}
		$fitems = $this->item->get_items_by_fid( $id );
		$items = array();
		if(! $fitems )
		{
			info_page( '此组件无字段.' );
		}
		if( $finfo['order'] )
		{
			$order_info = unserialize( $finfo['order']  ); 
			$layout = $order_info['layout'];
			$data['layout'] =  $layout;
			$price = false;
			$sorder = array();
			foreach( $fitems  as $v)
			{
				if( $layout == '4' && ( $v['key'] == 'pic' ||  $v['key'] == 'title' )  )
				{
					$sorder[0][] = $v['id'];
				}
				else
				{
					$id_order[] = $v['id'];
				}
				if( $v['key'] == 'price' )
					$price = true;
			}
			if( $price )
			{
				$id_order[] = 'price';
			}
			if( $finfo['state'] )
			{
				$id_order[] = 'state';
			}
			$sorder = make_desgin_display_order( $id_order , $layout , $sorder );
			$data['order'] = $sorder;
			lazy_run_sql("update `w2_form` set `order` = '".serialize($data)."' where `id` = '{$finfo['id']}' limit 1 ");
			header('Location: /design/display/'.$finfo['id']);
		}
		else
		{
			header('Location: /design/display/'.$finfo['id']);
		}
	}
	function bindkey( $id = NULL )
	{
		check_admin();
		$id = intval( $id );
		$data['finfo'] = $this->form->get_form_info_by_id( $id );
		if( !$data['finfo'] )
		{
			info_page( '错误的组件Id' );
		}
		$data['fitems'] = $this->item->get_items_by_fid( $id );

		$this->view( 'bindkey' ,$data);

	}
	function bindsave( $id = NULL )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$title = intval( v('title') );
		$pic = intval( v('pic') );
		$desp = intval( v('desp') );
		$price = intval( v('price') );
		if( !$title || !$desp )
		{
			info_page( '标题和简介不能为空' );
		}
		$sql = "update `w2_item` set `key` = NULL where `fid` = '$id' ;";
		lazy_run_sql( $sql );
		$sql = "update `w2_item` set `key` = 'title' where `fid` = '$id' and `id` = '$title' ;";
		lazy_run_sql( $sql );
		$sql = "update `w2_item` set `key` = 'desp' where `fid` = '$id' and `id` = '$desp' ;";
		lazy_run_sql( $sql );
		if( $pic )
		{
			$sql = "update `w2_item` set `key` = 'pic' where `fid` = '$id' and `id` = '$pic' ;";
			lazy_run_sql( $sql );
		}
		if( $price )
		{
			$sql = "update `w2_item` set `key` = 'price' where `fid` = '$id' and `id` = '$price' ;";
			lazy_run_sql( $sql );
		}
		header('Location: /design/display/'.$id );
	}
	function update($id = NULL )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$data['update'] = true;
		$data['error'] = array();
		$finfo = $this->form->get_form_info_by_id( $id );
		if( !$finfo )
		{
			$data['error']['finfo'] = true;
			$data['update'] = false;
		}
		$fitem = $this->item->get_items_by_fid( $id );
		if( !$fitem )
		{
			$data['error']['items'] = true;
			$data['error']['bind'] = true;
			$data['update'] = false;
		}
		else
		{
			$keys = array();
			foreach( $fitem as $v )
			{
				if( $v['key'] )
				{
					$keys[] = $v['key'];
				}
			}
			if( !in_array( 'title', $keys ) || !in_array( 'desp', $keys ) || count( $keys ) > 4 )
			{
				$data['error']['bind'] = true;
				$data['update'] = false;
			}
		}
		if( !$finfo['order'] )
		{
			$data['error']['order'] = true;
			$data['update'] = false;
		}
		$field = get_old_fields( $finfo['id'] );
		$forminfo['field'] = $field;
		$forminfo['finfo'] = $finfo;
		$forminfo['fitem'] = $fitem;
		$data['forminfo'] = serialize($forminfo);
		$data['id'] = $id;
		//echo $data['forminfo'];
		$this->view( 'update' ,$data);
	}
	function upgrade($id = NULL )
	{
		check_admin();
		$infos = v('infos');
		if( !$infos )
		{
			info_page( '错误,无数据' );
		}
		$this->load->library('service');
		$this->service->add( 'infos' ,$infos );
		$res = $this->service->result( 'getModule' );
		if( $res == false )
		{
			$data['error'] = true;
			$data['notice'] = '网络错误';
		}
		else
		{
			if( $res['flag'] != 'ok' )
			{
				$data['error'] = true;
				$data['notice'] = '数据传输错误';
			}
			else
			{
				if( $res['error'] )
				{
					$data['error'] = true;
					$data['notice'] = $res['notice'];
				}
				else
				{
					if( $id != $res['fid'] )
					{
						info_page('错误的组件ID');
					}
					$data['error'] = false;
					$path = 'application/app/'.$res['folder'].'/';
					MakeDir( ROOT. $path );
					if( !is_writable( ROOT. $path ) )
					{
						info_page('错误 目录'.$path.'不可以写');
					}
					file_put_contents( $path.'install.zip', $res['zip'] );
					$data['notice'] = '数据已成功下载到目录'.$path;
					$data['id'] = $id;
				}
			}
		}
	
		$this->view( 'upgrade' ,$data);
	}
	function unzip( $id = NULL )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$finfo = $this->form->get_form_info_by_id( $id );
		if( !$finfo )
		{
			info_page( '错误的组件Id' );
		}
		$data['folder'] = $finfo['name'];
		$path = 'application/app/'.$finfo['name'].'/';
		$file = $path.'install.zip';
		if( !file_exists( ROOT.$file ) )
		{
			info_page('错误 文件'.$file.'不存在');
		}
		$this->load->library('pclzip' , $file );
		if ($this->pclzip->extract(  PCLZIP_OPT_REPLACE_NEWER , PCLZIP_OPT_PATH, $path ) == 0) 
		{
			info_page("Error : ".$this->pclzip->errorInfo(true));
		}
		@unlink( $file );
		if( !file_exists( ROOT.'static/icon/'.$finfo['name'].'.gif' ) )
		{
			@copy( ROOT.'static/images/app.default.png' , ROOT.'static/icon/'.$finfo['name'].'.gif' );
		}
		$all_files = app_files( 'application/app/'.$finfo['name'] );
		if( $all_files )
		{
			foreach( $all_files as $file )
			{
				@touch( ROOT.$file );
			}
		}
		@include_once( $path.'controller/operate.php' );
		$updatesql = ( isset( $update['sql'] )&& $update['sql'] )?1:0;
		$data['installed'] = check_app_install( $id );
		if( $data['installed'] && $updatesql )
		{
			$data['update'] = true;
		}
		else
		{
			$data['update'] = false;
		}
		$data['id'] = $id;
		$this->view( 'unzip' ,$data);

	}
	function upsql( $id )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$finfo = $this->form->get_form_info_by_id( $id );
		if( !$finfo )
		{
			info_page( '错误的组件Id' );
		}
		$path = 'application/app/'.$finfo['name'].'/';
		@include_once( $path.'controller/operate.php' );
		$updatesql = ( isset( $update['sql'] )&& $update['sql'] )?1:0;
		if( check_app_install( $id ) && $updatesql )
		{
			$sqls = $update['sql'];
			foreach( $sqls as $sql )
			{
				lazy_run_sql( $sql );
			}
			info_page( '更新组件成功.  ','/admin/applist','管理组件' );
		}
		else
		{
			info_page( '错误: 此组件不需要更新','/admin/applist','管理组件' );
		}
	}
	function del_confirm( $id = NULL  )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$data['finfo'] = $this->form->get_form_info_by_id( $id );
		if( !$data['finfo'] )
		{
			info_page('错误: 无此组件.');
		}
		$path = 'application/app/'.$data['finfo']['name'].'/';
		$data['extra'] = false;
		if( is_dir( ROOT.$path ) )
		{
			$data['extra'] = true;
			$data['disabled'] = NULL;
			if( check_app_install( $data['finfo']['id'] ) )
			{
				$data['disabled'] = 'disabled';
			}
		}
		$this->view( 'del_confirm' ,$data);
	}
	function del($id = NULL  )
	{
		check_admin();
		$id = intval( $id );
		if( !$id )
		{
			info_page( '错误的组件Id' );
		}
		$data['finfo'] = $this->form->get_form_info_by_id( $id );
		if( !$data['finfo'] )
		{
			info_page('错误: 无此组件.');
		}
		$folder = $data['finfo']['name'];
		$path = 'application/app/'.$folder.'/';
		$this->form->del_form_by_id( $id );
		$this->item->del_item_by_fid( $id );
		if( v('unlink') )
		{
			deldir( $path );
		}
		info_page('成功删除组件' , '/design','返回组件列表');
	}
	
}