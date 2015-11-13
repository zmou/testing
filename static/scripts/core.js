var foodata = new Object();
var MyDrag = null;
function ajax_get_widget(id,cid,pid)
{
	var url = '/ajax/widget/'+id;
	
	var para=new Object();
	para.cid = cid;
	para.pid = pid;

	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:false,
		onComplete:function( res )
		{ 
			$('widget-'+id ).innerHTML = res;
			$('widgets_now').value = parseInt($('widgets_now').value)+1;
			check_widget_num();
			run_script( res );
		}
	}).request();
}
function check_widget_num()
{
	
	if($('widgets_now').value == $('num_widgets').value)
	{
		$('widgets_now').value = 0;
		$('saved').value = '0';
		MyDrag = new MooDrag( 'ulist' , {handles:'.Drag',onComplete:function( el )
		{ 
			var saved = $('saved').value; 
			var tag = $('tag').value; 
			var order = this.serialize();
			var url = '/ajax/save_widget_location/'+tag+'/'+order +'/'+saved ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					if($('saved').value == '0')
					$('saved').value = '1';
				}
			}).request();
			
		}} );
	}

}
function auto_drag()
{
	$('widgets_now').value = 0;
		$('saved').value = '0';
		MyDrag = new MooDrag( 'ulist' , {handles:'.Drag',onComplete:function( el )
		{ 
			var saved = $('saved').value; 
			var tag = $('tag').value; 
			var order = this.serialize();
			var url = '/ajax/save_widget_location/'+tag+'/'+order +'/'+saved ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					if($('saved').value == '0')
					$('saved').value = '1';
				}
			}).request();
			
		}} );
}
function reload_miniblog()
{
	var url = '/user/miniblog/1';
	if($('friend_only_check_box').checked == true )
	{
		url = url + '/friend'; 
	}
	else
	{
		url = url + '/all'; 	
	}
	location = url;
}
function ajax_del_mesaage(id,box)
{
	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
	var url = '/ajax/delmessage/'+box+'/'+id;
	
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			location = '/user/message/'+box;
		}
	}).request();
}

function ajax_update_status( msg )
{
	var url = '/ajax/updatestatus/'+ encodeURIComponent( msg ) ;
	
	//alert( url );
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			//alert( res );
		}
	}).request();
}

function message_swich(id)
{
	if($('message_info_'+id).style.display == "")
		$('message_info_'+id).style.display = "none";
	else
		$('message_info_'+id).style.display = "";
		

}
function message_reply_swich(id)
{
	if($('message_reply_'+id).style.display == "")
		$('message_reply_'+id).style.display = "none";
	else
		$('message_reply_'+id).style.display = "";
}
function select_all(action,name)
{
	var e=document.getElementsByTagName("input");

	for(var i=0 ;i<e.length;i++)
	{
		if(e[i].name == name)
		{
			if(action=="selectAll")
			{
				e[i].checked = 1;
			}
			if(action=="selectNone")
			{
				e[i].checked = false;
			}
			if(action=="selectOther")
			{
				e[i].checked = !e[i].checked;
			}
		}
	}
}
function show_people_level()
{
	var $i = 0;

	var e=document.getElementsByTagName("input");

	for(var i=0 ;i<e.length;i++)
	{
		if(e[i].name == 'ids[]' && e[i].checked == true )
		{
			$i++;
		}
	}
	if($i == 0)
	{
		alert('<?=_text('system_no_chooose_user');?>!');
		return;
	}
	//$('admin_member_action').disabled = true;
	$('member_list').disabled = true;
	$('button_show_level').disabled = true;
	$('people_level').style.display = "";
}
function change_level(clevel)
{
	$('member_list').disabled = false;
	$('clevel').value= clevel;
	$('member_list').submit();
}
function search_members(admin)
{

	var search =  encodeURIComponent( $('searchtext').value );
	if(admin == 1)
	{
		location = '/admin/admins/1/'+search;
	}
	else
	{
		location = '/admin/members/1/'+search;
	}
}
function do_manage(id,action)
{
	var url = '/ajax/contents/';
	
	var para=new Object();
	para.ids = id;
	para.action = action;
	var myajax = new Ajax(url,
	{
		data:para,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				alert( res );
			}
			else
			{
				window.location.reload();
			}
		}
	}).request();
}
function u_do_manage()
{
	var url = '/ajax/u_do_manage/';
	
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				alert( res );
			}
			else
			{
				window.location.reload();
			}
		}
	}).request();
}
function do_manages(action)
{
	var $i = 0;

	var e=document.getElementsByTagName("input");

	for(var i=0 ;i<e.length;i++)
	{
		if(e[i].name == 'ids[]' && e[i].checked == true )
		{
			$i++;
		}
	}
	if($i == 0)
	{
		alert('<?=_text('system_no_choose_contents');?>');
		return;
	}
	$('action').value = action;
	
	$('contents_manage').send({
		onComplete: function(res) {
			if( res != '' )
			{
				alert( res );
			}
			else
			{
				window.location.reload();
			}
		}
	});

}
function add_meta_data()
{
	if( $('name').value != '' && $('info').value != '' )
	{	
		$('data_submit').disabled = false;
	}
	else
	{
		$('data_submit').disabled = true;
	}
}
function admin_data_check_cate()
{

	if($('do_action').value == '0')
	{	
		$('cate_input_name').style.display = '';
		
		if ($('cate_name').value != '')
			$('data_submit').disabled = false;
		else
			$('data_submit').disabled = true;

	}
	if($('do_action').value == '2')
	{
		if( $('cate_id').value != '0' )
			$('cate_input_name').style.display = '';
		else
		{
			$('cate_input_name').style.display = 'none';
			$('data_submit').disabled = true;
		}

		if ($('cate_name').value != '')
			$('data_submit').disabled = false;
		else
			$('data_submit').disabled = true;
	}
	if($('do_action').value == '1')
	{
		$('cate_input_name').style.display = 'none';
			if( $('cate_id').value != '0' )
				$('data_submit').disabled = false;
			else
				$('data_submit').disabled = true;
	}

}
function choose_cate(value)
{
	$('cate_id').value = value;
	admin_data_check_cate();
}
function info_load(name)
{
	$(name).innerHTML = '<p></p><img src="/static/images/loading.gif">Loading Please Wait.';
}
function add_page()
{
	set_layout(2);
	$('page_action').value = 'add';
	set_select_value('page_type', '1');
	$('page_title').value = '';
	select_display('none');
	$('div-newMod').style.display = 'none';
	
	$('div-newPage').style.display = '';
	check_page_input();
	center_it('div-newPage');
	

}
function close_new_page()
{
	$('div-newPage').style.display = 'none';
	select_display('');
}
function add_new_mod()
{
	close_new_page();
	$('div-newMod').style.display = '';
	
	var p = $('u2_add_mod').getPosition();
	
	//alert( 'x -' + p.x + ' y - ' + p.y );
	
	//$('qmenu_pop').style.display='';
	$('div-newMod').setStyle( 'left' , p.x + 60 - parseInt( $('div-newMod').getStyle('width') ) ); 
	$('div-newMod').setStyle( 'top' , ( parseInt( p.y ) + 12 ) + 'px' );
	
	
	if($('mod_list').innerHTML != '')
	{
		return;
	}

	info_load('mod_list');

	var url = '/ajax/get_app_list/';
	
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('mod_list').innerHTML = res;
		}
	}).request();
}
function close_new_mod()
{
	$('div-newMod').style.display = 'none';
}
function show_widget_byaid(id)
{
	if($('widget_aid_' + id).style.display == '')
	{
		$('widget_aid_' + id).style.display = 'none';
	}
	else
	{
		$('widget_aid_' + id).style.display = '';
	}
	if($('widget_aid_' + id).innerHTML != '')
	{
		return;
	}
	info_load('widget_aid_' + id );

	var url = '/ajax/get_widget_by_aid/'+id;
	
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('widget_aid_' + id).innerHTML = res;
		}
	}).request();
}
function add_widget_on_page(id)
{
	var tag = $('tag').value;

	var url = '/ajax/add_widget_by_id/'+tag+ '/' +id;
	
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 			
			window.location.reload();
			
		}
	}).request();
}
function del_page_widget(id)
{
	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
	var tag = $('tag').value;

	var url = '/ajax/del_widget_by_id/'+tag+ '/' +id;

	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$($('widget-'+id).parentNode).remove();
			if( MyDrag != null )
			{
				MyDrag.rebuild();
				$('saved').value = '0';
			}
			//alert(res);
		}
	}).request();
}

