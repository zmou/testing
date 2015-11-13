<?php
class User_model extends Model 
{

    function User_model()
    {
        parent::Model();
    }
	function load_user_information_by_uid($uid = NULL )
	{
		$uid = format_uid($uid);
		if(!$uid)
			return;

		$this->db->select('*')->from('u2_user')->where('id',$uid)->limit(1);
		
		return  lazy_get_line();
	}
	function load_user_photos_by_uid($uid = NULL)
	{
		$uid = format_uid($uid);

		$this->db->select('*')->from('u2_user_pic')->where('u2_uid',$uid)->orderby('u2_time','DESC')->limit(5);
		
		return  lazy_get_data();
	}
	
	function get_nickname_by_uid( $uid )
	{
		$this->db->select('u2_nickname')->from('u2_user')->where('id' , intval($uid))->limit(1);
		return lazy_get_var();
	}

	function login_confirm($mail,$psw , $new = NULL)
	{
		if( strlen( $psw ) < 20 ) $psw = md5($psw);
		
		$this->db->select('*')->from('u2_user')->where("LCASE(`u2_email`)" , strtolower($mail))->where( "u2_password" , $psw) ->limit(1);
		
		$userinfo = lazy_get_line();
		if(  $userinfo['id'] && $userinfo['u2_level'] == 0 )
		{
			info_page( _text('system_user_locked') );
			die();
		}
		
		if(is_array($userinfo))
		{
			$this->load->library('session');
			if( $new != NULL )
			{
				$userinfo = array_merge( $userinfo , $new );
			}
			$this->session->set_userdata($userinfo);
			
			
			return true;
		}
		return false;
	}
	function register_save($email,$nickname,$psw)
	{
		$this->db->select('count(*)')->from('u2_user')->where(" LCASE(u2_email)= '".strtolower($email)."'" )->orwhere('u2_nickname',$nickname);

		if(lazy_get_var())
		{
			return false;
		}
		$data = array();
		$data['u2_joindate'] = date("Y-m-d");
		$data['u2_pincode'] = md5( rand( 1 , 10000000 ) );
		$data['u2_level'] = 1;
		$data['u2_nickname'] = $nickname;
		$data['u2_password'] = md5($psw);
		$data['u2_email'] = $email;
		
		$this->db->insert('u2_user', $data);
		
		return true;
	}
	function resetpass_by_uid($pass,$uid = NULL)
	{
		$uid = format_uid($uid);
		$data['u2_password'] = md5($pass);
		$this->db->where( 'id' , $uid );
		$this->db->update('u2_user', $data);
	}
	function add_user_upload_pic($name,$uid = NULL)
	{
		$uid = format_uid($uid);

		$data = array();
		$data['u2_uid'] = $uid;
		$data['u2_pid'] = 0;
		$data['u2_pic_name'] = $name;
		$data['u2_time'] = date("Y-m-d H:i:s");
		$data['u2_is_active'] = 1;
		
		$this->db->insert('u2_user_pic', $data);
	}
	function get_frinds_ids_by_uid($uid = NULL)
	{
		$uid = format_uid($uid);
	
		$where = "(`u2_uid1` = '".$uid."'  AND `is_active` = '1' )OR( `u2_uid2` = '".$uid."' AND `is_active` = '1')";

		$this->db->select('*')->from('u2_fans')->where($where);
		
		$fans = lazy_get_data();
		
		$fid = NULL;
		
		if( isset($fans[0]) && is_array($fans[0]))
		{
			foreach( $fans as $f )
			{
				if($f['u2_uid1'] == $uid)
				{
					$fid[] = $f['u2_uid2'];
				}
				else
				{
					$fid[] = $f['u2_uid1'];
				}
			}
		}
		
		if(is_array($fid))
		{
			return $fid;
		}
	}
	function get_users_by_ids($fid,$search = NULL , $start = NULL )
	{
		if(is_array($fid))
		{
			$ids = join(',',$fid);
			
			$where = "`id` IN(".$ids.")";

			$this->db->select('sql_calc_found_rows *')->from('u2_user')->where($where);

			if($search != NULL)
			{
				
				$search = strtolower( $search );
				$search_sql = "LCASE(`u2_nickname`) LIKE " . s( '%' . $search . '%') . "";

				$this->db->where($search_sql);
			}
			if( $start !== NULL)
			{
				$this->db->limit(intval(c('user_per_page')), $start );
			}
			return lazy_get_data();
		}
	}
	function load_friends_minifeed( $only , $uid=NULL , $start = 0 )
	{
		if($only == 'friend')
		{
			$fids = $this->get_frinds_ids_by_uid($uid);
			
			if( is_array( $fids ) )
				$ids = join(',',$fids);
			else
				return false;	
			
			$where = "`u2_uid` IN(".$ids.")";

			$this->db->where($where);		
		}
		
		/*
		if(_sess('u2_miniblog'))
		{
			$type = unserialize( _sess('u2_miniblog') );
			$types = join(',',$type);
			$where = "`u2_type` IN(".$types.")";
			$this->db->where($where);
		}
		*/
		
		$this->db->select('sql_calc_found_rows *')->from('u2_mini_feed')->orderby('u2_time','DESC')->orderby('id','DESC')->limit('10' , $start );
		
		
			return lazy_get_data();
	}
	function get_user_message_num_by_uid( $box , $uid = NULL )
	{
		$uid = format_uid($uid);
	
		if($box == 'inbox')
		{
			$where = "`u2_sid` = '$uid' AND `u2_s_del`= '0' ";
		}
		else
		{
			$where = "`u2_uid` = '$uid' AND `u2_u_del`= '0' ";
		}
		
		$this->db->select('count(*)')->from('u2_pm')->orderby('id','DESC')->where($where);
		
		return lazy_get_var();
	}
	function load_user_message_by_uid($box='inbox', $start, $limit ,$uid = NULL)
	{
		$uid = format_uid($uid);
	
		if($box == 'inbox')
		{
			$where = "`u2_sid` = '$uid' AND `u2_s_del`= '0' ";
		}
		else
		{
			$where = "`u2_uid` = '$uid' AND `u2_u_del`= '0' ";
		}
		
		$this->db->select('*')->from('u2_pm')->orderby('id','DESC')->where($where)->limit($limit, $start );
		//$this->groupby('u2_uid');
		$data = lazy_get_data();
		
		if( $box == 'inbox' )
		{
			$this->db->where('u2_is_read' , 0 )->where('u2_sid' , $uid );
			$this->db->update( 'u2_pm' , array('u2_is_read'=>1) );
		}
		
		return $data;

	}
	function del_message($box ,$id )
	{
		$uid = format_uid();

		$message = $this->get_user_pm_by_id( $id);

		$this->db->where('id',$id);

		if($message['u2_uid'] == $uid && $box == 'sendbox' )
		{
			if ($message['u2_s_del'] == 0 )
			{
				$data['u2_u_del'] = 1;
			}
			else
			{
				$this->db->delete('u2_pm');
			}
		}
		elseif($message['u2_sid'] == $uid && $box == 'inbox' )
		{
			if ($message['u2_u_del'] == 0)
			{
				$data['u2_s_del'] = 1;
			}
			else
			{
				$this->db->delete('u2_pm');
			}
		}
		if($data)
		{
			$this->db->update('u2_pm', $data);
			
			return true;
		}
		else
		{
			return false;
		}
	}
	function get_user_pm_by_id($id)
	{
		$this->db->select('*')->from('u2_pm')->where('id',$id)->limit(1);

		return lazy_get_line();
	}
	function save_message($title,$info,$sid,$sname)
	{
		$uid = format_uid();
		
		$uname = _sess('u2_nickname');
		$data['u2_pm_title'] = $title;
		$data['u2_pm_info'] = $info;
		$data['u2_uid'] = $uid;
		$data['u2_sid'] = $sid;
		$data['uname'] = $uname;
		$data['sname'] = $sname;
		$data['u2_is_read'] = 0 ;
		$data['u2_u_del'] = 0 ;
		$data['u2_s_del'] = 0 ;
		$data['time'] = date("Y-m-d H:i:s");
		$this->db->insert('u2_pm', $data);
	}
	function check_uid($uid)
	{
		$this->db->select('count(*)')->from('u2_user')->where('id',$uid);

		if(!lazy_get_var())
		{
			info_page( _text('user_error_login_bad_uid'));
			die();
		}
	}
	
