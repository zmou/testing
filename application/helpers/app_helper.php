<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');

$CI =&get_instance();

if(!file_exists(APPPATH.'cache/backstage.cache.php') )
{
	initalize_app_cache();
}
else
{
	include_once(APPPATH.'cache/backstage.cache.php');
}



function load_apps()
{
	global $CI;

	$CI->db->select('*')->from('u2_app')->where('1');

	return lazy_get_data();
}
function initalize_app_cache()
{
	$apps = load_apps();


	
	foreach($apps as $a)
	{
		$contents = NULL;
		
		if( !$a['u2_folder'] ) continue;
		
		$path = APPPATH.'app/'.$a['u2_folder'].'/controller/backstage.php';

		if(file_exists($path) )
		{
			$old = array('<?php','?>');
			$contents .= str_replace($old,'',file_get_contents($path));
			eval($contents).$path;
		}
	}
	
	/*file_put_contents(APPPATH.'cache/backstage.cache.php' ,'<?php '."\r\n".$contents."\r\n".'?>');*/
}

function get_backstage_data($type = 'index')
{
	$apps = load_apps();
	
	$list = NULL;
	
	foreach($apps as $a)
	{
		$fun_name = $a['u2_folder'].'_'.$type;

		if( function_exists($fun_name)  )
		{
			$list[$a['u2_folder']] = $fun_name(); 
		}
	}
	return $list;
}

?>