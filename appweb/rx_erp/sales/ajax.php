<?php
wa::$buffers = [ '无效数据!', 'warn' ];
wa::set_action_post( 'sales_staff_update', function()
{
	wa::$buffers = rx_staff_update( '销售部' );







})::set_action_post( 'sales_device_get', function() use( &$sales_device_get )
{
	if ( isset( $_GET[2] ) && ( $q = wa::get_form_post( $sales_device_get ) ) )
	{
		$q['pw_time'] = $q['pw_starts'] = time();
		$q['pw_editid'] = wa::$user['username'];
		$q['status'] = '测试';
		if ( wa::$sql->q_update( 'rx_rd_device', $q, 'where only=?s and status="空闲" limit 1', $_GET[2] ) )
		{
			wa::$buffers = [ 'reload', 'close' ];
		}
		else
		{
			wa::$buffers = [ '提取失败！可能已经被提取了!', 'warn' ];
		}
	};

})::set_action_post( 'sales_device_services', function() use( &$rx_work_post )
{
	// $q  = wa::get_form_post( $rx_work_post );
	// if ( isset( $_GET[2] ) && ( $w = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) && $w['pw_userid'] == wa::$user['name'] )
	// {
	// 	if ( $q['riqq'] && strpos( "\r\n" . $w['pw_imid'] . "\r\n", "\r\n" . $q['riqq'] . "\r\n" ) === FALSE )
	// 	{
	// 		wa::$buffers = [ '保障QQ无效!', 'warn' ];
	// 		return;
	// 	};
	// 	$q['to'] = explode( ',', $q['to'], 2 );
	// 	$q['note'] = $q['to'][1] . "\n" . $q['note'];
	// 	$q['to'] = $q['to'][0];
	// 	$q['time'] = time();
	// 	$q['user'] = wa::$user['username'];
	// 	$q['imqq'] = wa::$user['office_imid'];
	// 	$q['from'] = wa::$user['group'];
	// 	$q['no'] = $w['only'];
	// 	$q['ip'] = explode( ' ', $w['ip_main'], 2 )[0];
	// 	$q['info'] = json_encode( $w, JSON_UNESCAPED_UNICODE );
	// 	$q['only'] = wa::short_hash( join( $q ) );
	// 	if ( $q['to'] === 'ermsd' )
	// 	{




			

	// 	}
	// 	else
	// 	{
	// 		wa::$sql->q_insert( 'rx_public_task', $q ) && wa::$buffers = [ 'reload', 'close' ];
	// 	};
	// };
	wa::$buffers = [ '这个功能已经被锁定!', 'warn' ];

})::end_action(function()
{
	wa::$buffers = [ '未定义功能!', 'warn' ];

}, 'wa::end_str_status' );