	function get_chat_history( $buddy )
	{
		$uid = format_uid();
		$this->db->select('*')->from('u2_pm')->limit(20);
		$where = " `u2_u_del` != 1 AND  ( `u2_uid` = '" . $uid . "' AND `u2_sid` = '" . $buddy . "' ) OR ( `u2_uid` = '" . $buddy . "' AND `u2_sid` = '" . $uid  . "' )  ";
		$this->db->where( $where )->orderby( 'time' , 'desc' );
		
		return lazy_get_data();
	}
	
	function get_notice_by_uid( $uid , $start , $limit , $mark  )
	{
		$uid = format_uid( $uid );
		$this->db->select('sql_calc_found_rows *')->from('u2_notice')->where('u2_uid' , $uid)->orderby('id','DESC')->limit($limit , $start );
		$data = lazy_get_data();
		save_count();
		// update all message to read
		if( $mark )
		{
			$this->db->where('u2_is_read' , 0 )->where('u2_uid' , $uid );
			$this->db->update( 'u2_notice' , array('u2_is_read'=>1) );
		}
		return $data;
	}
	
	
	function friend_doaction($action , $fid)
	{
		$uid = format_uid();
		if($uid == $fid)
			return 'noself';

		$where = "(`u2_uid1` = '".$uid."'  AND `u2_uid2` = '".$fid."' )OR( `u2_uid2` = '".$uid."'  AND `u2_uid1` = '".$fid."')";

		if($action == 'add')
		{
			$this->db->select('*')->from('u2_fans')->where($where)->limit(1);
			$finfo = lazy_get_line();
			if($finfo)
			{
				if($finfo['u2_uid1'] == $uid)
					return 'wait';

				$data['is_active'] = 1 ;
				$this->db->where($where);
				$this->db->update('u2_fans',$data);
				return 'accept';
			}
			else
			{
				$data['is_active'] = 0 ;
				$data['u2_uid1'] = $uid ;
				$data['u2_uid2'] = $fid ;
				$this->db->insert('u2_fans',$data);
				return 'wait';
			}
		}
		elseif($action == 'del')
		{
			$this->db->where($where);
			$this->db->delete('u2_fans');
			return NULL;
		}

		
	}
	function get_user_staus($uid = NULL)
	{
		$id = format_uid($uid);

		$this->db->select('u2_desp')->from('u2_user')->where('id',$id)->limit('1');

		return lazy_get_var();
	}
	
