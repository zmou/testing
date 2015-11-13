<?php
class Admin_model extends Model 
{

    function Admin_model()
    {
        parent::Model();
    }

	function app_is_install($aid , $folder = NULL )
	{
		$this->db->select('*')->from('u2_app')->where('aid',$aid)->limit(1);

		$line = lazy_get_line();

		if( $line )
		{
			if( $folder && strtolower( $line['u2_folder']) != strtolower( $folder )  )
			{
				info_page('您已经在其他目录安装过此应用了');
			}
			return true;;
		}
		else
		{
			return false;
		}
	}
	function get_apps()
	{
		return lazy_get_data("select * from `u2_app` " , 'aid');
	}
	function run_sqls($sqls)
	{
		if(isset($sqls['sql']) && is_array($sqls['sql'])  )
		{
			foreach ($sqls['sql'] as $sql)
			{
				if($sql != NULL)
					$this->db->query($sql);
			}
		}
	}
	
	function drop_app_table($tables)
	{
		foreach ($tables as $table)
		{
			if($table != NULL)
			{
				$sql = "DROP TABLE IF EXISTS `".trim($table)."`";
				$this->db->query($sql);
			}
		}
	}
	
	function del_app_and_widget_by_aid($aid)
	{
		$this->db->where('u2_aid',$aid);

		$this->db->delete('u2_widget_instance');

		$this->db->where('u2_aid',$aid);

		$this->db->delete('u2_widget' );
		
		$this->db->where('aid',$aid);

		$this->db->delete('u2_app' );
	}
	
	function add_app($config)
	{
		$data['aid'] = $config['id'];
		$data['u2_folder'] = $config['folder'];
		$data['u2_title'] = $config['name'];
		$data['u2_desc'] = $config['desc'];
		$data['u2_is_active'] = 1;
		$data['u2_has_widgets'] = $config['widget'];
		$data['u2_icon'] = $config['icon'];
		$data['u2_left_nav'] = $config['left'];
		
		
		
		
		$data['u2_time'] = date("Y-m-d H:i:s");
		

		$this->db->insert('u2_app', $data);
	}
	
	function load_members($start = 0 ,$limit = 3 , $is_admin = 0 ,$searchtext = NULL )
	{
		$this->db->select('sql_calc_found_rows *')->from('u2_user')->limit($limit,$start);

		if($is_admin)
		{
			$this->db->where('u2_level >=','5');
		}
		if($searchtext)
		{
			$this->db->like('u2_nickname',$searchtext);
			$this->db->orlike('u2_email',$searchtext);
		}

		$this->db->orderby('id','DESC');

		return lazy_get_data();
	}
	
	function add_widget($config,$aid,$folder)
	{
		$data['u2_aid'] = $aid;
		$data['u2_folder'] = $folder;
		$data['u2_name'] = $config['name'];
		$data['u2_desc'] = $config['desc'];
		$data['u2_is_active'] = 1;
		$data['u2_stats'] = $config['stats'];

		$this->db->insert('u2_widget', $data);
	}
	
	function change_level($ids,$level)
	{
		$iid = join(',',$ids);
		$data['u2_level'] = intval($level);
		$mylevel = _sess('u2_level');
		if( $mylevel != 9 )
		{
			$this->db->where("`u2_level` < '".intval( $mylevel )."'");
		}
		$this->db->where("`id` IN (".$iid.")");
		$this->db->update('u2_user',$data);
		$this->db->select('u2_sid')->from('u2_online')->where("`u2_uid` IN (".$iid.")");
		$users = lazy_get_data();
		if($users)
		{
			$mysid = session_id();
			foreach($users as $u)
			{
				if( $u['u2_sid'] )
				{
					session_id( $u['u2_sid'] );
					session_destroy();
				}
			}
			session_id( $mysid );
		}


	}
	function load_manager($start, $limit,$key )
	{
		if( $key == 'done' )
		{
			$this->db->where( '`u2_doid` > 0 ');
		}
		else
		{
			$this->db->where( 'u2_state',$key );
		}
		$this->db->select('sql_calc_found_rows *')->from('u2_manager')->limit( $limit, $start);

		return  lazy_get_data();
	}
	
	function get_pages($is_system = 0)
	{
		$this->db->select('*')->from('u2_page')->where('u2_is_system',$is_system)->orderby('u2_order')->limit(100);
		return lazy_get_data();
	}
	
	function get_new_members_num()
	{
		$this->db->select('COUNT(*)')->from('u2_user')->where('u2_joindate',date("Y-m-d") );
		return lazy_get_var();
	}