function ajax_accept_friend_request( buddyid , tohidden )
{
	ajax_update_friend_request( buddyid , 1 , tohidden );
}

function ajax_refuse_friend_request( buddyid , tohidden )
{
	ajax_update_friend_request( buddyid , 2 , tohidden );
}

function ajax_update_friend_request( buddyid , to , tohidden )
{
	var url = '/ajax/friend_request/' + buddyid + '/' + to ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			//alert( res );
			//$(tohidden).setStyle('display' , 'none');
			if( to == 1 )
				$(tohidden).setHTML('通过成功');
			else
				$(tohidden).setHTML('通过忽略');		
		}
	}).request();
}


function select_display(play)
{
	document.getElements('select').each( function( item , index )
	{
		item.style.display = play;
	})
}
function check_page_input()
{
	if($('page_title').value == '')
	{
		$('page_submit').disabled = true;

		return;
	}
	var type = get_select_value('page_type');

   if(type == '1' || type == '2')
	{
		$('page_submit').disabled = false;
		return;
	}
	if(type == '3' )
	{
		if($('pro_id').value != '')
		{
			$('page_submit').disabled = false;
			return;
		}
	}
	if(type == '4' )
	{
		if( $('page_link').value != '' )
		{
			$('page_submit').disabled = false;
			return;
		}
	}

		$('page_submit').disabled = true;
}
function add_page_input( cid ,pid )
{
	var type = get_select_value('page_type');

	if(type == '1')
	{
		if( $('page_extra_data').innerHTML != '' )
			$('page_extra_data').innerHTML = '';
	}
	if(type == '2')
	{
		info_load('page_extra_data');
		var url = '/ajax/get_cates_html/check_page_input/';

		var myajax = new Ajax(url,
		{
			data:foodata,method:'post' ,
			evalScripts:true,
			onComplete:function( res )
			{ 
				$('page_extra_data').innerHTML = '<?=_text('pro_cate_choose');?>&nbsp;&nbsp;&nbsp;&nbsp;' + res ;
				if(cid != '')
				{
					$('cateid').value = cid;
				}

			}
		}).request();
	}
	if(type == '3')
	{
		$('page_extra_data').innerHTML = '<?=_text('system_pro_id');?>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="text" id="pro_id" NAME="pro_id" onchange="check_page_input();void(0);" class="text">';
		if( pid != '')
				{
					$('pro_id').value = pid;
				}
	}
	if(type == '4')
	{
		$('page_extra_data').innerHTML = '<?=_text('system_url');?>&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="text" id="page_link" NAME="page_link" onchange="check_page_input();void(0);" class="text"><p></p><p><INPUT TYPE="checkbox" NAME="new_window" value="1" checked>&nbsp;&nbsp;在新窗口中打开</p>';
	}
	check_page_input()
}

function set_select_value(name ,value)
{
	var type = 0;
	var page_types = document.getElementsByName(name);
	for(var i=0;i<page_types.length;i++)
	{
		if(page_types[i].value == value)
			{
				page_types[i].checked = true;
				break;
			}
   }

}
function get_select_value(name)
{
	var type = 0;
	var page_types = document.getElementsByName(name);
	for(var i=0;i<page_types.length;i++)
	{
		if(page_types[i].checked)
			{
				type = page_types[i].value;
				break;
			}
   }
   return type;
}
function quick_menu(a , b )
{
	var p = $(a).getPosition();
	
	//alert( 'x -' + p.x + ' y - ' + p.y );
	
	//$('qmenu_pop').style.display='';
	$(b).setStyle( 'left' , p.x ); 
	$(b).setStyle( 'top' , ( parseInt( p.y ) + 12 ) + 'px' );
	$(b).setStyle( 'display' , 'block' ) ;
}
function close_menu(a)
{
	$(a).style.display='none';
}
function pro_choose_cate(value)
{
	$('cate').value = value;
	load_pro_extra(value);
}
function load_pro_extra(id)
{
	if(id == '0')
	{
		$('pro_extra').innerHTML = '';
		return;
	}

	info_load('pro_extra');
	
	var url = '/ajax/pro_extra_input/' + id ;

	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('pro_extra').innerHTML = res ;
		}
	}).request();
}
function modify_page(type,layout,cid,pid)
{
	set_layout(layout);
	$('page_action').value = 'modify';
	set_select_value('page_type', type);
	
	$('page_title').value = decodeURIComponent( $('tag').value );

	add_page_input(cid ,pid );

	select_display('none');
	$('div-newMod').style.display = 'none';
	$('div-newPage').style.display = '';
	center_it('div-newPage');
	
}

function center_it( name )
{
	var info = $(name).getSize();
	$(name).setStyle( 'left' , get_center_left(info.size.x) +'px');
	$(name).setStyle( 'top' , get_center_top(info.size.y)+'px');
}

function get_center_top( window_h )
{
	return window.getScrollTop() + ((document.documentElement.clientHeight-window_h)/2);
	//return window.getScrollTop() + ((window.getHeight()-window_h)/2);
}

function get_center_left( window_w )
{
	return window.getScrollLeft() + ((window.getWidth()-window_w)/2);
}