	function save_user_staus($status ,$uid = NULL)
	{
		$id = format_uid($uid);
		$this->db->where('id',$id);
		$data['u2_desp'] = $status;

		$this->db->update('u2_user',$data);
	}
	
	function save_user_profile( $uid = NULL )
	{
		$id = format_uid($uid);
	
		if( z(v('nick_name')) != _sess('u2_nickname') )
		{
			$check = lazy_get_var("select count(*) from u2_user where LCASE(u2_nickname) = '".strtolower( z(v('nick_name')) )."' and id != '$id' ");
			if($check)
			{
				return false;
			}
		}
		$this->db->where('id',$id);
		$data = array();	
		$data['u2_nickname'] = z(v('nick_name'));
		$data['u2_true_name'] = z(v('true_name'));
		$data['u2_sex'] = z(v('sex'));
		$data['u2_msn'] = z(v('msn'));
		$data['u2_qq'] = z(v('qq'));
		$data['u2_mobile'] = z(v('mobile'));
		$data['u2_city'] = z(v('city'));
		$data['u2_address'] = z(v('address'));
		$data['u2_zipcode'] = z(v('zipcode'));
		
		//$data['u2_desp'] = v('status');

		$this->db->update('u2_user',$data);

		set_sess($data);
		return true;
	}
	
