function ding( feed_id )
{
	$('dig_show_' + feed_id).innerHTML = '推荐中';
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_dig/' + feed_id;
	var myajax = new Ajax(url,
	{	
		data:foodata,
		method:'post',
		evalScripts:true,
		onComplete:function( res ) 
		{
			if( res == 0 )
			{	
				$('dig_show_' + feed_id).innerHTML = '已推荐';
			}

			if( res > 0 )
			{
				$('digs_' + feed_id).innerHTML = res;
				$('dig_show_' + feed_id).innerHTML = '已推荐';
			}
		}
	}).request();
}

function show_desp_all( id )
{
	if( $( 'desp_breviary_' + id ).style.display == '' )
	{	
		$( 'desp_breviary_' + id ).style.display = 'none';
		$( 'desp_all_' + id ).style.display = '';
		$( 'desp_close_' + id ).style.display = '';
		$( 'desp_closeTOP_' + id ).style.display = '';
		$( 'desp_open_' + id ).style.display = 'none';
	}
	else
	{
		$( 'desp_all_' + id ).style.display = 'none';
		$( 'desp_breviary_' + id ).style.display = '';
		$( 'desp_close_' + id ).style.display = 'none';
		$( 'desp_closeTOP_' + id ).style.display = 'none';
		$( 'desp_open_' + id ).style.display = '';
	}
}

function Change_type( aid, tid )
{	
	$('type-'+ aid).setHTML('<img src="/static/images/loading.gif">');
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_change_type/' + aid + '/' + tid;
	var myajax = new Ajax(url,
	{
		data:foodata,
		method:'post',
		evalScripts:true,
		onComplete:function( res )
		{
			if( res == '0' )
			{	
				$('type-'+ aid).setHTML('<img src="/static/images/cross.gif">');
			}
			else
			{
				$('type-'+ aid).setHTML('<img src="/static/images/tick.gif">');
			}
		}
	}).request();
}

function Change_state( aid, sid )
{
	$('state_'+ aid).setHTML('<img src="/static/images/loading.gif">');
	
	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_change_state/' + aid + '/' + sid;
	var myajax = new Ajax(url,
	{
		data:foodata,
		method:'post',
		evalScripts:true,
		onComplete:function( res )
		{
			//$('state_'+ aid).setHTML('<img src="/static/images/tick.gif">');
			$( 'state-'+ aid ).setHTML( res );
		}
	}).request();
}

//更新
function f_refresh( fid )
{
	var id2up = new Array();
	//class = feeds
	$$('.feeds').each( function( item )
	{
		id2up.push( item.id.replace('feed-' , '' ) );	
	});	
	
	
	if( id2up )
	{
	
		if( fid < id2up.length )
		{
			var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_update/' + id2up[fid];
			//alert( url );
			if( fid+1 >= id2up.length )
			{
				$('feed-text-' + id2up[fid]).setHTML( '<a href="javascript:$(\'feed-text-' + id2up[fid] + '\' ).setHTML();void(0)" class=""><img src="/static/images/arrow_fat_down.gif"></a>&nbsp;<img src="/static/images/loading.gif" />' );
			}
			else
			{
				$('feed-text-' + id2up[fid]).setHTML( '<a href="javascript:f_refresh('+ (fid+1) +');$(\'feed-text-' + id2up[fid] + '\' ).setHTML();void(0)"><img src="/static/images/arrow_fat_down.gif"></a>&nbsp;<img src="/static/images/loading.gif" />' );
			}

			var myajax = new Ajax( url,
			{
				data:foodata,
				method:'post',
				evelScripts:true,
				onComplete:function( res )
				{
					$('feed-text-'+ id2up[fid]).setHTML( res );
					fid++;
					f_refresh( fid );
				}
			}).request();
		}
	}
}


//强制图片引用
function Change_img( aid )
{
	$('feed_'+ aid ).setHTML('<img src="/static/images/loading.gif" />');

	var url = '/app/native/<?php echo $GLOBALS['app'] ?>/ajax_change_img/' + aid;
	var myajax = new Ajax(url,
	{
		data:foodata,
		method:'post',
		evalScripts:true,
		onComplete:function( res )
		{

			if( res == '2' )
			{
				$('feed_'+ aid ).setHTML('<img src="/static/images/minus.gif" alt="强制图片引用已关闭" onclick="Change_img( '+ aid +' )" style="cursor:pointer">');
			}
			
			if( res == '1' )
			{
				$('feed_'+ aid ).setHTML('<img src="/static/images/plus.gif" alt="强制图片引用已开启" onclick="Change_img( '+ aid +' )"  style="cursor:pointer">');
			}

		}
	}).request();
}


function lock_img()
{
	var imgs = document.getElementsByTagName( 'img' );
	for( var i = 0; i<imgs.length; i++ )
	{
		var size = parseInt( imgs[i].getAttribute( 'lock' ) );
		
		var iwid = imgs[i].width;
		var ihei = imgs[i].height;
		if( size > 0 )
		{
			if( iwid > size )
			{
				imgs[i].width = size;
				var cha = iwid/size;
				imgs[i].height = parseInt(ihei/cha);
			}
		}
	}
}