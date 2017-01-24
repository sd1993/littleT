<?php
// ( isset( $_SERVER['REQUEST_SCHEME'] )
// 	&& $_SERVER['REQUEST_SCHEME'] === 'https' ) || wa::end_string( '为了数据不被第三方所利用请 HTTPS 协议' );
function rx_kiss_signin( $q, $w )
{
	if ( wa::set_signin_user( $q, $w, 'rx_hr_staff' ) && wa::$user['resign'] == 0 )
	{
		setcookie( 'userid', wa::$user['username'] );
		return TRUE;
	};
	return FALSE;
	//return wa::set_signin_user( $q, $w, 'rx_hr_staff' ) && wa::$user['resign'] == 0;
}
function rx_kiss_signin_home()
{
	wa::locale();
	wa::htt();
	wa::htt_title( 'Sign In' );
	wa::get_signin_user( 'rx_signin', 'rx_kiss_signin' ) || wa::end_htt_signin( TRUE, function( $q )
	{
		wa::$htt['style'] = 'background:darkslateblue;overflow:hidden';
		wa::$htt->body->div->div[0]['style'] = 
		wa::$htt->body->div->div[2]['style'] = 
		wa::$htt->body->nav['style'] = 'display:none';
		wa::$htt->body->div['style'] = 'padding:42px 0 160px 0';
		$q['action'] = '?/rx_kiss/ajax_signin';
		$q['data-referer'] = '?/rx_kiss';
		$q['data-tagname'] = 'rx_signin';
		$q->set_css([
			'background' => 'white',
			'width' => '360px',
			'margin' => '0 auto',
			'padding-top' => '16px',
			'border' => '1px solid black',
			'box-shadow' => '0 0 8px 2px rgba( 0, 0, 0, 0.6 )'
		]);
		$q->div = 'v1.4';
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
	});
	wa::htt_title( '锐讯网络' );
	wa::htt_nav( wa::$user['name'], [
		[ '修改密码', '?/rx_kiss(1)user_change_password', 'glyphicon-edit' ],
		'divider',
		[ '注销', 'javascript:$.setcookie_remove("rx_signin"),$.go("?/rx_kiss");', 'glyphicon-log-out' ]
	], 1 );
}
function rx_kiss_signin_ajax()
{
	wa::locale();
	wa::get_signin_user( 'rx_signin', 'rx_kiss_signin' ) || wa::end_string( wa_unauthorized );
}
class rx_kiss
{
	function rx_kiss_chat()
	{
		wa::$htt['style'] = 'overflow:hidden';
		wa::$buffers->css[] = 'appweb/rx_kiss/chat.css';
		wa::$buffers->js[] = 'appweb/rx_kiss/websocket.js';
		wa::$buffers->js[] = 'appweb/rx_kiss/chat.js';


		wa::$buffers->script = '$(rx_chat_init_staffs)';



		$q = wa::$buffers->write->tag_table()->tbody;
		$w = &$q->td;
		$w['class'] = 'rx_chat_public';
		$w->div = '载入聊天记录..';

		$w = $w->tag_table();
		$w['class'] = 'rx_chat_editor';
		$e = &$w->tbody->tr->td;
		$e['colspan'] = 2;
		$e->tag_select( [ '并没有什么卵用的选择' ] );
		$e->tag_select( [ '并没有什么卵用的选择' ] );
		$e->tag_select( [ '并没有什么卵用的选择' ] );


		$r = $e->tag_button( '超级性感的表情包' );
		$r['onmousedown'] = 'wa.menu_act(this)';
		$r['data-target'] = 'wa_htt_id0';
		$r['data-fixed'] = 1;
		$r['data-center'] = 1;
		$r['data-offset_y'] = -2;
		$r = &wa::$htt->body->nav->div[];
		$r['id'] = 'wa_htt_id0';
		$r->set(function( $q )
		{
			$w = 'appweb/rx_kiss/pack_feel/';
			foreach ( scandir( $w ) as $e )
			{
				if ( $e[0] !== '.' )
				{
					$q->img[]['src'] = $w . $e;
				};
			};
		});



		
		$w->tfoot->tr->td->textarea['placeholder'] = '请输入..';
		$w->tfoot->tr->td->textarea['onkeydown'] = 'event.altKey&&event.keyCode===83&&rx_chat_send_to(this)';
		$e = &$w->tfoot->tr->td[];
		$e['nowrap'] = TRUE;
		$e->pre = "发送\n(Alt+S)";
		$e->pre['onclick'] = 'rx_chat_send_to(this.parentNode.parentNode.firstChild.firstChild)';


		
		// $e['style'] = 'width:100%';
		// $e->tfoot->tr->td->textarea = 'asd';
		// $e->tfoot->tr->td->textarea['style'] = 'width:100%';
		// $e = &$e->tfoot->tr->td[];
		// //$e['']
		// $e['style'] = 'width:100px;background:blue';
		// $e->div = '发送';


		//$e = &$w->div[];
		//$e->textarea = 'asd';


		//['style'] = 'height:210px';

		$e = &$q->td[];
		$e['class'] = 'rx_chat_staff_lists';
		$e['nowrap'] = TRUE;
		$e[0] = '载入员工列表..';


		// $r = &$e->div;
		// $r->span = '载入员工列表..';
		// $r = &$r->a;
		// $r['href'] = 'javascript:alert(1);';
		// $r['class'] = 'glyphicon-refresh';
		// $r['style'] = 'float:right;text-decoration:none;color:black';




	}

	function rx_kiss_chat_send_to( string $q, string $w = NULL ):array
	{
		$q = [
			'only' => substr( microtime( TRUE ) * 10000, 0, 14 ),
			'from' => wa::$user['username'],
			'content' => $q
		];
		$q['only'] = $q['only'] . wa::short_hash( join( $q ) );
		$e = [ 'rx_chat_send_callback', 'rx_script_callback',
			'cbws' => 'send_public',
			'time' => substr( $q['only'], 0, 10 ),
			'from' => $q['from'],
			'content' => $q['content']
		];
		if ( $w !== NULL )
		{
			if ( preg_match( WA_USER_MATCH, $w ) )
			{
				$q['to'] = $w;
				$e['cbws'] = 'send_userid ' . $w;
			}
			else
			{
				$q['group'] = $w;
				$e['cbws'] = 'send_group ' . $w;
			};
		};
		return wa::$sql->q_insert( 'rx_kiss_chat', $q ) ? $e : [ '消息写入失败，请重试！' ];
	}


}