	function del_user_pic($id , $uid = NULL)
	{
		$uid = format_uid($uid);
		$this->db->select('u2_pic_name')->from('u2_user_pic')->where('id',$id)->where('u2_uid',$uid)->limit('1');
		$name = lazy_get_var();
		if($name)
		{
			$this->db->where('id',$id);
			$this->db->delete('u2_user_pic');
		}
		return $name;
	}
	function get_online_user_ids_by_ids($ids )
	{
		if(!$ids)
		{
			return '0';
		}
		$seconds = intval( $this->config->item('online_seconds') );

		if(intval($seconds) < 300 )
		{
			$seconds = 300;
		}
		$time = date( "Y-m-d H:i:s" , strtotime("-$seconds seconds") );

		$uids = join(',',$ids);
			
		$where = "`u2_uid` IN(".$uids.")";
		
		$this->db->select('*')->from('u2_online')->where($where)->where('u2_stay_time >',$time);

		$onlines = lazy_get_data();

		if($onlines)
		{
			foreach($onlines as $o)
			{
				$oids = $o['u2_uid'];
			}
			return $oids;

		}

	}
	function get_online_by_ids( $ids , $action = NULL )
	{
		$data = NULL;
		if(is_array($ids))
		{
			$uids = join(',',$ids);
			
			$where = "`id` IN(".$uids.")";

			$this->db->select('*')->from('u2_user')->where($where);

			$users = lazy_get_data();

			$where = "`u2_uid` IN(".$uids.")";

			$this->db->select('*')->from('u2_online')->where($where);

			$u2_online = lazy_get_data();
			if($u2_online)
			{
				foreach($u2_online as $o)
				{
					$online[$o['u2_uid']] = $o;
				}
			}
			if($users)
			{
				$seconds = intval( $this->config->item('online_seconds') );
	
				if(intval($seconds) < 300 )
				{
					$seconds = 300;
				}
				$time = date( "Y-m-d H:i:s" , strtotime("-$seconds seconds") );
				foreach($users as $u)
				{
					if(isset( $online[$u['id']]['u2_stay_time'] ) && $online[$u['id']]['u2_stay_time'] > $time)
					{
						$data['online'][] = $u;
					}
					else
					{
						$data['offline'][] = $u;
					}
				}
			}

			if($action == NULL )
			{
				return $data;
			}
			else
			{
				if(isset($data[$action]))
				{
					return $data[$action];
				}
				else
				{
					return NULL;
				}
			
			}
		}
	}
	
	function get_friend_request( $uid )
	{
		$this->db->select('*')->from('u2_fans')->where( 'u2_uid2' , $uid )->where( 'is_active' , 0 );
		return lazy_get_data();
	}
	
	function get_user_info_by_array( $array , $key )
	{
		$data = array();
		
		
		
		if( is_array( $array ) )
		{
			$ids = array();
			foreach( $array as $r )
			{
				if( isset( $r[$key] ) )
					$ids[] = $r[$key];
			}
			
			if( isset( $ids[0] ) )
			{
				$this->db->select('*')->from('u2_user')->where( ' id IN ( ' . join( ',' , $ids ) . ' ) ' );
				$data = lazy_get_data();
				
				if( is_array( $data ) )
				{
					$ret = array();
					foreach( $data as $item )
					{
						$ret[$item['id']] = $item;
					}
					
					return $ret;
				}
			}
		}
		
		return false;
	}
	
	function save_blogset($type)
	{
		$uid = format_uid();
		$data['u2_miniblog'] = serialize($type);
		$this->db->where('id',$uid);
		$this->db->update('u2_user',$data);
		set_sess($data);
	}

	function get_online_peoples(  $limit = 10 , $start = 0  )
	{
		$start = intval( $start );
		$limit = intval(  $limit );
		$seconds = intval( $this->config->item('online_seconds') );
	
		if(intval($seconds) < 300 )
		{
			$seconds = 300;
		}
		$time = date( "Y-m-d H:i:s" , strtotime("-$seconds seconds") );
		
		$this->db->select('sql_calc_found_rows u2_uid')->from('u2_online')->where('u2_stay_time >' ,$time);
		if( $limit > 0 )
		{
			$this->db->limit( $limit , $start );
		}
		$oids = lazy_get_data();

		save_count();

		if(!$oids)
		{
			return NULL;
		}
		foreach($oids as $o)
		{
			$ids[] = $o['u2_uid'];
		}
		$users = $this->get_users_by_ids($ids);
		return $users;
	}

	function get_user_minifeeds_by_uid($uid)
	{
		$this->db->select('*')->from('u2_mini_feed')->where('u2_uid',$uid)->orderby('id','DESC')->limit( c('space_minifeed_per_page') );
		return lazy_get_data();
	}

	function save_space_hit($id)
	{
		$sql = " UPDATE u2_user SET u2_space_hit = u2_space_hit + 1  WHERE id = $id ";
		$this->db->query($sql);
	}

	function get_last_user_by($ids)
	{
		if($ids)
		{
			$data = NULL;
			$uids = join(',',$ids);
			$this->db->select('u2_uid')->from('u2_online')->where("`u2_uid` IN(".$uids.")" )->orderby('u2_stay_time','DESC');
			$onlines = lazy_get_data();
			$this->db->select('*')->from('u2_user')->where("`id` IN(".$uids.")" );
			$user = lazy_get_data();
			if(!$user || !$onlines)
			{
				return;
			}
			foreach($user as $u)
			{
				$users[$u['id']] = $u;
			}
			if($onlines)
			{
				foreach($onlines as $o)
				{
					$data[] = $users[$o['u2_uid']];
				}
			}

			return $data;
			
		}
	}

