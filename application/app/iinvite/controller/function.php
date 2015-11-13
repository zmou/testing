<?php

$tab_array = array
(
	'index' => '邀请好友',	
);
function app_get_emails(  $email , $psw , $type )
{

	$temp = explode('@' ,  $email );
	$name = array_shift( $temp );
	$domain = array_shift( $temp );
	if( $type == 'email' )
	{
			include_once( dirname( __FILE__ ) . '/lib/mailfactory.php'   );
			switch ( strtolower( $domain ) ) 
			{
				case "126.com":
					$contact = new MailFactory(M126);
					break;
				case "sina.com":
					$contact = new MailFactory(MSINA);
					break;
				case "tom.com":
					$contact = new MailFactory(MTOM);
					break;
				case "gmail.com":
					$contact = new MailFactory(MGOOGLE);
					break;
				case "163.com":
					$contact = new MailFactory(M163);
					break;
				case "sohu.com":
					$name = $email;
					$contact = new MailFactory(MSOHU);
					break;
				case "vip.sohu.com":
					$name = $email;
					$contact = new MailFactory(MSOHU_VIP);
					break;
				case "yahoo.cn":
				case "yahoo.com":
				case "yahoo.com.cn":
					$name = $email;
					$contact = new MailFactory(MYAHOO);
					break;
				default:
					die("error");
			}
		return $contact->getContactList($name ,$psw );

	}
	elseif( strtolower($type) == 'msn' )
	{
		include_once( dirname( __FILE__ ) . '/lib/msn.class.php'   );
		$return = array(); 
		$msn2 = new msn;
		$return_emails = $msn2->qGrab( $email , $psw);
	
		if( $return_emails && is_array($return_emails) )
		{
	
			foreach($return_emails as $v)
			{
				$return[] = $v[0];
			}
		}
		return $return;

	}
}
function app_save_mails( $mails )
{
	//return true;
	$uid = format_uid();
	global $CI;
	$CI->load->database();
	$CI->db->where('uid',$uid);
	$CI->db->delete('app_iinvite_emails');
	$data['uid'] = $uid;
	$data['no_in_site'] = '0';
	foreach( $mails as $m )
	{
		$data['email'] = $m;
		$CI->db->insert('app_iinvite_emails',$data);
	}
}
function get_friends_by_uid( $uid = NULL )
{
	$uid = format_uid( $uid );

	
	$where = "(`u2_uid1` = '".$uid."'  AND `is_active` = '1' )OR( `u2_uid2` = '".$uid."' AND `is_active` = '1')";
	$sql = "SELECT * FROM `u2_fans` where $where LIMIT 500 ";
	
	$fans = lazy_get_data( $sql ) ;
	
	$fid = array();
	
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
	
	return $fid;	
}
function app_checkemail($inAddress)
{
	return (preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/",$inAddress));
}
?>