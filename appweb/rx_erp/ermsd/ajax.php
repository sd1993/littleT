<?php
wa::$buffers = [ '无效数据!', 'warn' ];
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'rx_pack':
		if ( isset( $_GET[2], $_GET[3] ) )
		{
			if ( $_GET[2] === 'done' )
			{
				wa::$buffers = [ '?/rx_erp(1)ermsd_task_work(7)md5.eq.zeronetazeronetazeroneta' . $_GET[3], 'goto' ];
			}
			else
			{
				rx_pack_call( $_GET[3], $_GET[2] );
			};
		};
		break;
	case 'ermsd_task_insert':
		if ( ( $q = wa::get_form_post( $ermsd_form_task_editor ) ) && ermsd_no_serviced( $q['pkip'] ) )
		{
			$q['time'] = time();
			$q['mark'] = '';
			$q['c_userid'] = wa::$user['username'];
			$q['md5'] = md5( join( $q ) );
			wa::$sql->q_insert( 'rx_ermsd_task', $q );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];
			wa::$buffers = [
				0 => '/ermsd_task_work',
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => 'close'
				//'come' => '?/rx_erp(1)ermsd_task_work'
			];
		};
		break;
	case 'ermsd_task_update':
		if ( isset( $_GET[2] ) && ( $q = wa::get_form_post( $ermsd_form_task_editor ) ) )
		{
			//$q['time'] = time();
			//$q['c_userid'] = wa::$user['username'];
			wa::$sql->q_update( 'rx_ermsd_task', $q, 'where md5=?s and over is null limit 1', $_GET[2] );
			$q = ermsd_pack_callback( wa::$sql->get_only( 'rx_ermsd_task', 'md5', $_GET[2] ), 'work' );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];
			wa::$buffers = [
				0 => '/ermsd_task_work' . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => 'close'
				//'come' => '?/rx_erp(1)ermsd_task_work'
			];
		};
		break;
	case 'ermsd_task_delete':
		isset( $_POST['value'], $_GET[2] )
		&& substr( $_GET[2], 0, 24 ) !== 'zeronetazeronetazeroneta'
		&& md5( $_POST['value'] ) === wa::$user['password']
		&& wa::$sql->q_delete( 'rx_ermsd_task', 'where md5=?s and over is null limit 1', $_GET[2] )
		//&& wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];
		&& wa::$buffers = [
			0 => '/ermsd_task_work',
			1 => 'rx_ws_callback',
			'send' => 'reload ',
			'come' => '?/rx_erp(1)ermsd_task_work'
		];
		break;
	case 'ermsd_task_work':
		if ( isset( $_GET[2], $_GET[5] ) )
		{
			wa::$sql->q_update( 'rx_ermsd_task', [ 'type' => $_GET[5] ], 'where md5=?s and over is null limit 1', $_GET[2] );
			$q = ermsd_pack_callback( wa::$sql->get_only( 'rx_ermsd_task', 'md5', $_GET[2] ), 'work' );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];
			wa::$buffers = [
				0 => '/ermsd_task_work' . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_work'
			];

		};
		if ( isset( $_GET[2], $_GET[3] ) )
		{
			$q = $_GET[3] ? [
				'start' => time(),
				'd_userid' => $_GET[3],
				'team' => isset( $_GET[4] ) ? urldecode( $_GET[4] ) : NULL
			] : [
				'start' => NULL,
				'd_userid' => NULL,
				'team' => NULL
			];
			wa::$sql->q_update( 'rx_ermsd_task', $q, 'where md5=?s and over is null limit 1', $_GET[2] );
			$q = ermsd_pack_callback( wa::$sql->get_only( 'rx_ermsd_task', 'md5', $_GET[2] ), 'work' );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];
			wa::$buffers = [
				0 => '/ermsd_task_work' . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_work'
			];
		};
		break;
	case 'ermsd_task_over':
		if ( isset( $_GET[2] ) && ( $q = wa::$sql->q_query( 'select * from rx_ermsd_task where md5=?s and start is not null and over is null limit 1', $_GET[2] )->fetch_assoc() ) )
		{
			
			//2017.10.29 验收记录(在维护其他能看到的验收数据才能去插入)
			
			
			//2016.10.29 验收记录

			wa::$sql->q_update( 'rx_ermsd_task', [
				'over' => $q['start'] + $ermsd_task_type[ $q['type'] ] - time()
			], 'where md5=?s and start is not null and over is null limit 1', $q['md5'] );
			$q = ermsd_pack_callback( $q, 'over' );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_work', 'goto' ];


			wa::$buffers = [
				0 => '/ermsd_task_work' . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_work'
			];
			
			
		};
		break;
	case 'ermsd_task_done':
		if ( isset( $_GET[2], $_POST['value'] ) && preg_match( '/^\d{1,2}(\.\d)?$/', $_POST['value'] ) && ( $q = wa::$sql->q_query( 'select * from rx_ermsd_task where md5=?s and start is not null and over is not null and done is null limit 1', $_GET[2] )->fetch_assoc() ) )
		{
			$q['type'] == 'k' && $q['over'] = 0;
			$q['done'] = time() - $q['start'];
			$q['score'] = $_POST['value'];
			wa::$sql->q_task_callback(function() use( &$q )
			{
				$w = wa::$sql->q_delete( 'rx_ermsd_task', 'where md5=?s limit 1', $q['md5'] );
				$e = ermsd_pack_callback( $q, 'done' );
				//$e = substr( $q['md5'], 0, 24 ) == 'zeronetazeronetazeroneta' ? rx_pack_call( substr( $q['md5'], -8 ), 'done' ) : 1;
				$q['md5'] = md5( join( $q ) );
				return $w && $e && wa::$sql->q_insert( 'rx_ermsd_task_record', $q );
			}) && wa::$buffers = [
				0 => '/ermsd_task_done',
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_done'
			];
		};
		break;
	case 'ermsd_task_mark':
		if ( isset( $_GET[2], $_POST['value'] ) && strlen( $_POST['value'] ) <= 128 )
		{
			wa::$sql->q_update( 'rx_ermsd_task', [ 'mark' => $_POST['value'] ], 'where md5=?s and done is null limit 1', $_GET[2] );
			$q = ermsd_pack_callback( [ 'md5' => $_GET[2], 'mark' => $_POST['value'] ], 'mark' );
			//wa::$buffers = [ '?/rx_erp(1)ermsd_task_' . ( isset( $_GET[3] ) ? $_GET[3] : 'work' ), 'goto' ];
			wa::$buffers = [
				0 => '/ermsd_task_' . ( isset( $_GET[3] ) ? $_GET[3] : 'work' ) . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_' . ( isset( $_GET[3] ) ? $_GET[3] : 'work' )
			];
		};
		break;
	case 'ermsd_task_work_my':
		if ( isset( $_GET[2] ) && wa::$user['group'] == '德胜机房运维中心' )
		{
			wa::$sql->q_update( 'rx_ermsd_task', [
				'start' => time(),
				'd_userid' => wa::$user['username'],
				'team' => wa::$user['rx_team']
			], 'where md5=?s and d_userid is null limit 1', $_GET[2] );
			$q = ermsd_pack_callback( wa::$sql->get_only( 'rx_ermsd_task', 'md5', $_GET[2] ), 'work' );
			wa::$buffers = [
				0 => '/ermsd_task_work' . ( is_string( $q ) ? ' ' . $q : '' ),
				1 => 'rx_ws_callback',
				'send' => 'reload ',
				'come' => '?/rx_erp(1)ermsd_task_my'
			];
		};
		break;
	case 'ermsd_task_over_my':
		// if ( isset( $_GET[2] ) && ( $q = wa::$sql->q_query( 'select * from rx_ermsd_task where md5=?s and d_userid=?s and start is not null and over is null limit 1', $_GET[2], wa::$user['username'] )->fetch_assoc() ) )
		// {
		// 	wa::$sql->q_update( 'rx_ermsd_task', [
		// 		'over' => $q['start'] + $ermsd_task_type[ $q['type'] ] - time()
		// 	], 'where md5=?s and d_userid=?s and start is not null and over is null limit 1', $q['md5'], wa::$user['username'] );
		// 	wa::$buffers = [ '?/rx_erp(1)ermsd_task_work_my', 'goto' ];
		// };
		break;
	case 'ermsd_task_stat':
		if ( isset( $_GET[7] )
			&& preg_match( '/^team\.eq\.(' . join( '|', $ermsd_team_list ) . ')\/start\.ge\.(\d{10})\/start\.le\.(\d{10})$/', urldecode( $_GET[7] ), $q )
			&& ( $q[3] - $q[2] ) < 31968000 )
		{
			wa::htt( FALSE );
			$w = wa::$buffers->write->tag_table();
			$w['class'] = 'wa_grid_table';
			$w['style'] = 'width:900px;margin:8px;text-align:right';
			$w->thead->add( 'tr' )->add( 'td', $q[1] . '工作统计表' )->set_attrs([
				'style' => 'text-align:center',
				'colspan' => 16
			]);
			$e = $w->thead->add( 'tr' );
			foreach ( [ '排名', '员工姓名', 'A', 'B', 'C', 'D', 'E', 'F', 'K', '总体时差', '得分', '全勤记分', '行为评分', '异常信息', '队长评语','备注' ] as $r )
			{
				$e->add( 'td', $r );
			};
			$e = 0;
			$r = 0;
			foreach ( ermsd_task_stat( $q[1], $q[2], $q[3] ) as $t )
			{
				$e += $t['score'];
				$y = $w->tbody->add( 'tr' );
				$y->add( 'td', ++$r );
				$y->add( 'td', $t['name'] );
				$y->add( 'td', $t['a'] );
				$y->add( 'td', $t['b'] );
				$y->add( 'td', $t['c'] );
				$y->add( 'td', $t['d'] );
				$y->add( 'td', $t['e'] );
				$y->add( 'td', $t['f'] );
				$y->add( 'td', $t['k'] );
				$y->add( 'td', number_format( $t['push'] + $t['past'] ) . '/s' );
				$y->add( 'td', number_format( $t['score'] ) );
				$y->add( 'td' )->add( 'input' )->set_style( 'width:64px;border:0;text-align:right' );
				$y->add( 'td' )->add( 'input' )->set_style( 'width:64px;border:0;text-align:right' );
				$y->add( 'td' )->add( 'input' )->set_style( 'width:64px;border:0;text-align:right' );
				$y->add( 'td' )->add( 'input' )->set_style( 'width:64px;border:0;text-align:right' );
				$y->add( 'td' )->add( 'input' )->set_style( 'width:168px;border:0;text-align:right' );
			};
			$r = $w->tfoot->add( 'tr' )->add( 'td' )->set_attrs([
				'class' => 'spacing_each',
				'style' => 'text-align:center',
				'colspan' => 16
			]);
			$r->add( 'span', '统计时间为：' . date( 'Y-m-d H:i:s 至 ', $q[2] ) . date( 'Y-m-d H:i:s', $q[3] ) );
			$r->add( 'span', '团队得分：' . number_format( $e ) );
			$r->add( 'span', '队长：' )->add( 'input' )->set_style( 'width:42px;border:0' );
			$r->add( 'span', '制表人：' . wa::$user['name'] );
			$r->add( 'span', '日期：' . date( 'Y年m月d日' ) );
			//wa::$buffers->script = '$(function(){print()})';
			exit;
		};
		break;
	case 'ermsd_staff_online':
		isset( $_GET[2] )
			&& wa::$sql->q_sent( 'update rx_hr_staff set online=case online when "1" then "0" else "1" end where username=?s limit 1', $_GET[2] )
			&& wa::$buffers = [ '?/rx_erp(1)ermsd_staff_online', 'goto' ];
		break;
	case 'ermsd_relief_insert':
		if ( $q = wa::get_form_post( $ermsd_relief_insert ) )
		{
			$q['time'] = time();
			$q['user'] = wa::$user['username'];
			$q['only'] = wa::short_hash( join( $q ) );
			wa::$sql->q_insert( 'rx_ermsd_relief', $q ) && wa::$buffers = [ '?/rx_erp(1)ermsd_relief_record', 'goto' ];
		};
		break;
	case 'ermsd_relief_record':
		if ( isset( $_GET[2] ) && ( $q = wa::$sql->get_only( 'rx_ermsd_relief', 'only', $_GET[2] ) ) && $q['c_time'] === NULL )
		{
			wa::$sql->q_update( 'rx_ermsd_relief', [
				'c_time' => time(),
				'c_user' => wa::$user['username']
			], 'where only=?s limit 1', $q['only'] ) && wa::$buffers = [ '?/rx_erp(1)ermsd_relief_record', 'goto' ];
		};
		break;
	case 'ermsd_staff_update':
		wa::$buffers = rx_staff_update( '德胜机房运维中心' );
		break;
	case 'rx_notice_insert':
		rx_notice_insert( '德胜机房运维中心' );
		break;
 	default:
 		wa::$buffers = [ '未定义功能!', 'warn' ];
};
wa::end_str_status();