<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

include_once( "contacts.class.php" );
include_once( "msn.class.php" );
class ContactsMsn extends Contacts
{

	public function checkLogin( $user, $password )
	{
		return true;
	}

	public function getContacts( $user, $password, &$result )
	{
		$msn = new msn( );
		if ( !$msn->connect( $user, $password ) )
		{
			return false;
		}
		$msn->rx_data( );
		$msn->process_emails( );
		$returned_emails = $msn->email_output;
		foreach ( $returned_emails as $value )
		{
			$result[$value[0]] = $value[1];
		}
		return true;
	}

}

?>