	function save_meta($info)
	{
		$annexs = array('u2_annex_1','u2_annex_2','u2_annex_3','u2_annex_4','u2_annex_4','u2_annex_5');

		$this->db->select('u2_en_name')->from('u2_meta_field');

		if($info['cate'] != 0)
		{
			$this->db->where('u2_cate_id',$info['cate'])->orwhere('u2_cate_id','0');
		}
		
		$tempkeys = lazy_get_data();
		
		$keys = array();
		
		if($tempkeys)
		{
			foreach($tempkeys as $v)
			{
				$keys[] = $v['u2_en_name'];
			}
		}
		$res = array_diff( $annexs , $keys);
		if( !$res )
		{
			info_page(_text('admin_data_save_maxed'));
			die();
		}
		$data['u2_en_name'] = array_pop($res);
		$data['u2_cn_name'] = $info['name'];
		$data['u2_type'] = 'text';
		$data['u2_is_active '] = 1;
		$data['u2_cate_id'] = $info['cate'];
		$data['u2_desp'] = $info['info'];
		$data['u2_select'] = '';
	
		$this->db->insert('u2_meta_field', $data );	
	}
	
	function cate_do_action( $action , $cid , $name = NULL)
	{
		if($action == '1')
		{
			$this->db->select('*')->from('u2_cate')->where('id',$cid);
			$cinfo = lazy_get_line();

			$this->db->select('count(*)')->from('u2_cate')->where('u2_cate_num <',$cinfo['u2_cate_num'].'9999')->where('u2_cate_num >' ,$cinfo['u2_cate_num'].'0000');

			$count = lazy_get_var();

			if($count)
			{
				info_page(_text('admin_data_cate_del_error'));
				die();
			}
			$this->db->where('u2_cate' , $cid );
			$this->db->delete('u2_content');
			$this->db->where('u2_cate_id' , $cid );
			$this->db->delete('u2_meta_field');
			$this->db->where('id' , $cid );
			$this->db->delete('u2_cate');
		}
		elseif($action == '2')
		{
			$this->db->where('id',$cid);
			$data['u2_cate_desc'] = $name;
			$this->db->update('u2_cate',$data);
		}
		elseif($action == '0')
		{
			if($cid != '0')
			{
				$this->db->select('*')->from('u2_cate')->where('id',$cid);
				$cinfo = lazy_get_line();
				$father_num = $cinfo['u2_cate_num'];
			}
			else
			{
				$father_num = NULL;
			}

			$this->db->select('*')->from('u2_cate')->where('u2_cate_num <',$father_num.'9999' )->where('u2_cate_num >',$father_num.'0000')->orderby('u2_cate_num','DESC')->limit(1);

			$now = lazy_get_line();

			if( !isset($now['u2_cate_num']) || strlen($now['u2_cate_num']) == strlen($father_num) )
			{
				$data['u2_cate_num'] = $father_num.'0001';
			}
			else
			{
				$temp = substr( $now['u2_cate_num'] , - 4 ) + 1 ;
				//$temp = str_replace( $father_num , '' ,$now['u2_cate_num']) + 1; 
			
				$data['u2_cate_num'] = $father_num.str_pad($temp, 4, "0", STR_PAD_LEFT);  
			}

			$data['u2_cate_desc'] = $name;

			$this->db->insert( 'u2_cate' , $data );
			    
		}
	}
	function get_data_extra($id = NULL)
	{
		$this->db->select('*')->from('u2_meta_field')->where('u2_cate_id','0');

		$data['all'] = lazy_get_data();

		if($id)
		{
			$this->db->select('*')->from('u2_meta_field')->where('u2_cate_id',$id);
			$data['cate'] = lazy_get_data();
		}
		return $data;
	}

	function update_data_extra($info)
	{
		if($info)
		{
			foreach($info as $k => $v)
			{
				if( strpos($k,'extra_name_') !== false )
				{
					if($v)
					{
						$id = str_replace('extra_name_','',$k);
						$data['u2_cn_name'] = $v;
						$data['u2_desp'] = isset($info['extra_desp_'.$id])?$info['extra_desp_'.$id]:NULL;

						$this->db->where('id',$id);
						$this->db->update('u2_meta_field',$data);
					}
				}
			}
		}
	}
	
