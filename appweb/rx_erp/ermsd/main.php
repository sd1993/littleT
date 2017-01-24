<?php
// $ermsd_group_list = rx_get_group( '德胜机房运维中心' );
// $ermsd_staff_list = rx_get_staff( '德胜机房运维中心' );
$ermsd_task_type = [
	'a' => 60 * 6,
	'b' => 60 * 15,
	'c' => 60 * 30,
	'd' => 60 * 60,
	'e' => 60 * 20,
	'f' => 60 * 45,
	'k' => 0
];
$ermsd_task_score = [
	'a' => 1,
	'b' => 2,
	'c' => 3,
	'd' => 6,
	'e' => 2,
	'f' => 4,
	'k' => 0 
];
$ermsd_team_list = [ '光纤队', '火箭队' ];
$ermsd_form_task_editor = [
	'type' => [
		'test' => [ 1, 1, '/^[abcdefk]$/' ],
		'name' => '选择任务',
		'type' => 'select',
		'value' => [
			'' => '请选择一个任务分类开始',
			'a' => 'A 类 计时6分钟',
			'b' => 'B 类 计时15分钟',
			'c' => 'C 类 计时半小时',
			'd' => 'D 类 计时一小时',
			'e' => 'E 类 计时20分钟',
			'f' => 'F 类 计时45分钟',
			'k' => 'K 类 特殊任务不限时间'
		]
	],
	'pkip' => [
		'test' => [ 7, 15, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => '设备ＩＰ',
		'type' => 'text',
		'note' => '机器IP地址'
	],
	'pkno' => [
		'test' => [ 0, 16, '/^$|^[A-Z\d\-]+$/' ],
		'name' => '设备编号',
		'type' => 'text',
		'note' => '可不填'
	],
	'sbxx' => [
		'test' => [ 0, 1024 ],
		'name' => '设备信息',
		'type' => 'textarea',
		'note' => '可不填'
	],
	'note' => [
		'test' => [ 1, 2048 ],
		'name' => '任务说明',
		'type' => 'textarea',
		'note' => '必填'
	]
];
$ermsd_relief_insert = [
	'ud0' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '公用铁U盘',
		'type' => 'text',
		'note' => '必填'
	],
	'ud1' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '公用塑料U盘',
		'type' => 'text',
		'note' => '必填'
	],
	'cdrom' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '光驱',
		'type' => 'text',
		'note' => '必填'
	],
	'screwdriver0' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '十字头螺丝刀',
		'type' => 'text',
		'note' => '必填'
	],
	'screwdriver1' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '一字头螺丝刀',
		'type' => 'text',
		'note' => '必填'
	],
	'pliers' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '剪线钳',
		'type' => 'text',
		'note' => '必填'
	],
	'cpu' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => 'CPU',
		'type' => 'text',
		'note' => '必填'
	],
	'ram2' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '内存条2代(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'ram3' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '内存条3代(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'nic' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '网卡(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'ps' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '电源(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'interphone' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '对讲机',
		'type' => 'text',
		'note' => '必填'
	],
	'phone' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '手机',
		'type' => 'text',
		'note' => '必填'
	],
	'hd' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '硬盘(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'cable' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '视频线(备用)',
		'type' => 'text',
		'note' => '必填'
	],
	'keyboard' => [
		'test' => [ 1, 2, '/^\d+$/' ],
		'name' => '键盘',
		'type' => 'text',
		'note' => '必填'
	],
	'note' => [
		'test' => [ 0, 2048 ],
		'name' => '备注',
		'type' => 'textarea'
	]
];
function ermsd_json_staff()
{
	$q = rx_get_staff( '德胜机房运维中心' );
	foreach ( $q as $w => $e )
	{
		$q[ $w ]['tasks'] = 0;
	};
	foreach ( wa::$sql->q_query( 'select d_userid from rx_ermsd_task where d_userid is not null and over is null' ) as $w )
	{
		if ( isset( $q[ $w['d_userid'] ] ) )
		{
			++$q[ $w['d_userid'] ]['tasks'];
		};
	};
	return json_encode( $q, JSON_UNESCAPED_UNICODE );
}
function ermsd_no_serviced( $q )
{
	if ( $q === '0.0.0.0' || ( isset( $_GET[9] ) && $_GET[9] === 'confirm' ) )
	{
		return TRUE;
	};
	$w = wa::$sql->q_query( 'select time,c_userid,note from rx_ermsd_task where done is null and pkip=?s', $q );
	if ( $w->num_rows )
	{
		$q = [ 'IP: ' . $q . ', 继续提交按提交请输入密码！' ];
		foreach ( $w as $w )
		{
			$q[] = date( 'Y-m-d H:i:s', $w['time'] ) . $w['note'];
			$q[] = '********** 以上内容提交员工ID:' . $w['c_userid'] . ' **********';
		};
		wa::$buffers = [ join( "\n", $q ), 'ermsd_task_submit_confirm', 'md5' => wa::$user['password'] ];
		return FALSE;
	};
	return TRUE;
}
function ermsd_task_over_time( $q )
{
	$w = $q < 0 ? '-' : '+';
	$q = abs( $q );
	return $w . ' ' . ( $q / 60 | 0 ) . ':' . substr( 0 . ( $q % 60 ), -2 );
}
function ermsd_task_stat( $q, $w, $e )
{
	$r = [];
	$t = [];
	foreach ( wa::$sql->q_query( 'select username,name from rx_hr_staff where `group`="德胜机房运维中心" and rx_team=?s and resign=0', $q ) as $y )
	{
		$r[ $y['username'] ] = 0;
		$t[ $y['username'] ] = [
			'name' => $y['name'],
			'past' => 0,
			'push' => 0,
			'a' => 0,
			'b' => 0,
			'c' => 0,
			'd' => 0,
			'e' => 0,
			'f' => 0,
			'k' => 0 ];
	};
	foreach ( wa::$sql->q_query( 'select d_userid,type,over,score from rx_ermsd_task_record where done is not null and start>=?i and start<=?i and team=?s', $w, $e, $q ) as $q )
	{
		if ( isset( $r[ $q['d_userid'] ] ) )
		{
			$r[ $q['d_userid'] ] += $q['score'];
			++$t[ $q['d_userid'] ][ $q['type'] ];
			$t[ $q['d_userid'] ][ $q['over'] < 0 ? 'past' : 'push' ] += $q['over'];
		};
	};
	arsort( $r );
	foreach ( $r as $w => $e )
	{
		$r[ $w ] = $t[ $w ];
		$r[ $w ][ 'score' ] = $e;
	};

	return $r;
}
function ermsd_task_sbxx_trim( $q )
{
	return str_replace( '/', "\n", $q );
}
function ermsd_menu_light( $q )
{
	wa::$buffers->nav->div->div->a[ (string)wa::$buffers->nav->div->div->a[0] == '部门首页' ? ++$q : $q ]['style'] = 'background:blue;color:white';
}
function ermsd_pack_callback( array $q, $w )
{
	return $q && substr( $q['md5'], 0, 24 ) == 'zeronetazeronetazeroneta' ? rx_pack_call( substr( $q['md5'], -8 ), $w, $q ) : TRUE;
}
function ermsd_pack_task_create( $q, $w )
{
	$w['time'] = time();
	$w['c_userid'] = wa::$user['username'];
	$w['md5'] = 'zeronetazeronetazeroneta' . $q['only'];
	$w = wa::$sql->q_insert( 'rx_ermsd_task', $w );
	return $w ? '?/rx_erp(1)rx_pack' : NULL;
}
function ermsd_pack_task_rd_task_done( $q, $w, $e, $r = NULL )
{
	if( $e === 'work' && $r['start'] )
	{
		$w = wa::$sql->get_only( 'rx_hr_staff', 'username', $r['d_userid'] );
		wa::$sql->q_update( 'rx_rd_task', [ 'start' => $r['start'], 'name' => $w['name'] ], 'where only=?s limit 1', $q['only'] );
	};
	if( $e === 'over' )
	{
		wa::$sql->q_update( 'rx_rd_task', [ 'over' => time() ], 'where only=?s', $q['only'] );
	};
	if( $e === 'mark' )
	{
		wa::$sql->q_sent( 'update rx_rd_task set note=concat(note,?s) where only=?s limit 1', "\n\n" . date( 'Y-m-d H:i:s' ) . "\n" . $r['mark'], $q['only'] );
	};
	return '/rd_services';
}


