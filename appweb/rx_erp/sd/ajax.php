<?php
wa::$buffers = [ '无效数据!', 'warn' ];
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'sd_task_insert':
		$sd_services_editor += [
			'pkip' => [
				'test' => [ 7, 15, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
				'type' => 'text',
			],
			'pkno' => [
				'test' => [ 0, 16, '/^$|^[A-Z\d\-]+$/' ],
				'type' => 'text',
			],
			'sbqq' => [
				'test' => [ 1, 1024 ],
				'type' => 'text',
			],
			'sbstatus' => [
				'test' => [ 1, 32 ],
				'type' => 'text',
			],
			'user_name' => [
				'test' => [ 1, 32 ],
				'type' => 'text',
			]
		];

		$sd_current_qq
		&& ( $q = wa::get_form_post( $sd_services_editor ) )
		&& sd_no_serviced( $q['pkip'] )
		&& ( $w = wa::$sql->get_only( 'rx_rd_device', 'only', $q['pkno'] ) )
		&& $w['room'] === '佛山德胜机房'
		&& $w['sb_type'] !== '云设备'
		&& ( $q['sbstatus'] === '出租' || $q['sbstatus'] === '测试' || $q['sbstatus'] === '托管' || $q['sbstatus'] === '变更' )
		&& sd_riqq_find( $q['sbqq'], $q['riqq'] )
		&& wa::$sql->q_task_callback(function() use( &$q, &$sd_service_qq, $sd_current_qq )
		{
			$w = $q['describe'];
			unset( $q['describe'], $q['sbstatus'], $q['sbqq'] );
			$q['time'] = time();
			$q['userid'] = wa::$user['username'];
			$q['imqq'] = $sd_current_qq;
			$q['note'] = $sd_service_qq[ $sd_current_qq ] . $sd_current_qq . "\n" . $q['note'];
			if ( $q['to'] == 2 || $q['to'] == 3 )
			{
				//unset( $q['to'] );
				$q['only'] = wa::short_hash( join( $q ) );
				$w = wa::$sql->q_insert( 'rx_sd_task', $q );
				return $w && wa::$buffers = [
					0 => '/td_task_wait',
					1 => 'rx_ws_callback',
					'send' => 'notify_pack',
					'come' => 'close'
				];
			};
			//unset( $q['to'] );

			$q['only'] = rx_pack_send( '德胜机房运维中心', $w, $q['pkno'], 'ermsd_pack_task_create', [
				'type' => 'k',
				'mark' => $w,
				'pkip' => $q['pkip'],
				'pkno' => $q['pkno'],
				'sbxx' => substr( $q['sbxx'], 0, strpos( $q['sbxx'], '主IP:' ) ) . substr( $q['sbxx'], strpos( $q['sbxx'], '主板:' ) ),
				'note' => $q['note']
			], 'ermsd_pack_task_sd_task_done' );

			return $q['only'] && rx_pack_call( $q['only'], 'unpack' ) && wa::$sql->q_insert( 'rx_sd_task', $q ) && wa::$buffers = [
				0 => '/sd_task_wait',
				1 => 'rx_ws_callback',
				'send' => 'notify_pack',
				'come' => 'close'
				//'come' => '?/rx_erp(1)sd_task_wait_my'
			];
		});
		break;
	case 'sd_task_wait_to':
		isset( $_GET[2] )
		&& ( $q = wa::$sql->get_only( 'rx_hr_staff', 'username', $_GET[2] ) )
		&& $q['group'] === '客服部'
		&& strpos( $q['rx_action'], 'sd_task_wait_my' ) !== FALSE
		&& wa::$sql->q_update( 'rx_sd_task', [ 'userid' => $q['username'] ], 'where done is null and userid=?s', wa::$user['username'] )
		&& wa::$buffers = [
			0 => '/sd_task_wait',
			1 => 'rx_ws_callback',
			'send' => 'reload ',
			'come' => '?/rx_erp(1)sd_task_wait_my'
		];
		break;
	case 'sd_task_delete':
		isset( $_GET[2] ) && wa::$sql->q_task_callback(function( $q )
		{
			if ( wa::$sql->q_delete( 'rx_public_pack', 'where only=?s and start is null limit 1', $q ) )
			{
				$w = wa::$sql->q_delete( 'rx_sd_task', 'where only=?s limit 1', $q );
				return $w && wa::$buffers = [
					0 => '/sd_task_wait',
					1 => 'rx_ws_callback',
					'send' => 'reload ',
					'come' => '?/rx_erp(1)sd_task_wait'
				];
			};
		}, $_GET[2] );
		break;
	case 'sd_task_done':
		isset( $_GET[2] )
		&& ( $q = wa::$sql->get_only( 'rx_sd_task', 'only', $_GET[2] ) )
		&& $q['done'] === NULL
		&& $q['over'] > 4
		&& wa::$sql->q_update( 'rx_sd_task', [ 'done' => time() ], 'where only=?s', $q['only'] )
		&& wa::$buffers = [
			0 => '/sd_task_wait',
			1 => 'rx_ws_callback',
			'send' => 'reload ',
			'come' => '?/rx_erp(1)sd_task_wait'
		];
		break;
	case 'sd_staff_update':
		wa::$buffers = rx_staff_update( '客服部' );
		break;
	case 'rx_notice_insert':
		rx_notice_insert( '客服部' );
		break;
 	default:
 		wa::$buffers = [ '未定义功能!', 'warn' ];
};
wa::end_str_status();
exit;
?>