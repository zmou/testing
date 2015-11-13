<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	var $CI;
	var $now;
	var $encryption		= TRUE;
	var $use_database	= FALSE;
	var $session_table	= FALSE;
	var $sess_length	= 7200;
	var $sess_cookie	= 'ci_session';
	var $userdata		= array();
	var $gc_probability	= 5;


	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */		
	function CI_Session()
	{
		$session_path = ROOT.'sessions/';
		$session_time = 60*60*24*3;
		if( rand(1 , 100) == 10 ) $this->session_gc( $session_path , $session_time );
		session_set_cookie_params( $session_time );
		session_save_path( $session_path );
		session_start();
		$_SESSION['session_id'] = session_id();
	}
	function session_gc( $path , $time )
	{
		foreach( glob( $path .'/sess_*' ) as $sfile )
			if(  (time() - filemtime( $sfile )) > $time  ) @unlink( $sfile );
	}
	// --------------------------------------------------------------------
	
	/**
	 * Run the session routines
	 *
	 * @access	public
	 * @return	void
	 */		
	function sess_run()
	{
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_read()
	{	
		// Fetch the cookie
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{								
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{	
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{	
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		session_destroy();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Garbage collection
	 *
	 * This deletes expired session rows from database
	 * if the probability percentage is met
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_gc()
	{
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a specific item form  the session array
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function userdata($item)
	{
		if( isset( $_SESSION[$item] ) )
			return $_SESSION[$item];
		else
			return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add or change data in the "userdata" array
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */		
	function set_userdata($newdata = array(), $newval = '')
	{
		if( is_array($newdata) && count($newdata) > 0 )
		{
			foreach( $newdata as $key=>$value )
			{
				$_SESSION[$key] = $value;
			}
		}
		else
		{
			$_SESSION[$newdata] = $newval;
		}
		
		return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */		
	function unset_userdata($newdata = array())
	{
			if( is_array($newdata) && count($newdata) > 0 )
			{
				foreach( $newdata as $key=>$value )
				{
					unset($_SESSION[$key]);
				}
			}
			else
			{
				unset($_SESSION[$newdata]);
			}

			return true;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Strip slashes
	 *
	 * @access	public
	 * @param	mixed
	 * @return	mixed
	 */
	 function strip_slashes($vals)
	 {
	 	if (is_array($vals))
	 	{	
	 		foreach ($vals as $key=>$val)
	 		{
	 			$vals[$key] = $this->strip_slashes($val);
	 		}
	 	}
	 	else
	 	{
	 		$vals = stripslashes($vals);
	 	}
	 	
	 	return $vals;
	}

}
// END Session Class
?>