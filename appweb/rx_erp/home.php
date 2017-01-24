<?php
wa::locale();
wa::htt();
wa::htt_title( 'Ruixun Enterprise Resource Planning' );
wa::get_signin_user( 'rx_signin', function( $q, $w )
{
	return wa::set_signin_user( $q, $w, 'rx_hr_staff' ) && wa::$user['resign'] == 0;
	
}) || wa::end_htt_signin( TRUE, function( $q )
{

	wa::$htt->body['style'] = 'overflow:hidden';
	wa::$htt->body->div->div[0]['style'] = 
	wa::$htt->body->div->div[2]['style'] = 
	wa::$htt->body->nav['style'] = 'display:none';
	wa::$htt->body->div['style'] = 'background:url("./appweb/rx_erp/canvas/image/2014-4-4-1-59-51.jpg")no-repeat scroll center center;padding:42px 0 160px 0';
	//wa::$htt->body->div['style'] = 'position:absolute;top:50%;left:50%;margin-top:-280px;margin-left:-180px';
	$q['action'] = '?/rx_erp/ajax_signin';
	$q['data-referer'] = '?/rx_erp';
	$q['data-tagname'] = 'rx_signin';
	$q->set_css([
		'width' => '360px',
		'margin' => '0 auto',
		'background' => 'rgba(255,255,255,0.4)',
		'padding-top' => '16px',
		'border-radius' => '4px',
		//'opacity' => '0.8',
		//'transform' => 'rotate(' . mt_rand( -90, 0 ) . 'deg)',
		'box-shadow' => '0 0 16px 4px rgba( 0, 0, 0, 0.6 )'
	]);
	$q->div = 'Ver 1.1';
	$q->div->set_css([
		'background' => 'url("./appweb/rx_erp/canvas/image/ruixun_logo.png")no-repeat scroll center center',
		'width' => '320px',
		'height' => '180px',
		'margin' => '0 auto',
		'color' => 'maroon',
		'font-size' => '14px',
		'text-align' => 'right',
		'font-weight' => 'bold'
	]);
	$q->button[0] = '我忘记密码了';
	$q->button['onclick'] = '$.go("?/rx_erp/fn_password_reset")';
	$q->button['class'] = 'r';
	//unset( $q->button[0] );
});
wa::$buffers->nav->div->a['onclick'] = 'return !$.setcookie_remove("rx_group")';
wa::$buffers->js[] = 'appweb/rx_erp/rx_websocket.js';
wa::htt_nav( wa::$user['name'], [
	[ '修改密码', '?/rx_erp(1)user_change_password', 'glyphicon-edit' ],
	[ '请假单申请入口', '?/rx_erp(1)user_afl' ],
	'divider',
	[ '注销', 'javascript:$.setcookie_remove("rx_signin"),$.go("?/rx_erp");', 'glyphicon-log-out' ]
], 1 );
// wa::htt_menu_link( $_ENV['language'], [
// 	[ 'English (en-US)', 'javascript:$.setcookie("wa_locale","en_US"),$.go();' ]
// ], 1 );
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'user_change_password':
		wa::$buffers->write->ins_user_change_password();
		break;
	case 'user_vote':
		include 'user_vote.php';
		break;
	case 'user_afl':
		wa::$buffers->write->ins_dialog( '提示', '该功能正在完善。。。' );
		break;
 	default:
 		include 'main.php';
};
exit;
?>