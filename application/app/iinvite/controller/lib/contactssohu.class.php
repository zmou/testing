<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

include_once( "contacts.class.php" );
class ContactsSohu extends Contacts
{

	public function checkLogin( $user, $password )
	{
		$ch = curl_init( );
		$url = "http://passport.sohu.com/sso/login.jsp";
		$url = $url."?userid=".urlencode( $user );
		$url = $url."&password=".md5( $password );
		$url = $url."&appid=1000&persistentcookie=0&s=".time( )."&b=1&w=1024&pwdtype=1";
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		if ( strpos( $contents, "success" ) === false )
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
		$cookies = array( );
		$bRet = $this->readCookies( COOKIEJAR, $cookies );
		if ( !$bRet || !$cookies['JSESSIONID'] )
		{
			return 0;
		}
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		curl_setopt( $ch, CURLOPT_URL, "http://www51.mail.sohu.com/webapp/contact" );
		ob_start( );
		curl_exec( $ch );
		$content = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		$bRet = $this->_parseData( $content, $result );
		if ( !$bRet )
		{
			return 0;
		}
		return 1;
	}

	public function _parseData( $content, &$ar )
	{
		$ar = array( );
		if ( !$content )
		{
			return 0;
		}
		$data = json_decode( $content );
		unset( $content );
		foreach ( $data->listString as $value )
		{
			if ( !preg_match_all( "/[a-z0-9_\\.\\-]+@[a-z0-9\\-]+\\.[a-z]{2,6}/i", $value->email, $matches ) )
			{
				continue;
			}
			$emails = array_unique( $matches[0] );
			unset( $matches );
			foreach ( $emails as $email )
			{
				$ar[$email] = $value->name;
			}
		}
		return 1;
	}

}

?>