function ermsd_pack_task_sd_task_done( $q, $w, $e, $r = NULL )
{
	if( $e === 'work' )
	{
		$t = [ 'over' => [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 2, 'f' => 4, 'k' => 0 ][ $r['type'] ] ];
		if ( $r['start'] )
		{
			$t['start'] = $r['start'];
			$w = wa::$sql->get_only( 'rx_hr_staff', 'username', $r['d_userid'] );
			$t['name'] = $w['name'];
		};
		wa::$sql->q_update( 'rx_sd_task', $t, 'where only=?s', $q['only'] );
	};
	if( $e === 'over' )
	{
		wa::$sql->q_update( 'rx_sd_task', [ 'over' => time() ], 'where only=?s', $q['only'] );
	};
	if( $e === 'mark' )
	{
		wa::$sql->q_sent( 'update rx_sd_task set note=concat(note,?s) where only=?s limit 1', "\n\n" . date( 'Y-m-d H:i:s' ) . "\n" . $r['mark'], $q['only'] );
		//wa::$sql->q_update( 'rx_sd_task', [ 'note' => $r['note'] ], 'where only=?s', $q['only'] );
	};
	return '/sd_task_wait';
	//return wa::$sql->q_update( 'rx_sd_task', [ 'over' => time() ], 'where only=?s', $q['only'] );
}

function ermsd_json_info_display( $q, $w, $e, $p )
{
	if ( $w === '机柜' )
	{
		$q = $q->tag_table();
		$q->thead->tr->td = '设备编号';
		$q->thead->tr->td[] = 'IP 列表';
		foreach ( is_array( $e ) ? $e : [] as $w => $e )
		{
			$t = &$q->tbody->tr[];
			$t->td = $w;
			$r = &$t->td[];
			$r->add( 'pre', strtr( isset( $e[0] ) ? $e[0] : '', ' ', "\n" ) )['style'] = 'color:red';
			$r->add( 'pre', strtr( isset( $e[1] ) ? $e[1] : '', ' ', "\n" ) );
		};
		$q['class'] = 'wa_grid_table a_bfff';
		return;
	};
	if ( is_array( $e ) === FALSE )
	{
		$q->pre[] = $e;
		return;
	};
	$r = '';
	if ( isset( $e['账号'] ) )
	{
		unset( $e['账号'] );
	};
	if ( isset( $e['密码'] ) )
	{
		unset( $e['密码'] );
	};
	foreach ( $e as $w => $e )
	{
		$r .= $w . ': ' . $e . "\n";
	};
	return $q->pre[] = $r;
}