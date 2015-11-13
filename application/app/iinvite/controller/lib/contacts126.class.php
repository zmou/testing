<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

include_once( "contacts.class.php" );
include_once( "xmlutil.class.php" );
class Contacts126 extends Contacts
{

	public function CheckLogin( $user, $password )
	{
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_URL, "https://entry.mail.126.com/cgi/login?redirTempName=https.htm&hid=10010102&lightweight=1&verifycookie=1&language=0&style=-1" );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "user=".$user."&pass=".$password."&domain=126.com" );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		if ( strpos( $contents, "登录出错" ) !== false )
		{
			return 0;
		}
		return 1;
	}

	public function GetContacts( $user, $password, &$result )
	{
		if ( !$this->CheckLogin( $user, $password ) )
		{
			return 0;
		}
		$bRet = $this->ReadCookies( COOKIEJAR, $cookies );
		$cookieid = substr( trim( $cookies['Coremail'] ), -32 );
		if ( !$cookieid )
		{
			return 0;
		}
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_URL, "http://g1a65.mail.126.com/a/s?sid=".$cookieid."&func=global:sequential" );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/xml" ) );
		$str = "<?xml version=\"1.0\"?><object><array name=\"items\"><object><string name=\"func\">pab:searchContacts</string><object name=\"var\"><array name=\"order\"><object><string name=\"field\">FN</string><boolean name=\"ignoreCase\">true</boolean></object></array></object></object><object><string name=\"func\">user:getSignatures</string></object><object><string name=\"func\">pab:getAllGroups</string></object></array></object>";
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $str );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
		//$pattern = "/([\\w_-])+@([\\w])+([\\w.]+)/";
		$pattern = "/([\\w\._\-])+@([\\w])+([\\w.]+)/";
		if ( preg_match_all( $pattern, $contents, $tmpres, PREG_PATTERN_ORDER ) )
		{
			$result = array_unique( $tmpres[0] );
		}
		return 1;
	}

}

?>