	function save_view_visitor( $uid , $vid )
	{
		$sql = "REPLACE INTO `u2_space_visit` (`u2_uid`,`u2_vid`,`u2_stay_time`)VALUES('".intval($uid)."','".intval($vid)."','".date( "Y-m-d H:i:s")."') ";
		$this->db->query($sql);
	}

	function get_view_vistor($uid)
	{
		$this->db->select('u2_vid')->from('u2_space_visit')->where('u2_uid',$uid)->orderby('u2_stay_time','DESC');
		$vid = lazy_get_data();
		if(!$vid)
		{
			return;
		}
		foreach($vid as $v)
		{
			$ids[] = $v['u2_vid'];
		}
		return $ids;
	}

	function get_users($start , $searchtext = NULL )
	{
		$limit = intval(c('user_per_page'));
		$this->db->select('sql_calc_found_rows *')->from('u2_user')->limit($limit,$start);
		if($searchtext)
		{
			$this->db->like('LCASE(u2_nickname)', strtolower($searchtext) );
			$this->db->orlike('LCASE(u2_email)',strtolower($searchtext) );
		}
		return lazy_get_data();
	}

	function get_user_num()
	{
		$this->db->select('count(*)')->from('u2_user');
		return lazy_get_var();
	}
	
	function update_friend_request( $user , $buddy , $to )
	{
		$this->db->where( 'u2_uid2' , $user );
		$this->db->where( 'u2_uid1' , $buddy );
		
		$data['is_active'] = intval( $to );
		$this->db->update('u2_fans',$data);
	}
	
	function get_user_by_email( $email )
	{
		$this->db->select('*')->from('u2_user')->where('u2_email' , $email)->limit(1);
		return lazy_get_line();
	}
	
	function remove_minifeed( $mid )
	{
		$this->db->where('id',$mid)->where( 'u2_uid' , format_uid() )->limit(1);
		$this->db->delete('u2_mini_feed');
	}
	
	
	function remove_wall( $wid )
	{
		$this->db->where('id', $wid )->where( 'u2_uid' , format_uid() )->limit(1);
		$this->db->delete('u2_wall');
	}

	function wall_save( $uid , $buddy , $content )
	{
		$data = array();
		$data['u2_uid'] = $uid ;
		$data['u2_guest_uid'] = format_uid( $buddy );
		$data['u2_content'] = $content;
		$data['u2_time'] = date("Y-m-d H:i:s");
		
		$this->db->insert( 'u2_wall' , $data ); 
		
	}
	
	function wall_get_by_uid( $uid )
	{
		$ret = array();
		$this->db->select('*')->from('u2_wall')->where('u2_uid' , $uid )->orderby('u2_time' , 'desc')->limit(30);
		$ret['wall'] = lazy_get_data();
		$ret['user'] = $this->get_user_info_by_array( $ret['wall'] , 'u2_guest_uid' );
		return $ret;
	}
	function check_invite_code( $code )
	{
		$this->db->select('*')->from('u2_invite')->where('u2_invite_code',$code)->where('u2_is_use','0')->limit('1');
		return lazy_get_line();
	}
	function marked_invite_code( $id )
	{
		$data['u2_is_use'] = '1';
		$this->db->where('id',$id);
		$this->db->update('u2_invite',$data);
	}
	function add_friend( $uid , $fid )
	{
		$data['is_active'] = 1 ;
		$data['u2_uid1'] = $uid ;
		$data['u2_uid2'] = $fid ;
		$this->db->insert('u2_fans',$data);
	}
	function new_pincode( $uid  = NULL )
	{
		$pincode = newpassword();
		$id = format_uid($uid);
		$data['u2_pincode'] = $pincode;
		$this->db->where('id',$id);
		$this->db->update('u2_user',$data);
		return $pincode;

	}
	function check_pincode($uid , $pincode )
	{
		$id = format_uid($uid);
		$this->db->select('count(*)')->from('u2_user')->where('id', $id )->where('u2_pincode',$pincode);
		return lazy_get_var();
	}
	function get_user_info_by_email($email )
	{
		if( !$email )
		{
			return false;
		}
		$this->db->select('*')->from('u2_user')->where("LCASE(u2_email) = '".strtolower($email)."'")->limit('1');

		return lazy_get_line();
	}
}

?>