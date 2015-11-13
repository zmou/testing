<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

include_once( "contacts.class.php" );
class ContactsTom extends Contacts
{

	public function checkLogin( $user, $password )
	{
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_URL, "http://login.mail.tom.com/cgi/login" );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		$fileds = "user={$user}&pass={$password}";
		$fileds .= "&style=0&verifycookie";
		$fileds .= "&type=0&url=http://bjweb.mail.tom.com/cgi/login2";
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $fileds );
		ob_start( );
		curl_exec( $ch );
		$result = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		if ( preg_match( "/warning|警告/", $result ) )
		{
			return 0;
		}
		return 1;
	}

	public function getContacts( $user, $password, &$result )
	{
		if ( !$this->checkLogin( $user, $password ) )
		{
			return 0;
		}
		$this->ReadCookies( COOKIEJAR, $res );
		if ( $res['Coremail'] == "" )
		{
			return 0;
		}
		$sid = substr( trim( $res['Coremail'] ), -16 );
		$url = "http://bjapp2.mail.tom.com/cgi/ldvcapp";
		$url .= "?funcid=address&sid={$sid}&showlist=all&listnum=0";
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIEJAR );
		ob_start( );
		curl_exec( $ch );
		$res = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		$pattern = "/([\\w_-])+@([\\w])+([\\w.]+)/";
		if ( preg_match_all( $pattern, $res, $tmpres, PREG_PATTERN_ORDER ) )
		{
			$result = array_unique( $tmpres[0] );
		}
		return 1;
	}

}

?>
