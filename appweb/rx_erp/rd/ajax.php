<?php
wa::$buffers = [ '无效数据!', 'warn' ];
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	// case 'rx_pack':
	// 	isset( $_GET[2], $_GET[3] ) && rx_pack_call( $_GET[3], $_GET[2] );
	// 	break;
	case 'rd_ip0_insert':
		$q = wa::get_form_post( $rd_form_ip0_editor );
		if ( $q && $q['ip_suffix_start'] < $q['ip_suffix_end'] )
		{
			wa::$buffers = wa::$sql->q_task_callback(function( $w ) use( &$q )
			{
				$e = hexdec( bin2hex( inet_pton( $q['ip_prefix_address'] . '.0' ) ) );
				$q['last_update_time'] = $q['time'] = time();
				$q['last_update_userid'] = wa::$user['username'];
				$q['only'] = sprintf( '%08x', $e | $q['ip_suffix_start'] );
				$status = $q['status'] ? $q['status'] : '空闲';
				unset( $q['status'] );
				if ( wa::$sql->q_insert( 'rx_rd_ip0', $q ) != 1 )
				{
					return FALSE;
				};
				$r = $q['ip_suffix_start'];
				$t = [ FALSE, FALSE, FALSE ];
				while ( $r <= $q['ip_suffix_end'] )
				{
					$y = [
						'only' => sprintf( '%08x', $e | $r ),
						'ip' => $q['ip_prefix_address'] . '.' . $r++,
						'status' => $status,
						'note' => '',
						'last_update_time' => $q['time'],
						'last_update_userid' => $q['last_update_userid']
					];
					switch ( $y['ip'] )
					{
						case $q['network_id']:
							$t[0] = TRUE;
							$y['status'] = '使用';
							$y['note'] = '网络号';
							break;
						case $q['gateway']:
							$t[1] = TRUE;
							$y['status'] = '使用';
							$y['note'] = '网关';
							break;
						case $q['broadcast_address']:
							$t[2] = TRUE;
							$y['status'] = '使用';
							$y['note'] = '广播地址';
							break;
					};
					if ( $w->q_insert( 'rx_rd_ip1', $y ) != 1 )
					{
						return FALSE;
					};
				};
				return $t[0] && $t[1] && $t[2];
			}) ? [ '?/rx_erp(1)rd_ip0_admin', 'goto' ] : [ '数据写入失败!请检查数据的有效性.', 'warn' ];
		};
		break;
	case 'rd_ip0_update':
		unset( $rd_form_ip0_editor['ip_prefix_address'] );
		unset( $rd_form_ip0_editor['ip_suffix_start'] );
		unset( $rd_form_ip0_editor['ip_suffix_end'] );
		isset( $_GET[2] ) && wa::get_form_post_callback( $rd_form_ip0_editor, function( $w, $q )
		{
			$e = wa::$sql->get_only( 'rx_rd_ip0', 'only', $_GET[2] );
			if ( !$e )
			{
				return;
			};
			$r = [
				'device' => NULL,
				'expire' => 0,
				'status' => '空闲',
				'note' => '',
				'last_update_time' => time(),
				'last_update_userid' => wa::$user['username'],
			];
			if ( $w['gateway'] != $e['gateway'] )
			{
				$q->q_update( 'rx_rd_ip1', $r, 'where ip=?s limit 1', $e['gateway'] );
				$q->q_update( 'rx_rd_ip1', [
					'device' => NULL,
					'expire' => 0,
					'status' => '使用',
					'note' => '网关',
					'last_update_time' => $r['last_update_time'],
					'last_update_userid' => $r['last_update_userid'],
				], 'where ip=?s limit 1', $w['gateway'] );
			};
			if ( $w['network_id'] != $e['network_id'] )
			{
				$q->q_update( 'rx_rd_ip1', $r, 'where ip=?s limit 1', $e['network_id'] );
				$q->q_update( 'rx_rd_ip1', [
					'device' => NULL,
					'expire' => 0,
					'status' => '使用',
					'note' => '网络号',
					'last_update_time' => $r['last_update_time'],
					'last_update_userid' => $r['last_update_userid'],
				], 'where ip=?s limit 1', $w['network_id'] );
			};
			if ( $w['broadcast_address'] != $e['broadcast_address'] )
			{
				$q->q_update( 'rx_rd_ip1', $r, 'where ip=?s limit 1', $e['broadcast_address'] );
				$q->q_update( 'rx_rd_ip1', [
					'device' => NULL,
					'expire' => 0,
					'status' => '使用',
					'note' => '广播地址',
					'last_update_time' => $r['last_update_time'],
					'last_update_userid' => $r['last_update_userid'],
				], 'where ip=?s limit 1', $w['broadcast_address'] );
			};
			$w['status'] && $q->q_update( 'rx_rd_ip1', [ 'status' => $w['status'] ], 'where device is null and only>=?s and only<=?s',
				$e['only'],
				sprintf( '%.6s%02x', $e['only'], $e['ip_suffix_end'] ) );
			unset( $w['status'] );
			$w['last_update_time'] = time();
			$w['last_update_userid'] = wa::$user['username'];
			return $q->q_update( 'rx_rd_ip0', $w, 'where only=?s limit 1', $e['only'] );
		}) && wa::$buffers = [ '?/rx_erp(1)rd_ip0_admin', 'goto' ];
		break;
	case 'rd_ip0_delete':
		if ( isset( $_GET[2] ) && ( $q = wa::$sql->get_only( 'rx_rd_ip0', 'only', $_GET[2] ) ) )
		{
			wa::$buffers = wa::$sql->q_task_callback(function( $w ) use( &$q )
				{
					$e = $w->q_sent( 'delete from rx_rd_ip1 where status="空闲" and only>=?s and only<=?s',
						substr( $q['only'], 0, 8 ),
						sprintf( '%.6s%02x', $q['only'], $q['ip_suffix_end'] ) );
					return $e == ( $q['ip_suffix_end'] - $q['ip_suffix_start'] + 1 ) && $w->q_delete( 'rx_rd_ip0', 'where only=?s limit 1', $q['only'] ) == 1;
				})
				? [ '?/rx_erp(1)rd_ip0_admin', 'goto' ]
				: [ '数据删除失败!请检查该段IP全部处于空闲状态.', 'warn' ];
		};
		break;
	case 'rd_ip1_edit':
		$rd_form_ip1_editor['status']['test'][] = '/^(' . join( '|', $rd_form_ip1_status ) . ')$/';
		if ( isset( $_GET[2] ) && ( $q = wa::get_form_post( $rd_form_ip1_editor ) ) && ( $w = wa::$sql->get_only( 'rx_rd_ip1', 'only', $_GET[2] ) ) && $w['device'] === NULL )
		{
			$q['select'] || $q['select'] = NULL;
			$q['last_update_time'] = time();
			$q['last_update_userid'] = wa::$user['username'];
			$q['status'] != '空闲' && trim( $q['note'] ) == '' && $q['note'] = wa::$user['name'];
			if ( wa::$sql->q_update( 'rx_rd_ip1', $q, 'where only=?s limit 1', $_GET[2] ) )
			{
				$q = rd_ip1_only_ip0( $_GET[2] );
				wa::$buffers = [ '?/rx_erp(1)rd_ip1_list(2)' . $q['only'] . substr( 0 . dechex( $q['ip_suffix_end'] ), -2 ), 'goto' ];
			};
		};
		break;
	case 'rd_forcer_insert':
		if ( isset( $_GET[3] ) )
		{
			$q = rd_ip1_useable( $_GET[3], '' );
			wa::$buffers = json_encode( array_combine( $q, $q ) );
			exit;
		};
		$rd_form_forcer_editor['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
		$rd_form_forcer_editor['hold']['value'] = array_combine( $rd_hold_list, $rd_hold_list );
		$rd_form_forcer_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
		$rd_form_forcer_editor['room']['value'] += array_combine( $rd_room_list, $rd_room_list );
		if ( $q = wa::get_form_post( $rd_form_forcer_editor ) )
		{
			//is_array( $q['inip'] ) && $q['inip'] = join( "\n", $q['inip'] );
			$q['sur_0u'] = $q['max_0u'];
			$q['sur_1u'] = $q['max_1u'];
			$q['last_update_userid'] = wa::$user['username'];
			$q['last_update_time'] = $q['time'] = time();
			$q['only'] = wa::short_hash( join( $q ) );
			wa::$buffers = wa::$sql->q_insert( 'rx_rd_forcer', $q )
				? [ '?/rx_erp(1)rd_forcer_admin', 'goto' ]
				: [ '数据写入失败!请重试.', 'warn' ];
		};
		break;
	case 'rd_forcer_update':
		$rd_form_forcer_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
		$rd_form_forcer_editor['room']['value'] += array_combine( $rd_room_list, $rd_room_list );
		wa::$buffers = isset( $_GET[2] )
			&& ( $q = wa::get_form_post( $rd_form_forcer_editor ) )
			&& wa::$sql->q_task_callback(function( $w ) use( &$q )
			{
				if ( !$w || $w['name'] !== $q['name'] )
				{
					wa::$buffers = [ '目前不允许修改机柜编号', 'warn' ];
					return 0;
				};
				$q['sur_0u'] = $w['sur_0u'] + ( $q['max_0u'] - $w['max_0u'] );
				$q['sur_1u'] = $w['sur_1u'] + ( $q['max_1u'] - $w['max_1u'] );
				//is_array( $q['inip'] ) && $q['inip'] = join( "\n", $q['inip'] );
				$q['last_update_userid'] = wa::$user['username'];
				$q['last_update_time'] = time();
				return wa::$sql->q_update( 'rx_rd_forcer', $q, 'where only=?s limit 1', $_GET[2] );
			}, wa::$sql->get_only( 'rx_rd_forcer', 'only', $_GET[2] ) )
			? [ '?/rx_erp(1)rd_forcer_admin', 'goto' ]
			: [ '数据更新失败!请重试.', 'warn' ];
		break;
	case 'rd_forcer_delete':
		wa::$buffers = isset( $_GET[2] )
		&& ( $q = wa::$sql->get_only( 'rx_rd_forcer', 'only', $_GET[2] ) )
		&& wa::$sql->q_task_callback(function() use( &$q )
		{
			return wa::$sql->get_rows( 'rx_rd_device', 'where note=?s', $q['name'] ) === 0
				&& wa::$sql->q_delete( 'rx_rd_forcer', 'where only=?s limit 1', $q['only'] );
		})
		? [ '?/rx_erp(1)rd_forcer_admin', 'goto' ]
		: [ '数据删除失败!请重试.', 'warn' ];
		break;
	case 'rd_device_insert':
		if ( isset( $_GET[3] ) )
		{
			wa::$buffers = [];
			foreach ( wa::$sql->q_query( 'select name,room from rx_rd_forcer where name like ?s limit 10', $_GET[3] . '%' ) as $q )
			{
				wa::$buffers[] = $q['name'] . ',' . $q['room'];
			};
			wa::$buffers = join( ' ', wa::$buffers );
			exit;
		};
		if ( isset( $_GET[4] ) )
		{
			$q = rd_ip1_useable( $_GET[4], isset( $_COOKIE['rd_sb_type'] ) ? $_COOKIE['rd_sb_type'] : '' );
			wa::$buffers = json_encode( array_combine( $q, $q ) );
			exit;
		};
		$rd_form_device_editor['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
		$rd_form_device_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
		$rd_form_device_editor['sb_type']['test'][] = '/^(' . join( '|', $rd_form_device_list_type ) . ')$/';
		unset( $rd_form_device_editor['ip'] );
		isset( $_POST['sb_info'] ) && is_array( $_POST['sb_info'] ) && $_POST['sb_info'] = json_encode( $_POST['sb_info'], JSON_UNESCAPED_UNICODE );
		wa::get_form_post_callback( $rd_form_device_editor, function( $w, $q )
		{
			$w['status'] = '空闲';
			$w['pw_time'] = $w['time'] = time();
			$w['pw_editid'] = wa::$user['username'];
			if ( $w['room'] === '佛山德胜机房' || $w['room'] === '中山机房' || $w['room'] === '香港机房' )//2016-07-11 +香港机房 中山机房
			{
				$e = array_merge( is_array( $w['ip_main'] ) ? $w['ip_main'] : [], is_array( $w['ip_vice'] ) ? $w['ip_vice'] : [] );
				if ( isset( $e[0] ) )
				{
					$t = $q->q_update( 'rx_rd_ip1', [
						'device' => $w['only'],
						'status' => '使用',
						'note' => $w['note'],
						'last_update_time' => $w['time'],
						'last_update_userid' => wa::$user['username']
					], 'where ip in(?b)', join( ',', array_map( [ $q, 'escape' ], $e ) ) );
					if ( $t != count( $e ) )
					{
						return FALSE;
					};
					$w['ip_main'] = is_array( $w['ip_main'] ) ? join( ' ', $w['ip_main'] ) : '';
					$w['ip_vice'] = is_array( $w['ip_vice'] ) ? join( ' ', $w['ip_vice'] ) : '';
				};
			}
			else
			{
				$w['ip_main'] = is_array( $w['ip_main'] ) ? join( ' ', $w['ip_main'] ) : preg_replace( '/\s+/', ' ', $w['ip_main'] );
				$w['ip_vice'] = is_array( $w['ip_vice'] ) ? join( ' ', $w['ip_vice'] ) : preg_replace( '/\s+/', ' ', $w['ip_vice'] );
			};
			if ( $e = $q->get_only( 'rx_rd_forcer', 'name', $w['note'] ) )
			{
				$w['useu'] |= 0;
				$r = $w['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
				$r = $w['useu'] ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a-?i where name=?s limit 1', $r, $r, $w['useu'], $w['note'] ): 1;
			}
			else
			{
				$r = 1;
			};
			return rd_oplog( 'insert', $w ) && $r && wa::$sql->q_insert( 'rx_rd_device', $w );
		}, isset( $_POST['ip_main'] ) || isset( $_POST['ip_vice'] ) ) && wa::$buffers = [ '?/rx_erp(1)rd_device_admin(7)only.eq.' . $_POST['only'], 'goto' ];
		break;
	case 'rd_device_update':
		if ( isset( $_GET[3] ) )
		{
			wa::$buffers = [];
			foreach ( wa::$sql->q_query( 'select name,room from rx_rd_forcer where name like ?s limit 10', $_GET[3] . '%' ) as $q )
			{
				wa::$buffers[] = $q['name'] . ',' . $q['room'];
			};
			wa::$buffers = join( ' ', wa::$buffers );
			exit;
		};
		if ( isset( $_GET[4] ) )
		{
			$q = rd_ip1_useable( $_GET[4], isset( $_COOKIE['rd_sb_type'] ) ? $_COOKIE['rd_sb_type'] : '' );
			wa::$buffers = json_encode( array_combine( $q, $q ) );
			exit;
		};
		if ( isset( $_GET[2], $_GET[6] ) )
		{
			wa::$sql->q_task_callback(function( $q, $w, $e )
			{
				if ( !$q || !$w )
				{
					return FALSE;
				};
				$q['sb_info'] = json_decode( $q['sb_info'], TRUE ) ?? [];
				if ( isset( $q['sb_info'][ $w['only'] ] ) )
				{
					unset( $q['sb_info'][ $w['only'] ] );
					$q['sb_info'] = json_encode( $q['sb_info'], JSON_UNESCAPED_UNICODE );
					$q = $e->q_update( 'rx_rd_device', [ 'sb_info' => $q['sb_info'] ], 'where only=?s limit 1', $q['only'] );
				}
				else
				{
					$q = 1;
				};
				if ( $w['useu'] |= 0 )
				{
					$r = $w['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
					$r = $e->q_sent( 'update rx_rd_forcer set ?a=?a+?i where name=?s limit 1', $r, $r, $w['useu'], $w['note'] );
				}
				else
				{
					$r = 1;
				};
				return $q && $r && $e->q_delete( 'rx_rd_device', 'where only=?s limit 1', $w['only'] );
			},
			wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ),
			wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[6] ) ) && wa::$buffers = [ 0, 'reload' ];
			break;
		};
		if ( isset( $_GET[2], $_GET[9] ) )
		{
			$rd_form_device_add_device['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
			$rd_form_device_add_device['sb_type']['test'][] = '/^(' . join( '|', $rd_form_device_list_type ) . ')$/';
			if ( ( $q = wa::get_form_post( $rd_form_device_add_device ) ) && ( $w = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) )
			{
				//$e = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[9] );
				wa::$buffers = rd_device_add_device( $q, $w, $_GET[9] )
					? [ 'reload', 'close' ]
					: [ '机柜设备操作失败', 'warn' ];
			};
			break;
		};
		$rd_form_device_editor['hold']['test'][] = '/^(' . join( '|', $rd_hold_list ) . ')$/';
		$rd_form_device_editor['room']['test'][] = '/^(' . join( '|', $rd_room_list ) . ')$/';
		$rd_form_device_editor['sb_type']['test'][] = '/^(' . join( '|', $rd_form_device_list_type ) . ')$/';
		unset( $rd_form_device_editor['ip'] );
		isset( $_POST['sb_info'] ) && is_array( $_POST['sb_info'] ) && $_POST['sb_info'] = json_encode( $_POST['sb_info'], JSON_UNESCAPED_UNICODE );
		isset( $_GET[2] ) && wa::get_form_post_callback( $rd_form_device_editor, function( $w, $q )
		{
			$e = wa::$sql->q_query( 'select only,room,note,useu,sb_type,ip_main,ip_vice from rx_rd_device where only=?s limit 1', $_GET[2] )->fetch_assoc();
			if ( !$e  )//|| $e['room'] !== $w['room']    2016-7-11 机房可以切换
			{
				return FALSE;
			};
			$e = (object)[
				'only' => $e['only'],
				'note' => $e['note'],
				'useu' => $e['useu'],
				'sb_type' => $e['sb_type'],
				'ip_main' => $e['ip_main'],
				'ip_vice' => $e['ip_vice']
			];
			//if ( is_string( $w['ip_main'] ) || is_string( $w['ip_vice'] ) )
			//if ( $w['room'] === '佛山德胜机房' )
			if ( $w['room'] === '佛山德胜机房' || $w['room'] === '中山机房' || $w['room'] === '香港机房' )//2016-07-11 +香港机房 中山机房
			{
				$r = trim( $e->ip_main . ' ' . $e->ip_vice );
				$e->delete_ip = $r ? explode( ' ', $r ) : [];
				$r = array_merge( is_array( $w['ip_main'] ) ? $w['ip_main'] : [], is_array( $w['ip_vice'] ) ? $w['ip_vice'] : [] );
				if ( isset( $r[0] ) )
				{
					$e->insert_ip = array_diff( $r, $e->delete_ip );
					$e->delete_ip = array_diff( $e->delete_ip, $r );
					$w['ip_main'] = is_array( $w['ip_main'] ) ? join( ' ', $w['ip_main'] ) : '';
					$w['ip_vice'] = is_array( $w['ip_vice'] ) ? join( ' ', $w['ip_vice'] ) : '';
				}
				else
				{
					$e->insert_ip = [];
				};
				$t = time();
				$e->delete_ip && $q->q_update( 'rx_rd_ip1', [
					'device' => NULL,
					'status' => '空闲',
					'note' => '',
					'last_update_time' => $t,
					'last_update_userid' => wa::$user['username']
				], 'where ip in(?b)', join( ',', array_map( [ wa::$sql, 'escape' ], $e->delete_ip ) ) );
				$t = [
					'device' => $w['only'],
					'status' => '使用',
					'note' => $w['note'],
					'last_update_time' => $t,
					'last_update_userid' => wa::$user['username'] ];
				if ( $w['only'] == $e->only )
				{
					$e->action = $e->insert_ip
						? count( $e->insert_ip ) == $q->q_update( 'rx_rd_ip1', $t, 'where status="空闲" and ip in(?b)',
							join( ',', array_map( [ wa::$sql, 'escape' ], $e->insert_ip ) ) )
						: TRUE;
				}
				else
				{
					isset( $r[0] ) && $q->q_update( 'rx_rd_ip1', $t, 'where status="空闲" and ip in(?b)', join( ',', array_map( [ wa::$sql, 'escape' ], $r ) ) );
					$e->action = TRUE;

				};
			}
			else
			{
				$e->action = TRUE;
				$w['ip_main'] = is_array( $w['ip_main'] ) ? join( ' ', $w['ip_main'] ) : preg_replace( '/\s+/', ' ', $w['ip_main'] );
				$w['ip_vice'] = is_array( $w['ip_vice'] ) ? join( ' ', $w['ip_vice'] ) : preg_replace( '/\s+/', ' ', $w['ip_vice'] );
			}


			if ( $e->sb_type !== $w['sb_type'] )
			{
				if ( $e->sb_type === '机柜' )
				{
					return FALSE;
				};
				if ( $w['sb_type'] === '机柜' )
				{
					$w['sb_info'] = '{}';
				};
			};
			if ( $w['note'] === $e->note )
			{
				if ( $q = wa::$sql->get_only( 'rx_rd_forcer', 'name', $e->note ) )
				{
					$r = $e->useu - $w['useu'];
					$t = $w['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
					if ( $e->sb_type === $w['sb_type'] )
					{
						$t = $r ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a+?i where name=?s limit 1', $t, $t, $r, $w['note'] ) : 1;
					}
					else
					{
						$q[ $e->sb_type === '刀片服务器' ? 'sur_0u' : 'sur_1u' ] += $e->useu;
						$q[ $t ] -= $w['useu'];
						$t = wa::$sql->q_update( 'rx_rd_forcer', [
							'sur_0u' => $q['sur_0u'],
							'sur_1u' => $q['sur_1u'],
							'last_update_time' => time(),
							'last_update_userid' => wa::$user['username']
						], 'where name=?s limit 1', $w['note'] );
					};
				}
				else
				{
					$t = 1;
				};
				if ( !$t )
				{
					return FALSE;
				};
			}
			else
			{
				if ( $q = wa::$sql->get_only( 'rx_rd_forcer', 'name', $e->note ) )
				{
					$e->useu |= 0;
					$t = $e->sb_type === '刀片服务器' ? 'sur_0u' : 'sur_1u';
					$t = $e->useu ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a+?i where name=?s limit 1', $t, $t, $e->useu, $e->note ) : 1;
				}
				else
				{
					$t = 1;
				};
				if ( !$t )
				{
					return FALSE;
				};
				if ( $q = wa::$sql->get_only( 'rx_rd_forcer', 'name', $w['note'] ) )
				{
					$r = $w['useu'] | 0;
					$t = $w['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
					$t = $r ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a-?i where name=?s limit 1', $t, $t, $r, $w['note'] ) : 1;
				};
			};
			return rd_oplog( 'update', wa::$sql->get_only( 'rx_rd_device', 'only', $e->only ), $w ) && $e->action && wa::$sql->q_update( 'rx_rd_device', $w, 'where only=?s limit 1', $e->only );
		}, isset( $_POST['ip_main'] ) || isset( $_POST['ip_vice'] ) ) && wa::$buffers = [ '?/rx_erp(1)rd_device_status(2)' . $_POST['only'], 'goto' ];
		break;
	case 'rd_device_status':
		if ( isset( $_GET[3] ) )
		{
			wa::$buffers = [];
			foreach ( wa::$sql->q_query( 'select name,username from rx_hr_staff where name like ?s limit 10', urldecode( $_GET[3] ) . '%' ) as $q )
			{
				wa::$buffers[] = $q['name'] . ',' . $q['username'];
			};
			wa::$buffers = join( ' ', wa::$buffers );
			exit;
		};


		isset( $_GET[2] ) && wa::$sql->q_query( 'select * from rx_rd_device where only=?s limit 1', $_GET[2] )->fetch_callback(function( $q ) use(
			&$rd_form_device_only,
			&$rd_form_device_status,
			&$rd_form_device_list_status )
		{
			$rd_form_device_status['status']['test'][] = '/^(' . join( '|', $q['hold'] == '锐讯'
				? array_slice( $rd_form_device_list_status, 1, 4 )
				: [ $rd_form_device_list_status[1], $rd_form_device_list_status[4], end( $rd_form_device_list_status ) ] ) . ')$/';
			if ( ( $w = wa::get_form_post( $rd_form_device_status ) ) && $w['pw_starts'] < time() )
			{
				$w['pw_time'] = time();
				$w['pw_editid'] = wa::$user['username'];
				$w['pw_expire'] == '' && $w['pw_expire'] = NULL;
				$rd_form_device_only = $q['only'];
				return rd_oplog( 'update', wa::$sql->get_only( 'rx_rd_device', 'only', $q['only'] ), $w ) && wa::$sql->q_update( 'rx_rd_device', $w, 'where only=?s limit 1', $q['only'] );
			};
		}) && wa::$buffers = [ '?/rx_erp(1)rd_device_admin(7)only.eq.' . $rd_form_device_only, 'goto' ];
		break;
	case 'rd_device_emptys':
		$q = [ 'pw_userid', 'pw_team', 'pw_starts', 'pw_expire', 'pw_of', 'pw_be', 'pw_name', 'pw_tel', 'pw_imid', 'pw_note' ];
		$q = array_combine( $q, array_fill( 0, count( $q ), NULL ) );
		$q['status'] = '空闲';
		$q['pw_time'] = time();
		$q['pw_editid'] = wa::$user['username'];
		isset( $_GET[2] )
		&& rd_oplog( 'update', $w = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ), $q + $w )
		&& wa::$sql->q_update( 'rx_rd_device', $q, 'where only=?s limit 1', $_GET[2] )
		&& wa::$buffers = [ '?/rx_erp(1)rd_device_admin(7)only.eq.' . $_GET[2], 'goto' ];
		break;
	case 'rd_device_delete':
		isset( $_GET[2] )
		&& ( $q = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) )
		&& wa::$sql->q_task_callback(function() use( &$q )
		{
			if ( $q['status'] === '回收' )
			{
				wa::$buffers = [ '?/rx_erp(1)rd_device_status(2)' . $q['only'], 'goto' ];
				return 0;
			};
			if ( !$q || $q['status'] === '机柜设备' )
			{
				return 0;
			};
			if ( $q['sb_type'] === '机柜' && wa::$sql->get_rows( 'rx_rd_device', 'where room=?s', $q['only'] ) )
			{
				return 0;
			};
			if ( $w = trim( $q['ip_main'] . ' ' . $q['ip_vice'] ) )
			{
				wa::$sql->q_update( 'rx_rd_ip1', [
					'device' => NULL,
					'status' => '空闲',
					'note' => '',
					'last_update_time' => time(),
					'last_update_userid' => wa::$user['username'] ], 'where ip in(?b)',
					join( ',', array_map( [ wa::$sql, 'escape' ], explode( ' ', $w ) ) )
				);
			};
			if ( $w = wa::$sql->get_only( 'rx_rd_forcer', 'name', $q['note'] ) )
			{
				$q['useu'] |= 0;
				$w = $q['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
				$w = $q['useu'] ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a+?i where name=?s limit 1', $w, $w, $q['useu'], $q['note'] ) : 1;
			}
			else
			{
				$w = 1;
			};
			if ( $w && wa::$sql->q_delete( 'rx_rd_device', 'where only=?s limit 1', $q['only'] ) )
			{
				$w = rd_oplog( 'delete', $q );
				$q['pw_time'] = time();
				$q['pw_editid'] = wa::$user['username'];
				$q['md5'] = md5( join( $q ) );
				return $w && wa::$sql->q_insert( 'rx_rd_delete', $q );
			};
			return FALSE;
		}) && wa::$buffers = [ '?/rx_erp(1)rd_device_admin', 'goto_referer' ];
		break;
	case 'rd_services_device':
		isset( $_GET[2] )
		&& ( $q = wa::get_form_post( $rd_services_editor ) )
		&& ( $w = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) )
		&& wa::$sql->q_task_callback(function() use( &$q, &$w )
		{
			if ( $w['sb_type'] === '机柜' )
			{
				$e = [];
				foreach ( json_decode( $w['sb_info'], TRUE ) as $r => $t )
				{
					if ( $t[0] )
					{
						foreach ( explode( ' ', $t[0] ) as $y )
						{
							$e[] = $r . ':' . $y;
						};
					};
					if ( $t[1] )
					{
						foreach ( explode( ' ', $t[1] ) as $y )
						{
							$e[] = $r . ':' . $y;
						};
					};
				};
				$e = join( "\n", $e );
			}
			else if ( $w['sb_type'] === '服务器' || $w['sb_type'] === '云设备' || $w['sb_type'] === '独立刀片机' || $w['sb_type'] === '刀片服务器' )
			{
				$e = [];
				foreach ( json_decode( $w['sb_info'], TRUE ) as $r => $t )
				{
					$e[] = $r . ': ' . $t;
				};
				$e = join( "\n", $e );
			}
			else
			{
				$e = $w['sb_info'];
			};
			$e = [
				'产权: ' . $w['hold'],
				'状态: ' . $w['status'],
				'机房: ' . $w['room'],
				'备注: ' . $w['note'],
				'业务员: ' . $w['pw_userid'],
				'联系QQ: ' . $w['pw_imid'],
				'设备信息: ' . "\n" . $e
			];
			$q['sbxx'] = join( "\n", $e );
			$q['userid'] = wa::$user['username'];
			$q['pkno'] = $w['only'];
			$q['time'] = time();
			$e = [
				'type' => 'k',
				'mark' => $q['describe'],
				'pkip' => $w['ip_main'] ? explode( ' ', $w['ip_main'] )[0] : '0.0.0.0',
				'pkno' => $w['only'],
				'sbxx' => join( "\n", $e ),
				'note' => wa::$user['name'] . "\r\n\r\n" . $q['note']
			];
			$r = $q['describe'] === '回收设备'
				? wa::$sql->q_update( 'rx_rd_device', [
					'status' => '回收',
					'pw_time' => time(),
					'pw_editid' => wa::$user['username']
					], 'where only=?s and status!="回收" limit 1', $w['only'] )
				: 1;
			$q['pkip'] = $e['pkip'];
			$q['only'] = rx_pack_send( $q['d_group'], $q['describe'], $w['only'], 'ermsd_pack_task_create', $e, 'ermsd_pack_task_rd_task_done' );
			//2017.01.05 提交回收设备任务时插入记录 （rd_oplog( 'update', $w = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ), $q + $w )）
			$mls=$q;
			$mls['status']='回收';
			return rd_oplog( 'insert', $mls ) && $r && $q['only'] && wa::$sql->q_insert( 'rx_rd_task', $q );
		})
		&& wa::$buffers = [
			0 => '/rx_pack_' . md5( '德胜机房运维中心' ),
			1 => 'rx_ws_callback',
			'send' => $q['describe'] === '回收设备' ? 'reload ' : 'notify_pack',
			'come' => 'close' ];
		break;
	case 'rd_device_admin':
		if ( isset( $_GET[2] ) )
		{

			$q = wa::get_filter( [ 'pw_expire', 'status' ], [ 'status!="机柜设备"' ] );
			$q = wa::$sql->q_query( 'select pw_userid,ip_main,bandwidth,defense,pw_of,pw_starts,pw_expire,pw_note from rx_rd_device ?b order by ?b', $q,
				$_GET[2] == 1 ? 'pw_expire asc' : 'pw_starts desc' );
			$w = [];
			foreach ( $q as $q )
			{
				$w[ $q['pw_userid'] ][] = $q;
			};
			$q = $_GET[2] == 1 ? '所有已到期的设备' : '测试设备时间最长排列';
			$q .= "\n\n";
			foreach ( $w as $w )
			{
				foreach ( $w as $w )
				{
					//unset( $e[pw_userid'] );
					$w['ip_main'] = explode( ' ', $w['ip_main'] )[0];
					$w['pw_starts'] = date( 'Y-m-d(H)\h', $w['pw_starts'] );
					$w['pw_expire'] = date( 'Y-m-d(H)\h', $w['pw_expire'] );
					$w['pw_note'] = ' 备注：' . preg_replace( '/\s+/', ' ', $w['pw_note'] );
					$q .=  join( '	', $w ) . "\n";
				};
				$q .= "\n";
			};
			wa::$buffers = $q;
			exit;
		};
		break;
	case 'rd_serverrd':
		if ( $q = wa::get_form_post( $rd_serverrd_editor ) )
		{
			wa::$sql->q_task_callback(function() use( &$q )
			{
				$q['time'] = time();
				$q['userid'] = wa::$user['username'];
				$q['describe'] = wa::$user['name'];
				$q['only'] = rx_pack_send( $q['d_group'], $q['describe'], $q['pkno'], 'ermsd_pack_task_create', [
					'type' => 'k',
					'mark' => $q['describe'],
					'pkip' => $q['pkip'] ? $q['pkip'] : '0.0.0.0',
					'pkno' => $q['pkno'],
					'sbxx' => $q['sbxx'],
					'note' => wa::$user['name'] . "\r\n\r\n" . $q['note']
				], 'ermsd_pack_task_rd_task_done' );
				return $q['only'] && wa::$sql->q_insert( 'rx_rd_task', $q ) && rx_pack_call(  $q['only'], 'unpack' );
			}) && wa::$buffers = [
				0 => '/rx_pack_' . md5( '德胜机房运维中心' ),
				1 => 'rx_ws_callback',
				'send' => 'notify_pack',
				'come' => 'close' ];
		};
		break;
	case 'rd_task_delete':
		isset( $_GET[2], $_GET[3] ) && wa::$sql->q_task_callback(function( $q )
		{
			//2017.01.05 删除操作
			
			///////////////////////////////////////////////////////
			$e=wa::$sql->get_only( 'rx_rd_task', 'only', $q );
			$w=wa::$sql->get_only( 'rx_rd_device', 'only', $e['pkno'] );
			//处理
			$w['time']=date('Y-m-d H:i:s',$w['time']);
			$e['status']='测试';
			unset($e['start']);
			unset($e['name']);
			unset($e['over']);
			unset($e['done']);
			unset($e['note']);//备注信息
			
			//END
			$rows = [
					'only'=>md5($q),
					'time' => time(),
					'userid' => wa::$user['username'],
					'action' => 'rd_services_device',
					'query' =>'delete',
					'mark_time' => 0,
					'json_data0' => json_encode($e, JSON_UNESCAPED_UNICODE ),
					'json_data1' => '{}'
				];
			wa::$sql->q_insert( 'rx_rd_oplog', $rows );
			//return true ;
			$w = wa::$sql->q_delete( 'rx_public_pack', 'where only=?s and start is null limit 1', $q );
			
			if ( $w && wa::$sql->q_delete( 'rx_rd_task', 'where only=?s limit 1', $q ) )
			{
				$_GET[3] && wa::$sql->q_update( 'rx_rd_device', [ 'status' => '测试', 'pw_time' => time(), 'pw_editid' => wa::$user['username'] ], 'where only=?s limit 1', $_GET[3] );
				// wa::$buffers = [
				// 	0 => '/rd_services',
				// 	1 => 'rx_ws_callback',
				// 	'send' => 'reload ',
				// 	'come' => '?/rx_erp(1)rd_services'
				// ];
				wa::$buffers = [ $_GET[3] ? '?/rx_erp(1)rd_device_admin(7)only.eq.' . $_GET[3] : '?/rx_erp(1)rd_serverrd', 'goto' ];
				return TRUE;
			};
			return FALSE;
		}, $_GET[2] );
		break;
	case 'rd_task_done':
		isset( $_GET[2] )
		&& ( $q = wa::$sql->get_only( 'rx_rd_task', 'only', $_GET[2] ) )
		&& $q['over']
		&& $q['done'] === NULL
		&& wa::$sql->q_task_callback(function() use( &$q )
		{
			if ( $q['describe'] === '回收设备' )
			{
				$w = [ 'pw_userid', 'pw_team', 'pw_starts', 'pw_expire', 'pw_of', 'pw_be', 'pw_name', 'pw_tel', 'pw_imid', 'pw_note' ];
				$w = array_combine( $w, array_fill( 0, count( $w ), NULL ) );
				$w['status'] = '空闲';
				$w['pw_time'] = time();
				$w['pw_editid'] = wa::$user['username'];
				$e = wa::$sql->get_only( 'rx_rd_device', 'only', $q['pkno'] );
				
				if(!$e){ $e = []; }
				
				$temp_w = $w + $e;
				$log_rs = rd_oplog( 'update', $e , $temp_w );

				if($e){
					$update_rs = wa::$sql->q_update( 'rx_rd_device', $w, 'where only=?s limit 1', $q['pkno'] );
				}else{
					$update_rs = 1;
				}
				
				
				
				$w = $log_rs && $update_rs
					? '?/rx_erp(1)rd_device_update(2)' . $q['pkno']
					: 0;
			}
			else
			{
				$w = $q['pkno'] ? '?/rx_erp(1)rd_device_update(2)' . $q['pkno'] : '?/rx_erp(1)rd_serverrd';
			};
			if ( $w && wa::$sql->q_update( 'rx_rd_task', [ 'done' => time() ], 'where only=?s limit 1', $q['only'] ) )
			{
				wa::$buffers = [
					0 => '/rd_services',
					1 => 'rx_ws_callback',
					'send' => 'reload ',
					'come' => $w
				];
				
				//2017.01.04 验收成功后删除
				$md5 = $q['only'];
				$data =wa::$sql->q_query( "SELECT * FROM `rx_ermsd_task` WHERE md5 like '%$md5%' limit 1")->fetch_assoc();
				wa::$sql->q_delete( 'ysts', 'where md5=?s limit 1', $data['md5'] );
				//end
				
				return TRUE;
			};
			return FALSE;
		});
		break;
	case 'rd_staff_update':
		wa::$buffers = rx_staff_update( '资源部' );
		break;
	case 'rx_notice_insert':
		rx_notice_insert( '资源部' );
		break;
 	default:
 		wa::$buffers = [ '未定义功能!', 'warn' ];
};
wa::end_str_status();
exit;
?>