function ajax_widget_setting(id , cid , pid )
{

	lazy_div( 'app_extra_input' );

	$('app_extra_input').setStyle('display','block');
	center_it('app_extra_input');
	$('app_extra_input').setHTML('<center><img src="/static/images/loading.gif">Loading Please Wait.</center>');
	
	var url = '/ajax/get_widget_extra_html/' + id ;
	var para=new Object();
	para.cid = cid;
	para.pid = pid;

	var myajax = new Ajax(url,
	{
		data:para,method:'post' ,
		//evalScripts:true,
		onComplete:function( res )
		{ 
			$('app_extra_input').setHTML(res) ;
			run_script( res );
			center_it( 'app_extra_input' );
		}
	}).request();
}
function cancel_widget_extra()
{
	$('app_extra_input').setHTML('');
	$('app_extra_input').setStyle('display','none');
}
function send_widget_extra(id)
{
	$('widget_extra_'+id ).addEvent('submit', function(e) {
	new Event(e).stop();
	
	
	fck_ajax_send( 'widget_extra_'+id , {
		evalScripts:true,
		onComplete: function(res) 
		{
			$('widget-'+id).innerHTML = res;
			cancel_widget_extra();
		}
	} );

});
}
function ajax_reload_widget(id,cid,pid)
{
	var url = '/ajax/widget/'+id;
	
	var para=new Object();
	para.cid = cid;
	para.pid = pid;

	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:false,
		onComplete:function( res )
		{ 
			$('widget-'+id ).innerHTML = res;
			run_script( res );
			MyDrag.rebuild();
			$('saved').value = '0';
		}
	}).request();
}
function ajax_widget_page( id , cid , pid , extra )
{
	//$('widget-'+id ).setHTML('<br/><center><img src="/static/images/loading.gif">Loading Please Wait.</center><br/>');
	var winfo = $('widget-'+id ).getCoordinates();
	var loading = new Element('div');
	loading.setProperty('class','wloading');
	loading.injectInside('widget-'+id);
	loading.setHTML('<center><img src="/static/images/loading.gif">Loading Please Wait.</center>');
	center_it( loading );
	var url = '/ajax/widget/'+id  + extra;
	
	var para=new Object();
	para.cid = cid;
	para.pid = pid;

	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:false,
		onComplete:function( res )
		{ 
			$('widget-'+id ).innerHTML = res;
			run_script( res );
			if( MyDrag != null )
			{
				MyDrag.rebuild();
				$('saved').value = '0';
			}
		}
	}).request();
}
function admin_change_page_link(id)
{
	if($('page_'+id+'_link').value  == '')
		return;
	$('page_'+id+'_link').disabled = true;

	var link = encodeURIComponent( $('page_'+id+'_link').value );

	var url = '/ajax/change_page_link/' + id +'/'+ link ;

	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			alert(res);
			$('page_'+id+'_link').disabled = false;
		}
	}).request();
}
function admin_del_extra(id)
{
	
	var url = '/ajax/admin_del_extra/' + id  ;

	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function()
		{ 
			window.location.reload();
		}
	}).request();
}
function link_del_confirm(url)
{

	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
	location = url;
}
function check_mail(mail,name)
{

	if(  validateEmail( $(mail).value )  )
	{
		name.submit();
	}
	else
	{
		alert('<?=_text('system_email_error')?>');
	}
}
function validateEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if(splitted == null) return false;
    if(splitted[1] != null )
    {
      var regexp_user=/^\"?[\w-_\.]*\"?$/;
      if(splitted[1].match(regexp_user) == null) return false;
    }
    if(splitted[2] != null)
    {
      var regexp_domain=/^[\w-\.]*\.[A-Za-z]{2,4}$/;
      if(splitted[2].match(regexp_domain) == null) 
      {
	    var regexp_ip =/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
	    if(splitted[2].match(regexp_ip) == null) return false;
      }// if
      return true;
    }
return false;
}
function reg_check()
{
	if($('nickname').value == '' || $('email').value == '' || $('psw').value == ''  )
	{
		alert('<?=_text('user_error_register_is_null')?>');
		return;
	}
	if( !validateEmail( $('email').value ))
	{
		alert('<?=_text('system_email_error')?>');
		return;
	}
	if( $('psw').value !=  $('psw2').value)
	{
		alert('<?=_text('user_resetpass_not_same')?>');
		return;
	}
	if($('psw').value.length < 6 )
	{
		alert('<?=_text('user_pass_too_short')?>');
		return;
	}
	$('reg_from').submit();

}
function admin_save_link(id)
{
	if($('link_'+id).value == '')
	{
		alert('<?=_text('system_not_null');?>');
		return;
	}
	$('link_'+id).disabled = true;
	var url = '/ajax/change_page_link/' + id ;
	var para=new Object();
	para.link = $('link_'+id).value;

	var myajax = new Ajax(url,
	{
		data: para ,
		method:'post' ,
		evalScripts:true,
		onComplete:function()
		{ 
			$('link_'+id).disabled = false;
		}
	}).request();
}
function admin_magic_display(play,noplay)
{	
	$(play).style.display = '';
	$(noplay).style.display = 'none';	
}
function admin_modify_extra(id)
{
	$('extra_name_' + id).disabled = false;
	$('extra_desp_' + id).disabled = false;
	$('data_modify_submit').disabled = false;
}
function initialize_page(id)
{
	if(!confirm('<?=_text('system_initialize_page_confirm');?>') )
	{
		return;
	}
	var url = '/ajax/initialize_page/' + id ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			//alert(res);
			window.location.reload();
		}
	}).request();
}
function set_layout(id)
{
	$('page_layout_left').innerHTML = '<img src="/static/images/div-'+ id +'.gif">';
	$('page_layout').value = id;
}
function admin_change_page_display(pid,display)
{
	$('page_'+ pid +'_display').disabled = true;
	var url = '/ajax/chang_page_display/' + pid + '/' + display;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			//alert(res);
			$('page_'+ pid +'_display').innerHTML = res;
			$('page_'+ pid +'_display').disabled = false;
		}
	}).request();
	
}

function minifeed_remove( mid )
{
	var url = '/ajax/minifeed_remove/' + mid  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			//alert( res );
			$('minifeed_item_'+mid).setStyle('display' , 'none');
		}
	}).request();
}

function wall_remove( wid )
{
	var url = '/ajax/wall_remove/' + wid  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			//alert( res );
			$('wall_item_'+ wid).setStyle('display' , 'none');
		}
	}).request();
}

function ajax_form( bid )
{
	// change the submit button to a normal one
	if( f = parent_tag($(bid) , 'form') )
	{
		$(bid).setProperty('type' , 'button');
		$(bid).addEvent('click', function()
		{
			send_form(this);
		});
		return true;
	}
	else
	{
		return false;
	}	
}

function send_wall_content( obj )
{
	fobj = parent_tag( obj , 'form' );
	fobj.send
	({ 
		onComplete:function( text )
		{
			window.location.reload();		
		},
		evalScripts:true 
	});
}

