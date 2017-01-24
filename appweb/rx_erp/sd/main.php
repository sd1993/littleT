<?php
$sd_service_qq = [
	'2880269188' => '（售后1）',
	'2169164879' => '（售后2）',
	'2391965115' => '（售后3）',
	'1356348191' => '（售后4）',
	'1744199881' => '（售后5）',
	'2880269189' => '（队长）',
	'2881618276' => '（VIP维护）'
];
$sd_current_qq = isset( $_COOKIE['rx_sd_qq'], $sd_service_qq[ $_COOKIE['rx_sd_qq'] ] ) ? $_COOKIE['rx_sd_qq'] : NULL;
$sd_services_editor = [
	'to' => [
		'test' => [ 1, 1, '/^(1|2)$/' ],
		'name' => '发送给',
		'type' => 'select',
		'note' => '处理',
		'value' => [ 1 => '德胜机房运维中心', 2 => '技术部门（网安）', 3 => '技术部门（网管）' ]
	],
	'riqq' => [
		'test' => [ 0, 12, '/^\d{0,12}$/' ],
		'name' => '报障QQ',
		'type' => 'text',
		'note' => 'QQ号码'
	],
	'describe' => [
		'test' => [ 1, 64 ],
		'name' => '描述',
		'type' => 'text',
		'note' => '任务'
	],
	'sbxx' => [
		'test' => [ 0, 2048 ],
		'name' => '设备信息',
		'type' => 'textarea',
		'note' => '只读'
	],
	'note' => [
		'test' => [ 1, 2048 ],
		'name' => '任务说明',
		'type' => 'textarea',
		'note' => '必填'
	]
];
function sd_riqq_find( $q, $w )
{
	if ( $w )
	{
		preg_match_all( '/\d+/', $q, $q );
		return in_array( $w, $q[0] );
	};
	return TRUE;
}
function sd_no_serviced( $q )
{
	if ( isset( $_GET[9] ) && $_GET[9] === 'confirm' )
	{
		return TRUE;
	};
	$w = wa::$sql->q_query( 'select time,userid,note from rx_sd_task where pkip=?s and time>?i order by time desc', $q, time() - 86400 );
	if ( $w->num_rows )
	{
		$rx_get_staff = rx_get_staff( '客服部' );
		$q = [ 'IP: ' . $q . ', 继续提交按(确定) 否则(取消)' ];
		foreach ( $w as $w )
		{
			$q[] = $w['note'];
			$q[] = '********** ' . date( 'Y-m-d H:i:s', $w['time'] ) . '(' . ( isset( $rx_get_staff[ $w['userid'] ] ) ? $rx_get_staff[ $w['userid'] ]['name'] : $w['userid'] ) . ') **********';
		};
		wa::$buffers = [ join( "\n", $q ), 'sd_task_submit_confirm' ];
		return FALSE;
	};
	return TRUE;
}
function sd_json_info_display( $q, $w, $e, $p )
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
function ermsd_pack_task_create( $q, $w )
{
	$w['time'] = time();
	$w['c_userid'] = wa::$user['username'];
	$w['md5'] = 'zeronetazeronetazeroneta' . $q['only'];
	$w = wa::$sql->q_insert( 'rx_ermsd_task', $w );
	return $w ? '?/rx_erp(1)rx_pack' : NULL;
}


// function sd_ip_get_sbxx( $q, &$w )
// {
// 	foreach( json_decode( file_get_contents( 'http://183.60.197.235:8084/remote_api/index.php?ip='.$q.'&fuck=ruixunidc008' ), TRUE ) as $e )
// 	{
// 		if ( strpos( "\n" . $e['IP'] . "\n" . $e['vice_IP'] . "\n", "\n" . $q . "\n" ) !== FALSE )
// 		{
// 			return $w = $e;
// 		};
// 	};
// 	return NULL;
// }