	function get_user_by_uid($uid = NULL)
	{
		$uid = format_uid($uid);
		$this->db->select('*')->from('u2_user')->where('id',$uid)->limit('1');
		return lazy_get_line();
	}
	function get_recharge_card_num()
	{
		$this->db->select('count(*)')->from('u2_recharge_card');

		return lazy_get_var();
	}
	function get_recharge_card($start,$limit)
	{
		$this->db->select('*')->from('u2_recharge_card')->orderby('id','desc')->limit($limit, $start );

		return lazy_get_data();
	}
	function make_card( $number )
	{
		$data = array();
		$data['u2_is_use'] = '0';
		$data['u2_is_copied'] = '0';
		$data['u2_date'] = date("Y-m-d");
		while( $number > 0 )
		{
			$data['u2_card_no'] = newpassword();
			$this->db->insert( 'u2_recharge_card' , $data );
			$number--;
		}
	}
	function get_app_info_by_folder( $folder )
	{
		$this->db->select('*')->from('u2_app')->where('u2_folder',$folder)->limit('1');
		return lazy_get_line();
	}
	function save_appsetting($folder)
	{
		$key = array('u2_left_nav');
		foreach( $key as $v )
		{
			$data[$v] = $_POST[$v];
		}
		$this->db->where('u2_folder',$folder)->limit('1');
		$this->db->update( 'u2_app' ,$data );
	}
	function pro_list( $search ,$start,$limit )
	{

		$this->db->select('sql_calc_found_rows *')->from('u2_content')->where('u2_is_active',1)->orderby('id','DESC')->limit($limit,$start);

		if( $search )
		{
			$search_sql = "`u2_title` LIKE '%$search%'";

			$this->db->where($search_sql);	
		}
		
		return lazy_get_data();
	}
	function iticket_list( $search ,$start,$limit )
	{

		$this->db->select('sql_calc_found_rows *')->from('app_iticket_items')->orderby('id','DESC')->limit($limit,$start);

		if( $search )
		{
			$search_sql = " title LIKE '%$search%'";

			$this->db->where($search_sql);	
		}
		
		return lazy_get_data();
	}
	function snotice_list($start,$limit )
	{
		$this->db->select('sql_calc_found_rows *')->from('u2_snotice')->orderby('id','DESC')->limit($limit,$start);
		
		return lazy_get_data();
	}
	function snotice_save( $data )
	{
		$this->db->insert( 'u2_snotice' , $data );	
	}
	function snotice_del( $id )
	{
		$this->db->where('id', $id )->limit(1);
		$this->db->delete( 'u2_snotice');	
	}
	function get_snotice_by_id( $id )
	{
		$this->db->select('*')->from('u2_snotice')->where('id', $id )->limit(1);
		return lazy_get_line();
	}
	function update_snotice( $data , $id )
	{
		$this->db->where('id', $id )->limit(1);
		$this->db->update( 'u2_snotice' , $data );

	}
	function get_update_max_id()
	{
		$this->db->select('*')->from('u2_upgrade')->where('is_max', '1' )->limit(1);
		
		$line = lazy_get_line();

		if( $line )
		{
			return $line['sid'];
		}
		else
		{
			$data = array();
			$data['sid'] = 0;
			$data['is_max'] = 1;
			$this->db->insert('u2_upgrade', $data);
			return 0;
		}
	}
	function get_update_list()
	{
		$this->db->select('*')->from('u2_upgrade')->where('is_max', '0' );
		$data = lazy_get_data();
		$list = array();
		if( is_array($data) && $data )
		{
			$app_list = array();
			foreach ($data as $v)
			{
				if ( $v['sid'] )
				{
					$key = intval($v['sid']);
					$list[$key]=$key;
				}
			}
		}
		return $list;
	}
	function update_max_update_id( $id )
	{
		$this->db->where('is_max', '1' )->limit(1);
		$data['sid'] = $id;
		$this->db->update( 'u2_upgrade' , $data );
	}
	function add_update_id( $ids )
	{
		if( is_array( $ids ) && $ids )
		{
			foreach( $ids as $id )
			{
				$data = array();
				$data['sid'] = intval($id);
				$data['is_max'] = 0;
				$this->db->insert('u2_upgrade', $data);
			}
		}
	}
	function del_update_id( $ids )
	{
		if( !is_array($ids) )
		{
			$ids = array( $ids );
		}
		$del_ids = array();
		foreach( $ids as $id )
		{
			if( $id )
			{
				$id = intval($id);
				$del_ids[] = $id;
			}
		}
		if($del_ids)
		{
			$this->db->where(" `sid` IN(".join(',',$del_ids).") ")->where('is_max', '0' );
			$this->db->delete( 'u2_upgrade');
		}
		
	}
	function get_ip_count()
	{
		$this->db->select('*')->from('u2_statistics_res');
		$data = lazy_get_data();
		if( $data )
		{
			foreach( $data as $v )
			{
				$array[$v['key']] = $v['value'];
			}
			if( isset( $array['ip_date'] ) && $array['ip_date'] == date("Y-m-d") )
			{
				return $array['ip_count'];
			}
		}
		return 0;
	}
	function get_ave_online()
	{
		$this->db->select("AVG(`online_today`)")->from('u2_user')->where('online_date',date("Y-m-d") )->where(' `online_today` > 0 ');
		return lazy_get_var();
	}
	function get_pm_count_today()
	{
		$this->db->select("count(*)")->from('u2_pm')->where(" `time` > '".date("Y-m-d").' 00:00:00'."'" );
		return lazy_get_var();
	}
	function get_feed_count_today()
	{
		$this->db->select("count(*)")->from('u2_mini_feed')->where(" `u2_time` > '".date("Y-m-d").' 00:00:00'."'" );
		return lazy_get_var();
	}
	function do_karma( $karma , $uid , $type )
	{
		$sql = "SHOW TABLES LIKE 'app_ibank_account' ";
		if( lazy_get_var($sql) )
		{
			$uid = intval( $uid );
			$karma = intval( $karma );
			$key = $type == 'gold'?'gold_count':'g_count';
			$line = lazy_get_line("select * from `app_ibank_account` where `uid` = '$uid' limit 1");
			if( $karma > 0 )
			{
				if( $line )
				{
					$sql = "update `app_ibank_account` set `$key` = `$key` + $karma where `id` = '{$line['id']}' limit 1 ";
				}
				else
				{
					$g_count = $key=='g_count'?$karma:0;
					$gold_count = $key=='gold_count'?$karma:0;
					$sql = "insert into `app_ibank_account` ( `uid` , `g_count` , `gold_count` )values('$uid','$g_count','$gold_count')";
				}
			}
			else
			{
				if( $line )
				{
					$check = $line[$key] + $karma ;
					if( $check > 0 )
					{
						$sql = "update `app_ibank_account` set `$key` = `$key` + $karma where `id` = '{$line['id']}' limit 1 ";
					}
					else
					{
						$sql = "update `app_ibank_account` set `$key` 0 where `id` = '{$line['id']}' limit 1 ";
					}
				
				}
				else
				{
					return;
				}
			
			}
			lazy_run_sql( $sql );
		}
	}
	function save_record( $data )
	{
		$this->db->insert('u2_karma_record',$data);
	}
	function get_shop_types()
	{
		$this->db->select('*')->from('u2_shop_type')->orderby('orders','ASC');
		return  lazy_get_data();
	}
	function get_shop_type_by_id($id)
	{
		$this->db->select('*')->from('u2_shop_type')->where('id',$id)->limit(1);
		return  lazy_get_line();
	}
	function save_shop_type( $data )
	{
		$this->db->select('MAX(`orders`)')->from('u2_shop_type');
		$data['orders'] =intval( lazy_get_var() ) + 1;
		$this->db->insert('u2_shop_type', $data );	
		return $this->db->insert_id();
	}
	function update_shop_type( $data , $id )
	{
		$this->db->where('id',$id);
		$this->db->update('u2_shop_type',$data);
	}
	function check_shop_type( $name )
	{
		$this->db->select('count(*)')->from('u2_shop_type')->where('name',$name);
		return  lazy_get_var();
	}
	function get_shop_brands()
	{
		$this->db->select('*')->from('u2_shop_brands')->orderby('orders','ASC');
		return  lazy_get_data();
	}
	function check_shop_brand( $name )
	{
		$this->db->select('count(*)')->from('u2_shop_brands')->where('name',$name);
		return  lazy_get_var();
	}
	function save_shop_brand( $data )
	{
		$this->db->select('MAX(`orders`)')->from('u2_shop_brands');
		$data['orders'] =intval( lazy_get_var() ) + 1;
		$this->db->insert('u2_shop_brands', $data );	
	}
	function get_shop_item_by_id( $id )
	{
		$this->db->select('*')->from('u2_shop_items')->where('id',$id)->limit(1);
		return  lazy_get_line();
	}
	function save_shop_item( $id )
	{
		$time = date("Y-m-d H:i:s");
		$extra = v('extra');
		$data['name'] = z(v('name'));
		$data['number'] = z(v('number'));
		$data['brands'] = intval(v('brands'));
		$data['unit'] = z(v('unit'));
		$data['weight'] = intval(v('weight'));
		$data['price'] = floatval(v('price'));
		$data['market_price'] = floatval(v('market_price'));
		$data['pro_price'] = floatval(v('pro_price'));
		$data['is_pro'] = intval(v('is_pro'));
		$data['`leave`'] = intval(v('leave'));
		$data['alarm'] = intval(v('alarm'));
		$data['new'] = intval(v('new'));
		$data['good'] = intval(v('good'));
		$data['hot'] = intval(v('hot'));
		$data['cate'] = intval(v('cate'));
		$data['type'] = intval(v('type'));
		$data['pic'] = z(v('pic'));
		$data['desp'] =x(v('desp'));
		$data['is_active'] =intval(v('is_active')) == 1?1:0;
		$data['time'] = $time;
		$this->db->where('id',$id);
		$this->db->update('u2_shop_items', $data );
		$is_active = $data['is_active'];
		$type = $data['type'];
		if( $extra && $type )
		{
			$data = array();
			foreach( $extra as $k => $v )
			{
				$k = intval( $k );
				if( $k > 0 )
				{
					if( is_array( $v ) )
					{
						$data['extra_'.$k] = serialize( $v );
					}
					else
					{
						$data['extra_'.$k] = z( $v );
					}
				}
			}
			if( $data )
			{
				$this->db->select('count(*)')->from('shop_extra_'.$type)->where('cid', $id);
				if( lazy_get_var() )
				{
					$this->db->where('cid', $id);
					$this->db->update('shop_extra_'.$type , $data );
				}
				else
				{
					$data['cid'] = $id;
					$this->db->insert('shop_extra_'.$type , $data );
				}
			}
		}
	
		$data = array();
		$data['is_active'] = $is_active;
		$this->db->where('cid', $id);
		$this->db->update('u2_shop_relate_tags', $data );
		
		return $time;

	}
	function get_shop_brand_by_id($id)
	{
		$this->db->select('*')->from('u2_shop_brands')->where('id',$id)->limit(1);
		return  lazy_get_line();
	}
	function update_shop_brand( $data , $id )
	{
		$this->db->where('id',$id);
		$this->db->update('u2_shop_brands',$data);
	}
	function get_shop_cates()
	{
		$all_cate = lazy_get_data("select * from `u2_shop_cate` ", 'id');
		$cates = array();
		if( $all_cate )
		{
			foreach( $all_cate as $v )
			{
				$parent[$v['pid']][$v['id']] =  $v['orders'];
			}
			$ids = format_cate_order( 0 , $parent );
			foreach( $ids as $v )
			{
				$cates[$v['id']] = $all_cate[$v['id']];
				$cates[$v['id']]['root'] = $v['root'];
			}
		}
		return $cates;
	}
	function get_shop_cate_by_id($id)
	{
		$this->db->select('*')->from('u2_shop_cate')->where('id',$id)->limit(1);
		return  lazy_get_line();
	}
	function save_shop_cate( $data )
	{
		$this->db->select('MAX(`orders`)')->from('u2_shop_cate')->where('pid',$data['pid']);
		$data['orders'] =intval( lazy_get_var() ) + 1;
		$this->db->insert('u2_shop_cate', $data );	
	}
	function update_shop_cate( $data , $id )
	{
		$this->db->where('id',$id);
		$this->db->update('u2_shop_cate',$data);
	}
	function get_shop_draft_item()
	{
		$uid = format_uid();
		$this->db->select('*')->from('u2_shop_items')->where('is_active','0')->where('uid',$uid)->limit(1);
		$line = lazy_get_line();
		if( !$line )
		{
			$data = array();
			$data['is_active'] = 0 ;
			$data['uid'] = $uid;
			$data['time'] = date("Y-m-d H:i:s");
			$this->db->insert( 'u2_shop_items' , $data );
			//$data['id'] = $this->db->insert_id();
			$this->db->select('*')->from('u2_shop_items')->where('is_active','0')->where('uid',$uid)->limit(1);
			$line = lazy_get_line();
		}
		return $line;
	}
	function check_shopcate_install() 
	{
		$this->db->select('count(*)')->from('u2_app')->where('aid' , 'ishopcart')->limit(1);
		return lazy_get_var();
	}
	function get_shop_orders( $start = 0 , $limit = 10 )
	{
		$this->db->select('sql_calc_found_rows *')->from('app_shoporder')->orderby('id','desc')->limit($limit , $start );
		return lazy_get_data();
	}
}