function send_chat( obj , fobj )
{
	if( $('chat_editor').value == '' )
	{
		alert('不能发送空消息');
		return falsel
	}
	
	//fobj = parent_tag( obj , 'form' );
	fobj.send
	({ 
		onComplete:function( text )
		{
			if( text != '' )
			{
				alert( text );	
			}
			chat_close();		
		},
		evalScripts:true 
	});
	
}


function send_form( obj )
{
	fobj = parent_tag( obj , 'form' );
	fobj.send
	({ 
		onComplete:function( text )
		{
			ajax_notice( text , fobj );
		},
		evalScripts:true 
	});

	//fobj.disable();
}


function show_chat( mid )
{
	if( !$('u2_live_chat') )
	{
		var chat_div = new Element('div');
		chat_div.setProperty('id','u2_live_chat');
		chat_div.injectTop( $E('body') );
	}
	
	$('u2_live_chat').setStyle('display' , 'block');
	
	var url = '/ajax/chat/' + mid  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			$('u2_live_chat').setHTML( res );
			center_it( 'u2_live_chat' );
			new Drag.Move('u2_live_chat', { 'handle': $('imove')});
		}
	}).request();
	
	$('u2_live_chat').setHTML('<img src="/static/images/loading.gif" />&nbsp;...');
	
	center_it( 'u2_live_chat' );
}

function chat_close()
{
	if( $('u2_live_chat') ) 
		$('u2_live_chat').setStyle( 'display' , 'none' );
}

function float_notice( text )
{
	if( !$('u2_float_box') )
	{
		var notice_div = new Element('div');
		notice_div.setProperty('id','u2_float_box');
		notice_div.injectTop($E('body'));
	
	
	}
	
	$('u2_float_box').setHTML(text);
	$('u2_float_box').setStyle('display','block');
	center_it( 'u2_float_box' );
	(function(){ $('u2_float_box').setStyle('display','none'); }).delay(1200);
	
}


function ajax_notice( text , f )
{
	//
	if( !$('u2_notice_box') )
	{
		var notice_div = new Element('div');
		notice_div.setProperty('id','u2_notice_box');

		if( f )
		{
			notice_div.injectTop(f);
		}
		else
		{
			if($('yui-main'))
				tobj = $('yui-main');
			else
			{
				if( $('ucontainer') )
					tobj = $('ucontainer');
				else
					tobj = $E('body');
			}
			
				
			notice_div.injectTop(tobj);
		}
	
	}
	
	$('u2_notice_box').setHTML(text);
	$('u2_notice_box').setStyle('display','block');

	
}

function parent_tag( node , tagname )
{
	if( nows = node.getParent() )
	{
		if( nows.getTag() == tagname )
		{
			return nows;
		}
		else
		{
			return nows = parent_tag( nows , tagname );
		}
		
	}
	else
	{
		return false;
	}
	
	
}

function run_script( text )
{
	scripts = [];
	var regexp = /<script[^>]*>([\s\S]*?)<\/script>/gi;
	while ((script = regexp.exec( text ))) scripts.push(script[1]);
	scripts = scripts.join('\n');
	
	if (scripts) (window.execScript) ? window.execScript(scripts) : window.setTimeout(scripts, 0);
}

function set_value( name , value )
{
	var objs = document.getElementsByName( name );
	if( objs[0] )
	{
		switch( objs[0].type )
		{
			case 'radio': 
				return set_radio( name , value  );
				break;
			case 'select-one': 
				return set_select( name , value  );
				break;
			case 'checkbox': 
				return set_checkbox( name , value  );
				break;
			case 'textarea':
				set_text( name , value );
				break;
			case 'text':
			default: return set_text( name , value );
		}
	}
	
}

function set_text( name , value )
{
	var el = getElement( name );
	el.value = value;
}

 function getElement( name )
 {
	var el = document.getElementsByName( name );
	if( el[0] == null )
	{
		alert( 'cannot find ' + name + ' ! ' );
	}
	else
	{
		return el[0];
	}
	
 }

function set_checkbox( name , value )
{
	var obj = document.getElementsByName( name );
	for(var i=0;i<obj.length;i++)
	{
		if(obj[i].type=="checkbox")
		{
			if( obj[i].value == value )
			{
				obj[i].checked = true;
			}
		}
	}
}

function checkbox_on( name )
{
	var obj = document.getElementsByName( name );
	for(var i=0;i<obj.length;i++)
	{
		obj[i].checked = true;
	}

}

function checkbox_off( name )
{
	var obj = document.getElementsByName( name );
	for(var i=0;i<obj.length;i++)
	{
		obj[i].checked = false;
	}

}

function set_select_text( name , value )
{
	var sel = getElement( name );
	var ops = sel.options;
	for( var i = 0 ; i < ops.length ; i++ )
	{
		if( ops[i].text == value  )
		{
			try
			{
				if( i != ops.selectedIndex )
				{
					ops.selectedIndex = i;
					ops[i].selected = true;
				}
				
			}
			catch( e ) 
			{
				// alert( e.description );
				// ie对于动态生成的下拉框会抛出一个“不能设置selected属性，未指明的错误”的异常
				// 原因不明，先不做处理
			}
			
			
		}
	}
}

function set_select( name , value )
{
	var sel = getElement( name );
	var ops = sel.options;
	for( var i = 0 ; i < ops.length ; i++ )
	{
		if( ops[i].value == value  )
		{
			try
			{
				if( i != ops.selectedIndex )
				{
					ops.selectedIndex = i;
					ops[i].selected = true;
				}
				
			}
			catch( e ) 
			{
				// alert( e.description );
				// ie对于动态生成的下拉框会抛出一个“不能设置selected属性，未指明的错误”的异常
				// 原因不明，先不做处理
			}
			
			
		}
	}
}


function set_radio( name , value )
{
	var objRadio = document.getElementsByName( name );
	for(var i=0;i<objRadio.length;i++)
	{
		if(objRadio[i].type=="radio")
		{
			if( objRadio[i].value == value )
			{
				objRadio[i].checked = true;
			}
		}
	}
}

function make_rich( textarea_name , thestyle )
{
	var width = $(textarea_name).getStyle('width');
	var height = $(textarea_name).getStyle('height');
	
	var oFCKeditor = new FCKeditor( textarea_name ) ;
	oFCKeditor.Width = width;
	oFCKeditor.Height = height;
	
	if( thestyle ) 
		oFCKeditor.ToolbarSet = thestyle;
	else
		oFCKeditor.ToolbarSet = 'Basic';
	
	oFCKeditor.ReplaceTextarea() ;
}

function cron_jobs( module )
{
	var url = '/ajax/cron_jobs/' + module  ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			
		}
	}).request();
}


