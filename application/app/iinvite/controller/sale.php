<?php 
if( !is_login() )
{
	die('请登陆后操作');
}
if( !c('invite_active') )
{
	die('目前网站没有开发邀请注册');
}
$CI = &get_instance();
$CI->load->model('Invite_model', 'invite', TRUE);

$limit = intval( $CI->invite->get_invite_limit() );

$price = '&nbsp;'.intval( c('invite_price') ).'&nbsp;'.( c('invite_use_gold') ?  _text('system_gold_money') : _text('system_silver_money')) ;

$html = '<div><span class="r" style="margin:5px;"><a href="JavaScript:void(0)" onclick="app_close_wait_box()"><img src="/static/images/cross.gif" /></a></span><br clear="all"><p class="buy_invite_notice">'._text('invite_buy_notice',$limit,$price ).'</p>
<P class="item"><LABEL>'._text('invite_buy_number').'</LABEL>&nbsp;<INPUT class="text" value="" id="number" name="number"></P>
<P class="act"><INPUT class="button" type="button" value="购买" onclick="app_buy_invite( $(\'number\').value )"></P></div>';

echo $html;
?>