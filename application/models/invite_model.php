<?php
class Invite_model extends Model 
{

    function Invite_model()
    {
        parent::Model();
    }
	function get_user_invite_num( $uid = NULL )
	{
		$uid = format_uid( $uid );

		$this->db->select('count(*)')->from('u2_invite')->where('u2_uid',$uid);

		return lazy_get_var();
	}
	function get_user_invite($start,$limit,$uid = NULL)
	{
		$uid = format_uid( $uid );

		$this->db->select('*')->from('u2_invite')->where('u2_uid',$uid)->orderby('id','desc')->limit($limit, $start );

		return lazy_get_data();
	}
	function get_invite_limit()
	{
		if( c('invite_active') )
		{
			$date = date("Y-m-d");
			$this->db->select('count(*)')->from('u2_invite')->where('u2_date',$date);

			return  intval(c('invite_limit'))-intval(lazy_get_var());

		}
	}
	function buy( $number , $uid = NULL )
	{
		$uid = format_uid($uid);
		$money = intval(c('invite_price'))*$number;
		$key = c('invite_use_gold')?'gold':'g';
		$this->db->select($key)->from('app_ihome_user')->where('uid',$uid)->limit(1);
		$now = lazy_get_var();
		if($now < $money )
		{
			return false;
		}
		$sql = "UPDATE app_ihome_user SET $key = $key - $money  WHERE uid = '$uid'";
		lazy_run_sql($sql);
		$data = array();
		$data['u2_uid'] = $uid;
		$data['u2_is_use'] = '0';
		$data['u2_is_copied'] = '0';
		$data['u2_date'] = date("Y-m-d");
		while( $number > 0 )
		{
			do
			{
				$icode = newpassword();
				$check = $this->count_invite_code( $icode );
			}
			while( $check);
			
			$data['u2_invite_code'] = $icode;
			$this->db->insert( 'u2_invite' , $data );
			$number--;
		}
		return true;
	}
	function count_invite_code( $code )
	{
		$this->db->select('count(*)')->from('u2_invite')->where('u2_invite_code',$code)->where('u2_is_use','0');
		return lazy_get_var();
	}
}

?>