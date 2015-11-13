function put_on( fid )
{
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/puton/' + fid  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' && $('ihome_avatar') )
			{
				$('ihome_avatar').setHTML( res );
			}
		}
	}).request();
	
}
function ihome_user_item( id )
{
	if( !$('ihome_wait') )
		{
			var wait_div = new Element('div');
			wait_div.setProperty('id','ihome_wait');
			wait_div.injectTop( $E('body') );
		}
		center_it( 'ihome_wait' );
		$('ihome_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
		var url = '/app/native/<?php echo $GLOBALS['app'] ?>/carry/' + id ;
		var myajax = new Ajax(url,
		{
			data:foodata,
			method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 			
				$('ihome_wait').setHTML(res);
				center_it( 'ihome_wait' );
				(function(){ $('ihome_wait').remove(); }).delay(2000); 
			}
		}).request();
}
function ihome_carry_item( id , pic , desp )
{
	for( i=1;i < 5;i++ )
	{
		if( $('wear_'+i).innerHTML == '' )
			break;
	}
	$('wear_'+i).setHTML('<a href="JavaScript:ihome_unset_item(\''+id+'\',\''+i+'\')"><img src="'+pic+'" alt="'+desp+'" /></a>');
}
function ihome_unset_item( id , wid )
{
	if( !$('ihome_wait') )
		{
			var wait_div = new Element('div');
			wait_div.setProperty('id','ihome_wait');
			wait_div.injectTop( $E('body') );
		}
		$('wear_'+wid).setHTML('');
		center_it( 'ihome_wait' );
		$('ihome_wait').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
		var url = '/app/native/<?php echo $GLOBALS['app'] ?>/unset/' + id ;
		var myajax = new Ajax(url,
		{
			data:foodata,
			method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 			
				$('ihome_wait').setHTML(res);
				center_it( 'ihome_wait' );
				(function(){ $('ihome_wait').remove(); }).delay(2000); 
			}
		}).request();
}
function ihome_add_item( id , pic , desp  )
{
	if( $('item_'+ id ) )
	{
		var now = parseInt($('item_count_value_'+ id ).value) + 1;
		$('item_count_value_'+ id ).value = now;
		$('item_count_'+ id ).setHTML(now) ;
		return;
	}
	var count = parseInt($('baggage_count').value);
	if( count < 48 )
	{
		var new_a = new Element('a');
		new_a.href = "JavaScript:ihome_user_item('"+id+"');";
		new_a.title = desp;
		new_a.setHTML('<div id="item_'+id+'"  name="item_'+id+'" class="ihome_my_item" style="background:url('+pic+') top center;background-repeat : no-repeat;">X<span id="item_count_'+id+'" id="item_count_'+id+'">1</span><INPUT TYPE="hidden" NAME="item_count_value_'+id+'" id="item_count_value_'+id+'" value="1"></div>');
		$('baggage_list').appendChild(new_a);
		$('baggage_count').value = count + 1;

	}
}