function lazy_div( did )
{
	if( !$(did) )
	{
		var mydiv = new Element( 'DIV' );
		mydiv.setProperty( 'id', did );
		mydiv.injectTop($E('body'));
	}
}
function copyinvite( id )
{
	copyToClipBoard( 'invite_code_'+id );
	var url = '/ajax/copyintive/'+id ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('invite_action_'+id).innerHTML = '<?=_text('system_copied')?>';

				}
			}).request();

}
function copyToClipBoard( name )
{ 
	window.clipboardData.setData("Text",$(name).innerHTML ); 
	alert("复制成功，请粘贴到你的QQ/MSN上推荐给你的好友"); 
}
function copycard( id )
{
	copyToClipBoard( 'card_code_'+id );
	var url = '/ajax/copycard/'+id ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('card_action_'+id).innerHTML = '<?=_text('system_copied')?>';

				}
			}).request();

}
function show_pro_pic()
{
	if( $('picfile').value != '' )
	{
		$('show_pro_pic').innerHTML = '<img src="'+$('picfile').value+'" class="icon" width="100" height="100" />';
	}
}
function show_res( aname , aid , uid )
{
	var url = '/ajax/getres/'+aname+ '/'+aid+'/'+uid+ '/';
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('res_list_'+aid).setStyle('display','block');
					$('res_list_'+aid).setHTML(res);
				}
			}).request();
	$('res_button_'+aid).setStyle('display','none');


}
function ajax_save_res( aname, aid )
{
	if( $('res_desp_'+aid).value == '' )
	{
		alert('<?=_text('user_message_send_is_null')?>');
		return;
	}

	var url = '/ajax/saveres/';
	var para=new Object();
	para.aid = aid;
	para.aname = aname ;
	para.uid = $('res_uid_'+aid).value;
	para.desp = $('res_desp_'+aid).value;
	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:false,
		onComplete:function(res)
		{ 
			$('res_list_'+aid).innerHTML = res;
		}
	}).request();
}
function cancel_res( aname , aid , uid )
{
	$('res_list_'+aid).innerHTML="";
	$('res_list_'+aid).setStyle('display','none');
	$('res_button_'+aid).setStyle('display','inline');
	//$('res_button_'+aid ).innerHTML = '<a href="JavaScript:void(0)" onclick="show_res(\''+aname+'\',\''+aid+'\',\''+uid+'\')">回复</a>';
}
function reload_page()
{
	window.location.reload();
}
function search_user()
{
	location = '/user/friend/1/'+encodeURIComponent($('search').value);
}
function ajax_del_confirm(url)
{

	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
		var myajax = new Ajax(url,
		{
			data:foodata,method:'post' ,
			evalScripts:true,
			onComplete:function()
			{ 
				window.location.reload();

			}
		}).request();
}
function ajax_do_ticket(id)
{
	var url = '/ajax/ticket/'+id;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			if(res != '')
			{
				alert(res);
			}
			else
			{
				window.location.reload();
			}

		}
	}).request();
}
function show_ajax_pager( url , divname )
{

	$(divname).setHTML( '<br/><center><img src="/static/images/loading.gif">Loading Please Wait.</center><br/>');
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			$(divname).setHTML(res);
		}
	}).request();
}
function do_lock()
{
	imgs = document.getElementsByTagName( 'IMG' );
	for( i = 0 ; i < imgs.length ; i++ )
	{
		size = parseInt( imgs[i].getAttribute("lock") );
		if( size > 0  )
		{
			if( imgs[i].width > size )
				imgs[i].width = size;

			//alert( imgs[i].width );
		}
	}
}
function show_pm_cron_jobs( notice , pm , request)
{
	flash_title();
	var all = parseInt(notice) + parseInt(pm) + parseInt(request);
	$('pm_all_count').setHTML('('+all+')');
	if( notice != '0' )
	{
		$('notice_count').setHTML('('+notice+')');
	}
	if( pm != '0' )
	{
		$('inbox_count').setHTML('('+pm+')');
	}
	if( request != '0' )
	{
		$('resquest_count').setHTML('('+request+')');
	}

}
var nowtitle = document.title;
function flash_title()
{
	
	(function(){ document.title = '【新信息】'+ nowtitle }).delay(100);
	(function(){ document.title = '【　　　】'+ nowtitle }).delay(600);
	(function(){ document.title = '【新信息】'+ nowtitle }).delay(1100);
	(function(){ document.title = '【　　　】'+ nowtitle }).delay(1600);
	(function(){ document.title = '【新信息】'+ nowtitle }).delay(2100);
	(function(){ document.title = '【　　　】'+ nowtitle }).delay(2600);
	(function(){ document.title = '【新信息】'+ nowtitle }).delay(3100);
	
	setTimeout( flash_title , 5000 );
}
function ajax_send( name , update )
{
	$(name).send({
		update: update,
		onComplete: function() {
		}
	});

}
function show_notice_box( html , name )
{
	if( !$('notice_box') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','notice_box');
		wait_div.injectTop( $E('body') );
	}
	html = '<div class="notice_top"><span class="r"><a href="javascript:void(0)" onclick="$(\'notice_box\').remove()"><img src="/static/images/cross.gif"/></a></span><img class="imove l" id="imove" style="CURSOR: move" src="/static/images/movearrow.gif"><h5 class="w2 l">&nbsp;'+name+'</h5></div><div class="notice_info">'+html+'</div>';
	$('notice_box').setHTML(html);
	center_it( 'notice_box' );
	new Drag.Move('notice_box', { 'handle': $('imove')});
}
function prev_id()
{
	$('notice').setStyle('display','none');
	if( $('nickname').value == '' )
	{
		alert('请输入您的名字');
		return;
	}
	if( $('email').value == '' )
	{
		alert('请输入您的E-mail');
		return;
	}
	if(  !validateEmail(  $('email').value )  )
	{
		alert('请正确输入您的E-mail');
		return;
	}
	if( $('psw1').value == '' )
	{
		alert('请输入您的密码');
		return;
	}
	if( $('psw1').value != $('psw2').value )
	{
		alert('2次输入密码不一致');
		return;
	}
	var html;
	if( $('picfile').value == '' )
	{
		html = '<img src="/static/images/user_normal_icon.default.gif" class="icon"/><br/>'+ $('nickname').value ;
	}
	else
	{
		html = '<img src="'+$('picfile').value+'" width="50px" height="50px" class="icon"/><br/>'+ $('nickname').value ;
	}
	$('id_icon').setHTML( html );

	$('id_send_box').setStyle('display','block');

}
function user_put_on( fid )
{
	var url = '/app/native/ihome/puton/' + fid  ;
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
function show_snotice( name , id )
{
	var url = '/ajax/show_snotice/' + id + '/' + name ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				if( !$(name).hasClass('snotice') )
				{
					$(name).addClass('snotice');
				}
				$(name).setHTML( res );
			}
			else
			{
				$(name).remove();
			}
		}
	}).request();
}
function show_invite( name )
{
	var url = '/ajax/show_iinvite_box/' ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			
			$(name).setHTML( res );
			
		}
	}).request();
}

