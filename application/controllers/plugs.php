<?php

class plugs extends Controller {

	function plugs()
	{
		parent::Controller();
	}
	function index() 
	{
		//$data = array();
		$data['ci_top_title'] = '微件列表';
		$args = func_get_args();
		if( isset($args[2]) )
		{
			$search = strip_tags(trim($args[2]));
			$search = urldecode( $search );
		}
		else
		{
			$search = strip_tags(trim(v('search')));
		}
		$data['search'] = $search;
		$type = intval(v('type'));
		
		if( $args )
		{
			$mid = intval($args[0]);
		}
		if( !isset($mid) || $mid == '' )
		{
			$mid = $type;
		}

		//
		if( $mid == '0' )
		{
			$where = " AND `name` LIKE '%".$search."%' ";
			//$data['name'] = '全部范围';
		}
		elseif( $mid > '0' )
		{
			$where = " AND `mid` = '".intval( $mid )."' AND `name` LIKE '%".$search."%'";
			$name = lazy_get_var("SELECT `name` FROM `u2_plugs` WHERE 1 AND `id` = '".intval( $mid )."'");
			if( !$name )
			{
				info_page('错误的组件ID');
			}
		}
		else
		{
			info_page('错误的组件ID');
		}
		$data['mid'] = $mid;
		
		$data['plugs_name'] = lazy_get_data("SELECT * FROM `u2_plugs`");

		$data['page'] = $page = ( !isset( $args[1] ) || intval($args[1]) < 1 ) ? 1 : intval($args[1]);
		$limit = 5;
		$start = ($page-1)*$limit;
		
		$item = lazy_get_data("SELECT sql_calc_found_rows * FROM `u2_plugs_widget` WHERE 1 {$where} ORDER BY `id` DESC  LIMIT {$start},{$limit}");
		$all = get_count();
		$data['item'] = $item;

		//$type = urlencode( $type );
		$base = '/plugs/index/'.$mid;
		$page_all = ceil( $all /$limit);
		$text = urlencode($search);
		$data['pager'] = get_pager( $page , $page_all , $base , $text );
		
		$data['is_admin'] = is_admin() ? true : false ;
		$domain = _sess('domain');
		if( $domain != '' )
		{
			$data['domain'] = $domain;
		}
		$this->view('list',$data);
	}
	
	function add()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		$data = array();
		$data['ci_top_title'] = '添加组件';
		
