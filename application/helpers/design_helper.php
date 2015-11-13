<?php  

if (!defined('BASEPATH')) exit('No direct script access allowed');

function format_design_row( $rows , $finfo )
{
	$bid = $finfo['id'];
	$html = NULL;
	if( $rows && is_array( $rows ) )
	{
		foreach( $rows as $v )
		{
			$html .= '<li><br clear="all"/>';
			if( is_array( $v ) )
			{
				$html .= '<span class="r"><a href="JavaScript:void(0)" onclick="del_design_block( '.$bid.','.$v['id'].')"><IMG src="/static/images/cross.gif"></a></span><img src="/static/images/movearrow.gif" class="Drag" style="float:left">';
				$html .= format_design_display( $v );
			}
			else
			{
				$html .= '<span class="r"><a href="JavaScript:void(0)" onclick="del_design_block('.$bid.' , \''.$v.'\')"><IMG src="/static/images/cross.gif"></a></span><img src="/static/images/movearrow.gif" class="Drag" style="float:left">';
				$html .= format_design_extra_display( $v , $finfo );
			}
			$html .= '</li>';
		}
		
	}
	return $html;
}
function format_design_display( $line )
{
	$html = NULL;
	if( $line['key'] == 'pic' )
	{
		$style = $line['custom_css']?' style="'.$line['custom_css'].'" ':NULL;
		$html = '<img src="/static/images/design/images.png" '.$style.' alt="用户上传图片" />';
	}
	elseif( $line['key'] == 'title' )
	{
		$html = '<h5 class="w2">标题</h5>';
	}
	elseif( $line['type'] == 'line' )
	{
		$show = $line['custom_css']?'<font style="'.$line['custom_css'].'">单行内容</font>':'<font style="color:#ccc">单行内容</font>';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.'&nbsp;'.$show;
	}
	elseif( $line['type'] == 'multi-line' )
	{
		$show = $line['custom_css']?'<div style="'.$line['custom_css'].'">用户输入内容[多行]<br/>用户输入内容[多行]</div>':'<div style="color:#ccc">用户输入内容[多行]<br/>用户输入内容[多行]</div>';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.$show;
	}
	elseif( $line['type'] == 'file' )
	{
		$show = $line['custom_css']?'<font style="'.$line['custom_css'].'"><a href="JavaScript:void(0)">点击下载</a></font>':'<a href="JavaScript:void(0)">点击下载</a>';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.':&nbsp;'.$show;
	}
	elseif( $line['type'] == 'pic' )
	{
		$style = $line['custom_css']?' style="'.$line['custom_css'].'" ':NULL;
		$show = '<img src="/static/images/design/images.png" '.$style.' alt="用户上传图片" />';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.'<br/>'.$show;
	}
	elseif( $line['type'] == 'multi-pic' )
	{
		$style = $line['custom_css']?' style="'.$line['custom_css'].'" ':NULL;
		$show = '<img src="/static/images/design/images.png" '.$style.' alt="用户上传图片" /><br/><img src="/static/images/design/images.png" '.$style.' style="float:left;width:50px;" alt="用户上传图片" /><img src="/static/images/design/images.png" '.$style.' style="float:left;width:50px;" alt="用户上传图片" /><br clear="all"/>';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.'<br/>'.$show;
	}
	elseif( $line['type'] == 'dropdown' || $line['type'] == 'radio' || $line['type'] == 'checkbox' )
	{
		$show = $line['custom_css']?'<font style="'.$line['custom_css'].'">用户选择值</font>':'<div style="color:#ccc">用户选择值</font>';
		$label = $line['label']?$line['label']:'默认标题';
		$html = $label.':&nbsp;'.$show;
	}
	else
	{
		$html = $line['label']?$line['label']:'默认标题';
	}
	return '<div>'.$html.'</div>';
}
function format_design_extra_display( $key , $finfo = NULL )
{
	if( $key == 'state' && $finfo['state'] )
	{
		$app = explode('|',  $finfo['state']);
		$button = NULL;
		foreach( $app as $v )
		{
			$button .= '<input type="button" class="button" value="'.$v.'" />&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		$html = $button;
	}
	elseif( $key == 'view_people' )
	{
		$html = '<div><h5 class="w2">最近访问的用户</h5><br/>';
		$user = '<div class="app_user_div"><img src="/static/images/user_normal_icon.default.gif" class="icon" /><br/><a href="JavaScript:void(0)">用户</a></div>';
		for( $i = 0 ; $i < 5 ; $i ++ )
		{
			$html .= $user;
		}
		$html .= '<br clear="all"/></div>';
	}
	elseif( $key == 'comments' )
	{
		$html = '<div class="comments_title">xxx的评论</div>';
		$html .= '<TABLE><TR><TD valign="top" width="52px"><img src="/static/images/user_normal_icon.default.gif" class="icon" /></TD><TD valign="top"><a href="JavaScript:void(0)">用户</a>&nbsp;&nbsp;&nbsp;<span class="post_time">xxxx-xx-xx xx:xx</span><br clear="all"><div style="color:#ccc">评论内容</div></TD></TR><TR><TD valign="top" width="52px"><img src="/static/images/user_normal_icon.default.gif" class="icon" /></TD><TD valign="top"><a href="JavaScript:void(0)">用户</a>&nbsp;&nbsp;&nbsp;<span class="post_time">xxxx-xx-xx xx:xx</span><br clear="all"><div style="color:#ccc">评论内容</div></TD></TR><TR><TD valign="top" width="52px"><img src="/static/images/user_normal_icon.default.gif" class="icon" /></TD><TD valign="top"><a href="JavaScript:void(0)">用户</a>&nbsp;&nbsp;&nbsp;<span class="post_time">xxxx-xx-xx xx:xx</span><br clear="all"><div style="color:#ccc">评论内容2<br>评论内容2 </div><a href="JavaScript:void(0)">(全文)</a></TD></TR></TABLE>';
		$html .= '<DIV class=pager>&nbsp;<A title=首页><IMG src="/static/images/arrow_fat_left.gif"></A>&nbsp;&nbsp;<A title=上一页><IMG src="/static/images/arrow_dash_left.gif"></A>&nbsp;<A class=current>&nbsp;1&nbsp;</A><A href="JavaScript:void(0)">&nbsp;2&nbsp;</A><A href="JavaScript:void(0)">&nbsp;3&nbsp;</A><A href="JavaScript:void(0)">&nbsp;4&nbsp;</A><A href="JavaScript:void(0)">&nbsp;5&nbsp;</A><A href="JavaScript:void(0)">&nbsp;6&nbsp;</A><A href="JavaScript:void(0)">&nbsp;7&nbsp;</A><A href="JavaScript:void(0)">&nbsp;8&nbsp;</A><A href="JavaScript:void(0)">&nbsp;9&nbsp;</A><A>&nbsp;...&nbsp;</A><A title=下一页 href="JavaScript:void(0)"><IMG src="/static/images/arrow_dash_right.gif"></A>&nbsp;<A title=末页 href="JavaScript:void(0)"><IMG src="/static/images/arrow_fat_right.gif"></A>&nbsp;<br/><br/></DIV>';
	}
	elseif( $key == 'star' )
	{
	
		$html = '<div><b>共有999人评分</b><br/><br/><ul class="rating star3 l"></ul><span class="l">&nbsp;&nbsp;&nbsp;2.6分</span><br clear="all"/></div>';
	}
	elseif( $key == 'star_list' )
	{
		$html = '<br/><div>
		<span class="stars_view stars5" title="力荐"/></span><div style="margin-top:4px;margin-left:3px;background:#e8e8e8;width:50px;height:8px;float:left"><div style="float:left;width:20px;height:8px;" class="design_display_bg"></div></div><div style="float:left;font-size:9px;">111人</div><br clear="all"/>
		<span class="stars_view stars4" title="推荐"/></span><div style="margin-top:4px;margin-left:3px;background:#e8e8e8;width:50px;height:8px;float:left"><div style="float:left;width:10px;height:8px;" class="design_display_bg"></div></div><div style="float:left;font-size:9px;">61人</div><br clear="all"/>
		<span class="stars_view stars3" title="一般"/></span><div style="margin-top:4px;margin-left:3px;background:#e8e8e8;width:50px;height:8px;float:left"><div style="float:left;width:8px;height:8px;" class="design_display_bg"></div></div><div style="float:left;font-size:9px;">41人</div><br clear="all"/>
		<span class="stars2 stars_view" title="较差"/></span><div style="margin-top:4px;margin-left:3px;background:#e8e8e8;width:50px;height:8px;float:left"><div style="float:left;width:10px;height:8px;" class="design_display_bg"></div></div><div style="float:left;font-size:9px;">21人</div><br clear="all"/>
		<span class="stars1 stars_view" title="很差"/></span><div style="margin-top:4px;margin-left:3px;background:#e8e8e8;width:50px;height:8px;float:left"><div style="float:left;width:3px;height:8px;" class="design_display_bg"></div></div><div style="float:left;font-size:9px;">12人</div><br clear="all"/></div>';
	}
	elseif( $key == 'com_button' )
	{
		$html = '<div><cneter><INPUT TYPE="button" class="button" value=" 我要写评论 "></centr></div>';
	}
	elseif( $key == 'vote_show' )
	{
		$extra_show = NULL;
		if(  $finfo['state'] )
		{
			$app = explode('|',  $finfo['state']);
			$html = '<h5 class="w2">谁'.$app[0].'这'.$finfo['title'].'</h5><br/>';
			foreach( $app as $k => $v )
			{
				if( $k == 0 )
				{
					$html .= '<div class="app_user_div"><center><img src="/static/images/user_normal_icon.default.gif" class="icon" /><br/><a href="JavaScript:void(0)">用户</a></center></div><div class="app_user_div"><center><img src="/static/images/user_normal_icon.default.gif" class="icon" /><br/><a href="JavaScript:void(0)">用户</a></center></div><div class="app_user_div"><center><img src="/static/images/user_normal_icon.default.gif" class="icon" /><br/><a href="JavaScript:void(0)">用户</a></center></div><br clear="all"/>';
					$html .= '<a href="JavaScript:void(0)"> > 还有'. rand(1,99).'人'.$v.'</a><br/>';
				}
				else
				{
					$html .= '<a href="JavaScript:void(0)"> > '. rand(1,99).'人'.$v.'</a><br/>';
				}
				
			}
			
		}
	}
	elseif( $key == 'adminlink')
	{
		$html = '<span class="r"><a href="JavaScript:void(0)">修改</a>&nbsp;&nbsp;<a href="JavaScript:void(0)">删除</a>&nbsp;&nbsp;</span>';
	}
	elseif( $key == 'price' )
	{
		$html = '<div><INPUT TYPE="button" class="button" value=" 加入购物车 "></div>';
	}
	else
	{
		$html = $key;
	}
	$html = '<div>'.$html.'</div>';
	return $html;
}
function get_old_fields( $id )
{
	if( check_app_install($id) )
	{
		$fields = lazy_get_data( "SHOW COLUMNS FROM app_content_".intval($id) );
		return $fields;
	}
	else
	{
		return NULL;
	}
	
}
function check_app_install( $id )
{
	$sql = "SHOW TABLES LIKE 'app_content_".intval($id)."' ";
	return lazy_get_var($sql);
}
function make_desgin_display_order( $id_order , $layout , $sorder = array()  )
{
	if( $layout == 4 )
	{
		$sorder[1] = $id_order;
		$sorder[1][] = 'view_people';
		$sorder[1][] = 'adminlink';
		$sorder[2][] = 'star';
		$sorder[2][] = 'vote_show';
		$sorder[2][] = 'star_list';
		$sorder[2][] = 'com_button';
		$sorder[3][] = 'comments';
	}
	elseif( $layout == 3 )
	{
		$sorder[1] = $id_order;
		$sorder[1][] = 'view_people';
		$sorder[1][] = 'adminlink';
		$sorder[0][] = 'star';
		$sorder[0][] = 'vote_show';
		$sorder[0][] = 'star_list';
		$sorder[0][] = 'com_button';
		$sorder[2][] = 'comments';
	}
	elseif( $layout == 2 )
	{
		$sorder[0] = $id_order;
		$sorder[0][] = 'view_people';
		$sorder[0][] = 'adminlink';
		$sorder[1][] = 'star';
		$sorder[1][] = 'vote_show';
		$sorder[1][] = 'star_list';
		$sorder[1][] = 'com_button';
		$sorder[2][] = 'comments';
	}
	else
	{
		$sorder[0] = $id_order;
		$sorder[0][] = 'view_people';
		$sorder[0][] = 'adminlink';
		$sorder[0][] = 'star';
		$sorder[0][] = 'vote_show';
		$sorder[0][] = 'star_list';
		$sorder[0][] = 'com_button';
		$sorder[1][] = 'comments';
	}
	return $sorder;
}
?>