function ajax_send_mails( name  )
{
	if( !$('notice_box') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','notice_box');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'notice_box' );
	$('notice_box').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	$(name).send({
		onComplete: function(res) 
			{
				$('notice_box').setHTML(res);
				(function(){ $('notice_box').remove(); }).delay(2000); 
			}
	});
}
function ajax_send_msn_account()
{
	if ( $('email').value == '' )
	{
		alert( "请填写账号信息" );
		return;
	}
	if ( $('psw').value == '' )
	{
		alert( "请填写密码" );
		return;
	}
	if( !$('notice_box') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','notice_box');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'notice_box' );
	$('notice_box').setHTML('<center>&nbsp;&nbsp;<img src="/static/images/loading.gif">Loading Please Wait.</center>');
	var url = '/app/native/iinvite/ajax/'+ $('type').value;
	var para=new Object();
	para.email = $('email').value;
	para.domain = $('domain').value;
	para.psw = $('psw').value;

	var myajax = new Ajax(url,
	{
		data:para,
		method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				$('notice_box').setHTML(res);
				(function(){ $('notice_box').remove(); }).delay(2000); 
			}
			else
			{
				location = '/app/native/iinvite/showuser';
			}
			
		}
	}).request();
	
}
function del_design_block( bid , key )
{

	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
	var url = '/design/del_block/'+bid+'/'+key ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				alert(res);
			}
			else
			{
				window.location.reload();
			}

		}
	}).request();
}
function show_pic_muti_preview( imgstr , pre )
{
	
	imgs = $(imgstr).value.split("\r\n");
	//alert( imgstr );
	//alert( imgs );
	//alert( imgs.length );
	if( imgs.length > 0 )
	{
		$(pre).innerHTML = '';
		for( i = 0 ; i < imgs.length ; i++ )
		{
			if( imgs[i].length > 10 )
				$(pre).innerHTML += '<img src="' + imgs[i] + '" width="50px" onclick="window.open(this.src)" style="cursor:pointer"/>&nbsp;';
		}
		
	}	
}

