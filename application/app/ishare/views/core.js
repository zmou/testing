function fav_remove( fid )
{
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/remove/' + fid  ;
	var myajax = new Ajax(url,
	{
		method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' ) alert( res );
			$('fav_item_'+ fid).setStyle('display' , 'none');
		}
	}).request();
	
	
}

function wall_remove( wid )
{
	
}