		$this->view( 'add' , $data );
	}

	function save()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		$name = strip_tags(trim(v('name')));
		$developer = strip_tags(trim(v('developer')));
		$link = strip_tags(trim(v('link')));
		$money = intval(trim(v('money')));
		$desp = strip_tags(v('desp'));
		$pic = strip_tags(v('plugs_file'));
		$big_pic = strip_tags(v('plugsbig_file'));
		$aid = strip_tags(trim(v('aid')));
		$download = strip_tags(trim(v('download')));
		$uid = format_uid();
		
		
		if( $name == '' )
		{
			info_page('组件名称不能为空!');
		}
		if( $desp == '' )
		{
			info_page('请填写组件简介!');
		}
		if( $download == '' )
		{
			info_page('请填写组件位置!');
		}
		if( $link === 'http://' )
		{
			$link = null;
		}
		//
		$deve_type = is_admin() ? 0 : 1 ;
		$is_active = $deve_type == 0 ? 1 : 0 ;
		
		$plugs_insert  = "INSERT INTO `u2_plugs` (`aid`, `name`, `pic`, `big_pic`, `desp`, `developer`, `uid`, `link`, ";
		$plugs_insert .= "`deve_type`, `download`, `time`, `money`, `is_active`, `type`) ";
		$plugs_insert .= "VALUES(".s($aid).", ".s($name).", ".s($pic).", ".s($big_pic).", ".s($desp).", ".s($developer).",";
		$plugs_insert .= " '".intval($uid)."', ".s($link).", '".$deve_type."', ".s($download).",";
		$plugs_insert .= " '".date('Y-m-d')."', '".$money."', '".$is_active."', '1' )";
	
		//echo $plugs_insert;
		lazy_run_sql( $plugs_insert );
		info_page( '添加成功!' , '/riki/index/4/' , '| 返回组件列表' );
		//header('Location: /riki/index/4/');
	}

	function modify()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}
		
		
		$data['ci_top_title'] = '编辑组件';
		$args = func_get_args();
		if( !isset( $args[0] ) )
		{
			info_page( '组件ID不能为空!' );
		}
		$pid = intval( $args[0] );
		
		$plugs = lazy_get_line("SELECT * FROM `u2_plugs` WHERE `id` = '".$pid."' LIMIT 1");
		if( !$plugs )
		{
			info_page( '组件ID错误!' );
		}
		$data['plugs'] = $plugs;
		$this->view( 'modify' , $data );
	}

	function update()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}
		
		$pid = intval(v('pid'));
		$name = strip_tags(trim(v('name')));
		$developer = strip_tags(trim(v('developer')));
		$link = strip_tags(trim(v('link')));
		$money = intval(trim(v('money')));
		$desp = strip_tags(v('desp'));
		$pic = strip_tags(v('plugs_file'));
		$big_pic = strip_tags(v('plugsbig_file'));
		$aid = strip_tags(trim(v('aid')));
		$download = strip_tags(trim(v('download')));
		$uid = format_uid();
		
		if( $pid < 1 )
		{
			info_page('组件ID错误!');
		}
		$pnum = lazy_get_var( "SELECT COUNT(*) FROM `u2_plugs` WHERE `id` = '".intval( $pid )."'" );	
		if( !$pnum )
		{
			info_page('组件ID错误!');
		}

		if( $name == '' )
		{
			info_page('组件名称不能为空!');
		}
		if( $desp == '' )
		{
			info_page('请填写组件简介!');
		}
		if( $link === 'http://' )
		{
			$link = null;
		}
		
		$plugs_update  = "UPDATE `u2_plugs` SET `aid` = ".s($aid).", `name` = ".s($name).", `pic` = ".s($pic).",";
		$plugs_update .= " `big_pic` = ".s($big_pic).", `download` = ".s($download).",";
		$plugs_update .= "`desp` = ".s($desp).", `developer` = ".s($developer).", `uid` = '".intval($uid)."',";
		$plugs_update .= "`link` = ".s($link).", `time` = '".date('Y-m-d')."', `money` = '".$money."', `type` = '1'";
		$plugs_update .= "WHERE `id` = '".intval($pid)."'";
		
		lazy_run_sql( $plugs_update );
		lazy_run_sql( "UPDATE `u2_plugs_widget` SET `aname` = ".s($name)." , `aid` = ".s($aid)." WHERE `mid` = '".intval( $pid )."'");
		
		info_page( '修改成功!' , '/riki/index/4/' , '| 返回组件列表' );
		//header('Location: /riki/index/10/');
	}

	function del()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		$args = func_get_args();
		
		if( !isset( $args[0] ) )
		{
			info_page( '组件ID不能为空!' );
		}

		$pid = intval($args[0]);
		if( $pid < 1 )
		{
			info_page( '错误的组件ID!' );
		}	
		lazy_run_sql("DELETE FROM `u2_plugs` WHERE `id` = '".intval( $pid )."' LIMIT 1");
		lazy_run_sql("DELETE FROM `u2_plugs_widget` WHERE `mid` = '".intval( $pid )."'");

		//info_page( '删除成功!' , '/riki/index/10/' , '| 返回组件列表' );
		header('Location: /riki/index/4/');
	}
	
	function widgets_add()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		$data = array();
		$data['ci_top_title'] = '添加微件';
		$args = func_get_args();
		if( !isset( $args[0] ) )
		{
			info_page('组件ID不能为空');
		}
		$data['pid'] = intval( $args[0] );
		$name = lazy_get_var("SELECT `name` FROM `u2_plugs` WHERE `id` = '".$data['pid']."' LIMIT 1");
		if( !$name )
		{
			info_page( '组件ID错误!' );
		}
		$data['name'] = $name;

		$this->view( 'widgets_add' , $data );
	}

	function widgets_save()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		//print_r( $_POST );
		$pid = intval(v('pid'));
		$name = strip_tags(trim(v('name')));
		$plugs_file = strip_tags(v('plugs_file'));
		$plugs_big_file = strip_tags(v('plugsbig_file'));
		$desp = strip_tags(v('desp'));
		if( $pid < 1 )
		{
			info_page('组件ID不能为空');
		}

		$plugs = lazy_get_line("SELECT * FROM `u2_plugs` WHERE `id` = '".$pid."' LIMIT 1");
		if( !$plugs )
		{
			info_page( '组件ID错误!' );
		}

		if( $name == '' )
		{
			info_page('微件名称不能为空!');
		}

		if( $desp == '' )
		{
			info_page('微件简介不能为空!');
		}
		
		$widgets_insert  = "INSERT INTO `u2_plugs_widget` (`mid`, `aid`, `aname`, `name`, `pic`, `big_pic`, `desp`, `time`) VALUES ";
		$widgets_insert .= "( '".intval($pid)."', ".s($plugs['aid']).", ".s($plugs['name']).", ".s($name).", ".s($plugs_file).", ".s($plugs_big_file).", ".s($desp).", '".date('Y-m-d')."' )";
		lazy_run_sql( $widgets_insert );
		lazy_run_sql("UPDATE `u2_plugs` SET `has_widget` = `has_widget` + 1 WHERE `id` = '".$pid."'");

		info_page( '微件添加成功! | <a href=/plugs/widgets_add/'.$pid.'>继续添加</a>' , '/plugs/index/'.$pid , '|  返回微件目录'  );
	}

	function widgets_del() 
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}

		$args = func_get_args();
		
		if( !isset( $args[0] ) )
		{
			info_page( '微件ID不能为空!' );
		}

		$wid = intval($args[0]);
		if( $wid < 1 )
		{
			info_page( '错误的微件ID!' );
		}

		$mid = lazy_get_var("SELECT `mid` FROM `u2_plugs_widget` WHERE `id` = '".intval( $wid )."'");
		if( !$mid )
		{
			info_page( '错误的微件ID!' );
		}
		
		lazy_run_sql("DELETE FROM `u2_plugs_widget` WHERE `id` = '".intval( $wid )."'");
		lazy_run_sql("UPDATE `u2_plugs` SET `has_widget` = `has_widget` - 1 WHERE `id` = '".$mid."'");
		
		header('Location: /plugs/index/'.$mid);
	}

	function widgets_modify()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}
		
		
		$data['ci_top_title'] = '编辑微件';
		$args = func_get_args();
		if( !isset( $args[0] ) )
		{
			info_page( '微件ID不能为空!' );
		}
		$wid = intval( $args[0] );
		
		$item = lazy_get_line("SELECT * FROM `u2_plugs_widget` WHERE `id` = '".$wid."' LIMIT 1");
		if( !$item )
		{
			info_page( '微件ID错误!' );
		}

		$data['item'] = $item;
		$this->view( 'widgets_modify' , $data );
	}

	function widgets_update()
	{
		if( !is_login() )
		{
			info_page('请登录后查看');
		}

		if( !is_admin() )
		{
			info_page('你没有权限进行操作!');
		}
		
		$id = intval(v('id'));
		$name = strip_tags(trim(v('name')));
		$desp = strip_tags(v('desp'));
		$pic = strip_tags(v('plugs_file'));
		$big_pic = strip_tags(v('plugsbig_file'));
		
		if( $id < 1 )
		{
			info_page('微件ID错误!');
		}
		$mid = lazy_get_var( "SELECT `mid` FROM `u2_plugs_widget` WHERE `id` = '".intval( $id )."'" );	
		if( !$mid )
		{
			info_page('微件ID错误!');
		}

		if( $name == '' )
		{
			info_page('微件名称不能为空!');
		}
		if( $desp == '' )
		{
			info_page('请填写微件简介!');
		}
		
		$widgets_update  = "UPDATE `u2_plugs_widget` SET `name` = ".s($name).", `pic` = ".s($pic).", `big_pic` = ".s($big_pic).",";
		$widgets_update .= "`desp` = ".s($desp).", `time` = '".date('Y-m-d')."' ";
		$widgets_update .= "WHERE `id` = '".intval($id)."'";
		//echo $widgets_update;
		lazy_run_sql( $widgets_update );
		info_page( '修改成功!' , '/plugs/index/'.$mid , '| 返回' );
	}

	function pic()
	{

		$data['ci_top_title'] = '效果图';
		$args = func_get_args();
		if( !isset( $args[0] ) )
		{
			info_page( '组件ID不能为空!' );
		}
		$pid = intval( $args[0] );
		
		$tid = isset($args[1]) && intval($args[1]) != ''? $args[1] : 1 ;
		if( $tid == '1' )
		{
			$sql = "SELECT `name`,`big_pic` FROM `u2_plugs` WHERE `id` = '".$pid."' LIMIT 1";
		}
		else
		{
			$sql = "SELECT `name`,`big_pic` FROM `u2_plugs_widget` WHERE `id` = '".$pid."' LIMIT 1";
		}

		
		$item = lazy_get_line($sql);
		if( !$item )
		{
			info_page( '组件ID错误!' );
		}

		$data['item'] = $item;
		$this->view( 'pic' , $data );
	}
	private function view($page,$data)
	{
		$data['page_name'] = $page;

		layout($data);
	}
}
?>