function upgrade_code( cid )
{
	var id2up = new Array();
	$$('.codecheck').each( function( item )
	{
		if( item.checked )
		{
			if(cid == 0)
				$('codespan' + item.value ).innerHTML = '<font color="gary">等待中</font>' ;
		
			id2up.push( item.value );
		} 
	});
	
	if( id2up )
	{
		if( cid < id2up.length )
		{
			$('codespan' + id2up[cid] ).innerHTML = '<img src="/static/images/loading.gif" />' ;
			var url = '/ajax/update_code/'+encodeURIComponent( id2up[cid] );
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('codespan' + id2up[cid] ).innerHTML = res ;
					cid++;		
					upgrade_code( cid ); 
				}
			}).request();
		}
	}
		
}
function ajax_vote_send( name )
{
	$(name).send({
		evalScripts:true,
		onComplete: function(res) 
		{
			//alert(res);
		}
	});

}
function ajax_show_vote( wid )
{
	var url = '/ajax/voteshow/'+wid;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function( res )
		{ 
			//alert(res);
		}
	}).request();
} 
function format_number( name )
{
	var no = $(name).value;
	var number = parseInt(no , 10 );
	if(  number < 1  || isNaN(number) )
	{
		number = 0;
	}
	$(name).value = number;
}
function format_float( name )
{
	var no = $(name).value;
	var number = parseFloat(no);
	if( isNaN(number) )
	{
		number = 0;
	}
	$(name).value = number;
}
function change_app_icon( name )
{
	if( !$('notice_box') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','notice_box');
		wait_div.injectTop( $E('body') );
	}
	center_it( 'notice_box' );
	$('notice_box').setHTML('<div class="underline p2"><span class="r"><a href="javascript:void(0)" onclick="$(\'notice_box\').remove()"><img src="/static/images/cross.gif"/></a></span><b>更改图标</b></div><iframe src="" name="icon_change" scrolling="no" frameborder="0" width="0px;" height="0px;"></iframe><div class="p2"><form action="/upload/icon/" method="post" encType="multipart/form-data" target="icon_change"><INPUT TYPE="hidden" NAME="name" value="'+ name +'"><INPUT TYPE="file" NAME="icon_file" class="file">&nbsp;&nbsp;&nbsp;<INPUT TYPE="submit" class="text" value="提交"><br/><br/></div>');
}
function show_icon_res( res )
{
	$('notice_box').setHTML( res );
	(function(){ $('notice_box').remove(); }).delay(2000); 
}
function save_statistics( ref )
{
	var url = '/statistics/index/' + ref;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function()
		{ 
		}
	}).request();
}
function show_karma( app , cid , uid , place )
{
	var url = '/ajax/karma/' + app + '/' + cid + '/' + uid ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			$(place).setHTML(res);
		}
	}).request();
}
function update_widgets( folder )
{
	var url = '/ajax/update_widgets/' + folder ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			var back = parseInt(res , 10 );
			if( isNaN(back) )
			{
				alert( res );
				return;
			}
			if( back > 0 )
			{
				alert('成功更新'+back+'插件');
			}
			if( back == 0 )
			{
				alert('暂无更新');
			}
		}
	}).request();
}
function del_app_snap( folder )
{
	var url = '/ajax/del_app_snap/' + folder ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			if( res == '' )
			{
				$('app_snap_control').setHTML('<font class="gray">暂无缩图</font>');
			}
			else
			{
				alert(res);
			}
		}
	}).request();
}
function show_shop_cate( id , itype )
{
	var url = '/ajax/show_shop_cate/'+id + '/'+itype ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('shop_cate').setHTML( res );
				}
			}).request();
}
function load_shop_extra( id )
{
	var bid = $('brands').value;
	$('shop_extra_div').setHTML( '<img src="/static/images/loading.gif">Loading Please Wait.' );
	$('shop_brand_opt').setHTML( '<img src="/static/images/loading.gif">Loading Please Wait.' );
	if( id == 0 )
	{
		$('shop_extra_div').setHTML( '' );
		load_shop_brand( id , bid );
	}
	else
	{
		var cid = $('item_id').value;
		var url = '/ajax/show_shop_extra_input/' + id + '/' + cid ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('shop_extra_div').setHTML( res );
					load_shop_brand( id , bid );
				}
			}).request();
	}
	
}
function load_shop_brand( id , bid )
{
	var url = '/ajax/load_shop_brand/'+id + '/' + bid ;
			var myajax = new Ajax(url,
			{
				data:foodata,method:'post' ,
				evalScripts:true,
				onComplete:function( res )
				{ 
					$('shop_brand_opt').setHTML( res );
				}
			}).request();
}
function set_shop_type( value )
{
	set( 'type' , value );
	var type = $('type').value;
	load_shop_extra( type  );
}
function shop_extra_field( id  , type )
{
	if( !$('notice_box') )
	{
		var wait_div = new Element('div');
		wait_div.setProperty('id','notice_box');
		wait_div.injectTop( $E('body') );
	}
	center_it('notice_box');
	$('notice_box').setHTML('<img src="/static/images/loading.gif">Loading Please Wait.');
	var para=new Object();
	if( $('extra_field_'+id) )
	{
		para.value = $('extra_field_'+id).value
	}
	else
	{
		para.value = 0;
	}
	para.type = type;
	para.id = id;
	var url = '/ajax/shop_extra_set/';
	var myajax = new Ajax(url,
	{
		data:para,method:'post' ,
		//evalScripts:true,
		onComplete:function( res )
		{ 
			show_notice_box( res , '设置字段信息' );
			run_script( res );
		}
	}).request();
}
function format_mutli_input( json_code , count )
{
	var html = '<table id="mutli_table">';
	if( json_code != 'null' )
	{
		var obj = Json.evaluate(json_code );
		for( i=1;i <= count ; i++ )
		{
			html += '<tr><td>文字'+i+'<br/><input type="text" name="type_values[name]['+i+']" style="width:100px" value="'+obj.name[i]+'"/></td><td>值'+i+'<br/><input type="text" name="type_values[value]['+i+']" style="width:100px" value="'+obj.value[i]+'"/></td></tr>';
		}
	}
	if( count < 3 )
	{
		for( i=count + 1;i <= 3 ; i++ )
		{
			html += '<tr><td>文字'+i+'<br/><input type="text" name="type_values[name]['+i+']" style="width:100px" value=""/></td><td>值'+i+'<br/><input type="text" name="type_values[value]['+i+']" style="width:100px" value=""/></td></tr>';
		}
	}
	html += '</table>';
	html += '<div id="mutli_button"><a href="JavaScript:void(0)" onclick="add_mutli_tabel_row('+i+')">增加选项</a></div>';
	$('mutli_input').setHTML( html );
}
function add_mutli_tabel_row( i )
{
	var row = $('mutli_table').insertRow(-1);
	x = row.insertCell(0);
	y = row.insertCell(1);
	x.innerHTML ='文字'+i+'<br/><input type="text" name="type_values[name]['+i+']" style="width:100px" value=""/>';
	y.innerHTML ='值'+i+'<br/><input type="text" name="type_values[value]['+i+']" style="width:100px" value=""/>';
	i++;
	$('mutli_button').setHTML('<a href="JavaScript:void(0)" onclick="add_mutli_tabel_row('+i+')">增加选项</a>');
}
function shop_extra_send( name )
{
	$(name).send({
		evalScripts:true,
		onComplete: function(res) {
		}
	});
}
function update_shop_extra_table( id , data )
{
	$('notice_box').remove();
	var obj = Json.evaluate( data );
	var html = '';
	if( obj.type == 'line' )
	{
		html = '<INPUT TYPE="text" NAME="extra['+id+']" class="text">';
	}
	else
	{
		var i = 1;
		while(obj.tvalue.name[i] != undefined )
		{
			if( obj.type == 'dropdown' )
			{
				html += '<OPTION VALUE="">'+obj.tvalue.name[i];
			}
			else
			{
				html += '<INPUT TYPE="'+obj.type+'" NAME="">'+obj.tvalue.name[i];
			}
			i++;
		}
		if( obj.type == 'dropdown' )
		{
			html = '<SELECT NAME="extra['+id+']">'+html+'</SELECT>';
		}
	
	}
	if( $('extra_field_'+id) )
	{
		$('extra_label_'+id).setHTML(obj.label);
		$('extra_input_'+id).setHTML(html);
		$('extra_field_'+id).value= obj.field;
		$('extra_action_'+id).setHTML= '<a href="JavaScript:void(0)" onclick="shop_extra_field('+id+' ,\''+obj.type+'\')">修改</a>&nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0)" onclick="del_shop_extra_row( this )">删除</a>';

	}
	else
	{
		var row = $('shop_extra_table').insertRow(-1);
		x = row.insertCell(0);
		y = row.insertCell(1);
		z = row.insertCell(2);
		x.width = 70;
		y.width = 200;
		x.innerHTML ='<div id="extra_label_'+id+'">'+obj.label+'</div>';
		y.innerHTML ='<div id="extra_input_'+id+'">'+html+'</div><INPUT TYPE="hidden" NAME="extra_field['+id+']" id="extra_field_'+id+'" value="'+obj.field+'">';
		z.innerHTML ='<div id="extra_action_'+id+'"><a href="JavaScript:void(0)" onclick="shop_extra_field('+id+' ,\''+obj.type+'\')">修改</a>&nbsp;&nbsp;&nbsp;<a href="JavaScript:void(0)" onclick="del_shop_extra_row( this )">删除</a></div>';
		id++;
		$('shop_type_button').setHTML('<a href="JavaScript:void(0)" onclick="shop_extra_field('+id+' ,\'line\')"><img src="/static/images/plus.gif">&nbsp;添加新字段</a>');
	}

}
function del_shop_extra_row(obj)
{
   var curRow = obj.parentNode.parentNode.parentNode;
   $('shop_extra_table').deleteRow(curRow.rowIndex);
}
function admin_shop_del( action , id )
{

	if( action == 'type' )
	{
		if(!confirm('确认删除商品类型及改类型下所有商品?') )
		{
			return;
		}
	}
	if( action == 'brands' )
	{
		if(!confirm('确认删除此品牌') )
		{
			return;
		}
	}
	if( action == 'cate' )
	{
		if(!confirm('确认删除此分类?') )
		{
			return;
		}
	}
	var url = '/ajax/admin_shop_del/'+action+'/'+id ;
	var myajax = new Ajax(url,
	{
		data:foodata,
		method:'post' ,
		evalScripts:true,
		onComplete:function(res)
		{ 
			if( res != '' )
			{
				alert(res);
			}
			else
			{
				window.location.reload();
			}
		}
	}).request();
}
function shop_cate_list( id )
{
	var url = '/ajax/shop_cate_list/'+id;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			$('shop_cate').setHTML(res);
			run_script( res );
		}
	}).request();
}
function shop_check_brand( check )
{
	var e= document.getElementsByName("brand[]");
	for(var i=0 ;i<e.length;i++)
	{
		e[i].disabled = check;
	}
}
function ajax_form_send( name )
{
	$(name).send({
		onComplete: function(res) 
		{
			if( res != '' )
			{
				alert(res);
			}
		}
	});

}

function fck_ajax_send( name , options )
{
	var obj = $(name);
	query = obj.toQueryString();
	obj.getElements('.fck').each( function( item , index ){ editor = FCKeditorAPI.GetInstance(item.id);query = query + '&' + encodeURIComponent(item.id) + '=' + encodeURIComponent( editor.EditorDocument.body.innerHTML );   }  );
	return new Ajax(obj.getProperty('action'), $merge({data: query }, options, {method: 'post'})).request();
}
function save_draft( name )
{
	$('is_active').value=0;
	$('item_notice').setHTML('<img src="/static/images/loading.gif">Loading Please Wait.');
	fck_ajax_send( name ,  { onComplete:function(res){show_draft_res(res)} } );
}
function show_draft_res( res )
{
	$('item_notice').setHTML('草稿最后保存于 '+res );
}
function shop_item_tag_list( id )
{
	var url = '/ajax/shop_item_tag_list/'+id;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			$('tag_list').setHTML(res);
		}
	}).request();
}
function del_shop_item_tag( cid , tid )
{
	var url = '/ajax/del_shop_item_tag/'+ cid + '/' + tid;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			shop_item_tag_list( cid )
		}
	}).request();
}
function add_shop_item_tag( cid )
{
	var url = '/ajax/add_shop_item_tag/'+ cid ;
	var para=new Object();
	para.tags = $('tags').value;
	var myajax = new Ajax(url,
	{
		data:para,method:'post' ,
		onComplete:function( res )
		{ 
			shop_item_tag_list( cid )
		}
	}).request();
}
function activation_nav( name , obj )
{
	var e = $(name).getChildren();
	for (var i = 0;i < e.length;i++)
	{ 
		if( $(obj.parentNode) == e[i] )
		{
			if( e[i].hasClass('out') == false )
			{
				e[i].addClass('out');
			}
		}
		else
		{
			if( e[i].hasClass('out') == true )
			{
				e[i].removeClass('out');
			}
		}

	} 
}
function show_widget_nav( wid , obj )
{
	activation_nav( 'widget_nav_'+wid , obj );
	var array = $$('.widget_'+wid+'_child_div');
	var active = obj.parentNode.id + '_child';
	array.each(function(item )
	{
		if( item.id == active )
		{
			$(item).setStyle('display','block');
		}
		else
		{
			$(item).setStyle('display','none');
		}
	});

}
function show_shop_nav( obj )
{
	var type =  obj.parentNode.id;
	var array = ['record','reply'];
	if( type == 'all' )
	{
		$('desp_info').setStyle('display','block');
		array.each(function(item )
		{
			$(item+'_info').setStyle('display','block');
			$(item+'_title').setStyle('display','block');
		});
	}
	else
	{
		$('desp_info').setStyle('display','none');
		array.each(function(item )
		{
			if( item == type )
			{
				$(item+'_title').setStyle('display','none');
				$(item+'_info').setStyle('display','block');
			}
			else
			{
				$(item+'_info').setStyle('display','none');
				$(item+'_title').setStyle('display','none');
			}
		});
	}
	activation_nav( 'shop_item_tab' , obj );
}
function shop_modify_reply( id , obj )
{
	cancel_shop_modify();
	var reply_obj = obj.parentNode.parentNode;
	var div = new Element('div');
	div.setProperty('id','shop_modify_div');
	div.injectAfter(reply_obj); 
	reply_obj.setStyle('display','none');
	$('shop_modify_div').setHTML('<center><img src="/static/images/loading.gif">Loading Please Wait.</center>');
	var url = '/ajax/shop_modify_reply/'+ id ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			$('shop_modify_div').setHTML( res )
		}
	}).request();
	
}
function cancel_shop_modify()
{
	if( $('shop_modify_div') )
	{
		$('shop_modify_div').remove();
		$$('.shop_reply_item').each( function( item )
		{
			if( item.getStyle('display') == 'none' )
			{
				item.setStyle('display','block');
			}
		});	
	}
}
function ajax_post_shop_reply( id )
{
	var info = $('modify_info').value;
	info = info.replace(/<\/?.+?>/g,"");
	info = info.replace("\n","<br/>");
	info = info.trim(); 
	if( info == '' )
	{
		alert('请正确填写修改内容');
		return;
	}
	$('reply_ajax_form').send({
		onComplete: function(res) {
			if( res == '' )
			{ 
				cancel_shop_modify();
				$('reply_item_'+id).setHTML(info);
			}
			else
			{
				cancel_shop_modify();
				alert( res );
			}
		}
	});
}
function show_shop_rreply_div( id , obj )
{
	cancel_rreply_div();
	var rreply_obj = $('rreply_item_'+id);
	var div = new Element('div');
	div.setProperty('id','shop_rreply_div');
	div.injectAfter(rreply_obj); 
	rreply_obj.setStyle('display','none');
	$('shop_rreply_div').setHTML('<center><img src="/static/images/loading.gif">Loading Please Wait.</center>');
	var url = '/ajax/shop_show_rreply/'+ id ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			$('shop_rreply_div').setHTML( res )
		}
	}).request();
	
}
function cancel_rreply_div()
{
	if( $('shop_rreply_div') )
	{
		$('shop_rreply_div').remove();
		$$('.shop_rreplys').each( function( item )
		{
			if( item.getStyle('display') == 'none' )
			{
				item.setStyle('display','block');
			}
		});	
	}
}
function ajax_post_shop_rreply( id )
{
	var rinfo = $('rinfo').value;
	rinfo = rinfo.replace(/<\/?.+?>/g,"");
	rinfo = rinfo.replace("\n","<br/>");
	rinfo = rinfo.trim(); 
	if( rinfo == '' )
	{
		alert('请正确填写回应内容');
		return;
	}
	$('rreply_ajax_form').send({
		evalScripts:true,
		onComplete: function(res) {
			cancel_rreply_div();
		}
	});
}
function shop_del_reply( id )
{
	if(!confirm('<?=_text('system_del_confirm');?>') )
	{
		return;
	}
	var url = '/ajax/shop_del_reply/'+ id ;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			if( res == '' )
			{
				window.location.reload();
			}
			else
			{
				alert(res);
			}
		}
	}).request();
}
function add_to_wishlist( cid , type , obj )
{
	var url = '/ajax/update_shop_wishlist/'+ cid + '/' + type;
	var myajax = new Ajax(url,
	{
		data:foodata,method:'post' ,
		onComplete:function( res )
		{ 
			if( res != '' )
			{
				float_notice( res );
			}
			else
			{
				var button_obj = $(obj.parentNode);
				if( type == 1 )
				{
					button_obj.setHTML('已收藏');
					float_notice( '已加入收藏' );
				}
				else
				{
					button_obj.setHTML('已加入Wishlist');
					float_notice( '已加入Wishlist' );
				}
			}
		}
	}).request();
}
function add_shop_item_shopcate( id , folder )
{
	if( id < 1 )
	{
		float_notice( '错误的ID' );
		return;
	}
	if( folder == '' )
	{
		float_notice( '错误 未安装购物车' );
		return;
	}
	var number = parseInt ( $('number').value);
	if( isNaN( number ) || number < 1 )
	{
		float_notice( '错误的数量.' );
		return;
	}
	
	location='/app/native/'+folder+'/insert/system/'+id +'/'+ number;
}
