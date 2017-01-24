<?php
$rd_hold_list = [ '锐讯', '客户' ];
$rd_room_list = [ '佛山德胜机房', '茂名机房', '厦门机房', '泉州机房', '美国 Digital Realty', '富士通机房', '中山机房', '香港机房' ];
$rd_form_ip0_editor = [
	'ip_prefix_address' => [
		'test' => [ 1, 16, '/^\d{1,3}(\.\d{1,3}){2}$/' ],
		'name' => 'IP 前缀地址',
		'type' => 'text',
		'note' => '格式 192.168.0'
	],
	'ip_suffix_start' => [
		'test' => [ 1, 3, '/^(\d{1,2}|1\d{2}|2[0-4]\d|25[0-5])$/' ],
		'name' => 'IP 后缀开始',
		'type' => 'text',
		'note' => '数字 0-255'
	],
	'ip_suffix_end' => [
		'test' => [ 1, 3, '/^(\d{1,2}|1\d{2}|2[0-4]\d|25[0-5])$/' ],
		'name' => 'IP 后缀结束',
		'type' => 'text',
		'note' => '数字 0-255'
	],
	'netmask' => [
		'test' => [ 1, 16, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => 'IP 掩码',
		'type' => 'text',
		'note' => '子网掩码'
	],
	'gateway' => [
		'test' => [ 1, 16, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => 'IP 网关',
		'type' => 'text',
		'note' => '网关地址'
	],
	'network_id' => [
		'test' => [ 1, 16, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => '网络号',
		'type' => 'text',
		'note' => '网络编号'
	],
	'broadcast_address' => [
		'test' => [ 1, 16, '/^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => '广播地址',
		'type' => 'text',
		'note' => 'IP 地址'
	],
	'status' => [
		'test' => [ 0, 6 ],
		'name' => '整段状态',
		'type' => 'select',
		'value' => [ '' => '默认' ]
	],
	'note' => [
		'test' => [ 0, 64 ],
		'name' => '备注',
		'type' => 'text',
		'note' => '备注信息'
	]
];
$rd_form_ip1_status = [ '空闲', '测试', '使用' ];
$rd_form_ip1_editor = [
	'select' => [
		'test' => [ 0, 32 ],
		'name' => '使用范围',
		'type' => 'select',
		'value' => [ '' => '所有' ]
	],
	'status' => [
		'test' => [ 0, 64 ],
		'name' => '状态',
		'type' => 'select'
	],
	'note' => [
		'test' => [ 0, 64 ],
		'name' => '备注',
		'type' => 'text'
	]
];
$rd_form_forcer_editor = [
	'hold' => [
		'test' => [ 6, 6 ],
		'name' => '所属产权',
		'type' => 'select'
	],
	'room' => [
		'test' => [ 1, 32 ],
		'name' => '所在机房',
		'type' => 'select',
		'value' => [ '' => '请选择' ]
	],
	'name' => [
		'test' => [ 1, 16, '/^[\w\-]+$/' ],
		'name' => '编号',
		'type' => 'text',
		'note' => '机柜编号名称'
	],
	'status' => [
		'test' => [ 6, 6, '/^(空闲|使用)$/' ],
		'name' => '状态',
		'type' => 'select',
		'value' => [ '空闲' => '空闲', '使用' => '使用' ]
	],
	'uses' => [
		'test' => [ 0, 128 ],
		'name' => '用途',
		'type' => 'text',
		'note' => '使用者公司名称'
	],
	'max_0u' => [
		'test' => [ 1, 2, '/^\d{1,2}$/' ],
		'name' => '刀片机位',
		'type' => 'text',
		'note' => '请填写可用U数'
	],
	'max_1u' => [
		'test' => [ 1, 2, '/^\d{1,2}$/' ],
		'name' => '1U机位',
		'type' => 'text',
		'note' => '请填写可用U数'
	],
	// 'inip' => [
	// 	'test' => [ 0, 256, '/^$|^\d{1,3}(\.\d{1,3}){3}$/' ],
	// 	'name' => '可用网段',
	// 	'type' => 'multiselect',
	// 	'note' => 'IP 段搜索',
	// 	'value' => '?/rx_erp/ajax(1)rd_forcer_insert(3)'
	// ],
	'note' => [
		'test' => [ 0, 1024 ],
		'name' => '备注',
		'type' => 'textarea'
	]
];
$rd_form_device_list_type = [ '服务器', '云设备', '独立刀片机', '刀片服务器', '路由器', '防火墙', '交换机', '机柜', '其它' ];
$rd_form_device_list_status = [ '空闲', '测试', '出租', '自用', '变更', '回收', '托管' ];
$rd_form_device_editor = [
	'only' => [
		'test' => [ 1, 16, '/^[A-Z\d\-]+$/' ],
		'name' => '设备编号',
		'type' => 'text',
		'note' => '大写唯一'
	],
	'hold' => [
		'test' => [ 6, 6 ],
		'name' => '所属产权',
		'type' => 'select'
	],
	'room' => [
		'test' => [ 1, 32 ],
		'name' => '所在机房',
		'type' => 'select',
		'value' => [ '' => '请选择' ]
	],
	'note' => [
		'test' => [ 0, 256 ],
		'name' => '机柜备注',
		'type' => 'text'
	],
	'useu' => [
		'test' => [ 1, 2, '/^\d{1,2}$/' ],
		'name' => '占用U数',
		'type' => 'text',
		'note' => '设备U数'
	],
	'sb_type' => [
		'test' => [ 6, 15 ],
		'name' => '设备类型',
		'type' => 'select',
		'value' => [ '' => '请选择' ]
	],
	'link' => [
		'test' => [ 0, 16 ],
		'name' => '关联编号',
		'type' => 'text'
	],
	'bandwidth' => [
		'test' => [ 0, 32 ],
		'name' => '带宽',
		'type' => 'text'
	],
	'defense' => [
		'test' => [ 0, 32 ],
		'name' => '防御',
		'type' => 'text'
	],
	'ip_main' => [
		'test' => [ 0, 4096, '/^$|^\d{1,3}(\.\d{1,3}){3}(\s+\d{1,3}(\.\d{1,3}){3})*$/' ],
		'name' => 'IP 主',
		'type' => 'multiselect',
		'value' => '?/rx_erp/ajax(1)'
	],
	'ip_vice' => [
		'test' => [ 0, 4096, '/^$|^\d{1,3}(\.\d{1,3}){3}(\s+\d{1,3}(\.\d{1,3}){3})*$/' ],
		'name' => 'IP 副',
		'type' => 'multiselect',
		'value' => '?/rx_erp/ajax(1)'
	],
	'sb_info' => [
		'test' => [ 0, 4096 ],
		'name' => '设备信息',
		'type' => 'textarea'
	]
];
$rd_form_device_add_device = [
	'only' => [
		'test' => [ 1, 16, '/^[A-Z\d\-]+$/' ],
		'name' => '设备编号',
		'type' => 'text',
		'note' => '大写唯一'
	],
	'hold' => [
		'test' => [ 6, 6 ],
		'name' => '所属产权',
		'type' => 'select'
	],
	'useu' => [
		'test' => [ 1, 2, '/^\d{1,2}$/' ],
		'name' => '占用U数',
		'type' => 'text',
		'note' => '设备U数'
	],
	'sb_type' => [
		'test' => [ 6, 15 ],
		'name' => '设备类型',
		'type' => 'select',
		'value' => [ '' => '请选择' ]
	],
	'link' => [
		'test' => [ 0, 16 ],
		'name' => '关联编号',
		'type' => 'text'
	],
	'ip_main' => [
		'test' => [ 0, 105, '/^$|^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => 'IP 主',
		'type' => 'multiselect',
	],
	'ip_vice' => [
		'test' => [ 0, 105, '/^$|^\d{1,3}(\.\d{1,3}){3}$/' ],
		'name' => 'IP 副',
		'type' => 'multiselect',
	],
	'sb_info' => [
		'test' => [ 0, 4069 ],
		'name' => '设备信息',
		'type' => 'textarea'
	]
];
$rd_form_device_status = [
	'status' => [
		'test' => [ 6, 6 ],
		'name' => '设备状态',
		'type' => 'select'
	],
	'pw_userid' => [
		//'test' => [ 5, 5, '/\d{5}$/' ],
		'test' => [ 6, 9 ],
		'name' => '业务员工',
		'type' => 'text',
		'note' => '员工销售统计'
	],
	'pw_team' => [
		'test' => [ 0, 32 ],
		'name' => '业务团队',
		'type' => 'text',
		'note' => '团队销售统计'
	],
	'pw_starts' => [
		'test' => [ 0, 10, '/^$|^\d{10}$/' ],
		'name' => '开通时间',
		'type' => 'text',
		'note' => '开通时间戳'
	],
	'pw_expire' => [
		'test' => [ 0, 10, '/^$|^\d{10}$/' ],
		'name' => '到期时间',
		'type' => 'text',
		'note' => '到期时间戳'
	],
	'pw_of' => [
		//'test' => [ 0, 16, '/^\d{0,16}$/' ],
		'test' => [ 0, 16, '/^$|^[A-Z\d\-]+$/' ],
		'name' => '订单编号',
		'type' => 'text'
	],
	'pw_be' => [
		'test' => [ 0, 16, '/^\d{0,16}$/' ],
		'name' => '关联信息',
		'type' => 'text'
	],
	'pw_name' => [
		'test' => [ 0, 64 ],
		'name' => '联系名称',
		'type' => 'text'
	],
	'pw_tel' => [
		'test' => [ 0, 128, '/^(\d+[\r\n]{0,2})*$/' ],
		'name' => '联系电话',
		'type' => 'textarea',
		'note' => '换行切开'
	],
	'pw_imid' => [
		'test' => [ 0, 256, '/^(\d+[\r\n]{0,2})*$/' ],
		'name' => '联系ＱＱ',
		'type' => 'textarea',
		'note' => '换行切开'
	],
	'pw_note' => [
		'test' => [ 0, 4096 ],
		'name' => '备注信息',
		'type' => 'textarea'
	]
];
$rd_services_editor = [
	'd_group' => [
		'test' => [ 1, 64 ],
		'name' => '完成部门',
		'type' => 'select',
		'value' => [ '德胜机房运维中心' => '德胜机房运维中心' ]
	],
	'describe' => [
		'test' => [ 1, 64 ],
		'name' => '描述',
		'type' => 'text',
		'note' => '任务'
	],
	'note' => [
		'test' => [ 1, 2048 ],
		'name' => '详细',
		'type' => 'textarea',
		'note' => '说明'
	]
];
$rd_serverrd_editor = [
	'd_group' => [
		'test' => [ 1, 64 ],
		'name' => '完成部门',
		'type' => 'select',
		'value' => [ '德胜机房运维中心' => '德胜机房运维中心' ]
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


function rd_ip1_only_ip0( $q )
{
	$w = hexdec( substr( $q, -2 ) );
	return wa::$sql->q_query( 'select * from rx_rd_ip0 where only like ?s and ip_suffix_start<=?s and ip_suffix_end>=?s limit 1', substr( $q, 0, 6 ) . '%', $w, $w )->fetch_assoc();
}
function rd_ip1_useable( $q, $w )
{
	$q = explode( '.', $q, 5 ) + [ 0, 0, 0, 0 ];
	$q = sprintf( '%08x', $q[0] << 24 | $q[1] << 16 | $q[2] << 8 | $q[3] );
	$e = [];
	$q = $w === '云设备'
		? wa::$sql->q_query( 'select ip from rx_rd_ip1 where status="空闲" and `select`=?s and only>=?s limit 256', $w, $q )
		: wa::$sql->q_query( 'select ip from rx_rd_ip1 where status="空闲" and `select` is null and only>=?s limit 256', $q );
	foreach ( $q as $q )
	{
		$e[] = $q['ip'];
	};
	return $e;
}
function rd_device_random_pwd()
{
	$q = '0123456789';
	$w = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$e = strtolower( $w );
	return substr( str_shuffle( $w . $e ), mt_rand( 0, 42 ), 4 ) . substr( str_shuffle( $q ), 2 );
}
function rd_pack_device_ermsd_done( $q, $w )
{
	$e = [ 'pw_userid', 'pw_team', 'pw_starts', 'pw_expire', 'pw_of', 'pw_be', 'pw_name', 'pw_tel', 'pw_imid', 'pw_note' ];
	$e = array_combine( $e, array_fill( 0, count( $e ), NULL ) );
	$e['status'] = '空闲';
	if ( preg_match_all( '/[\w]+/', $w['note'], $w ) )
	{
		$e['sb_username'] = $w[0][0];
		$e['sb_password'] = $w[0][1];
	};
	return wa::$sql->q_update( 'rx_rd_device', $e, 'where only=?s limit 1', $q['device'] )
		? '/?/rx_erp(1)rd_device_admin(7)only.eq.' .$q['device']
		: NULL;
}
function rd_json_info_display( $q, $w, $e, $p )
{
	if ( $w === '机柜' )
	{
		$q = $q->tag_table();
		$q->thead->tr->td = '设备编号';
		$q->thead->tr->td[] = 'IP 列表';
		$w = &$q->thead->tr->td[];
		$w->tag_a( '添加', '?/rx_erp(1)rd_device_update(2)' . $p . '(9)' )['onclick'] = 'return rd_device_add_device(this.href)';
		foreach ( is_array( $e ) ? $e : [] as $w => $e )
		{
			$t = &$q->tbody->tr[];
			$r = &$t->td;
			$r = $r->tag_a( $w, '?/rx_erp(1)rd_device_update(2)' . $p . '(9)' . $w );
			$r['onclick'] = 'return rd_device_add_device(this.href)';
			$r = &$t->td[];
			$r->add( 'pre', strtr( isset( $e[0] ) ? $e[0] : '', ' ', "\n" ) )['style'] = 'color:red';
			$r->add( 'pre', strtr( isset( $e[1] ) ? $e[1] : '', ' ', "\n" ) );
			$r = &$t->td[];
			$r = $r->tag_a( '删除', '?/rx_erp/ajax(1)rd_device_update(2)' . $p . '(6)' . $w );
			$r['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
			$r['data-confirm'] = '删除该设备';
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
function rd_device_add_device( $q, $w, $old_only )
{
	is_array( $q['ip_main'] ) && $q['ip_main'] = join( ' ', $q['ip_main'] );
	is_array( $q['ip_vice'] ) && $q['ip_vice'] = join( ' ', $q['ip_vice'] );
	$q += [
		'pw_time' => time(),
		'pw_editid' => wa::$user['username']
	];
	if ( $_GET[9] === '' || !( $e = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[9] ) ) )
	{
		$q += [
			'time' => time(),
			'room' => $w['only'],
			'note' => $w['note'],
			'bandwidth' => 0,
			'defense' => 0,
			'status' => '机柜设备',
		];
	};
	$q['useu'] |= 0;
	$p = wa::$sql->get_only( 'rx_rd_forcer', 'name', $w['note'] );
	return wa::$sql->q_task_callback(function() use( &$q, &$w, &$e, &$p, $old_only )
	{
		$sb_info = json_decode( $w['sb_info'], TRUE );
		$sb_info = is_array( $sb_info ) ? $sb_info : [];
		if ( isset( $sb_info[ $old_only ] ) )
		{
			unset( $sb_info[ $old_only ] );
		};
		$sb_info[ $q['only'] ] = [ $q['ip_main'], $q['ip_vice'] ];
		$sb_info = json_encode( $sb_info, JSON_UNESCAPED_UNICODE );
		if ( $e )
		{
			$sb_info = $sb_info === $w['sb_info'] ? 1 : wa::$sql->q_update( 'rx_rd_device', [ 'sb_info' => $sb_info ], 'where only=?s', $w['only'] );
			$r = $e['useu'] - $q['useu'];
			$t = $q['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
			if ( $e['sb_type'] === $q['sb_type'] )
			{
				$t = $r ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a+?i where name=?s limit 1', $t, $t, $r, $p['name'] ) : 1;
			}
			else
			{
				$p[ $e['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u' ] += $e['useu'];
				$p[ $t ] -= $q['useu'];
				$t = wa::$sql->q_update( 'rx_rd_forcer', [
					'sur_0u' => $p['sur_0u'],
					'sur_1u' => $p['sur_1u'],
					'last_update_time' => time(),
					'last_update_userid' => wa::$user['username']
				], 'where name=?s limit 1', $p['name'] );
			};
			return $t && $sb_info && wa::$sql->q_update( 'rx_rd_device', $q, 'where only=?s', $e['only'] );
		};
		$r = $q['sb_type'] === '刀片服务器' ? 'sur_0u' : 'sur_1u';
		$r = $q['useu'] ? wa::$sql->q_sent( 'update rx_rd_forcer set ?a=?a-?i where name=?s limit 1', $r, $r, $q['useu'], $q['note'] ) : 1;
		return $r
			&& wa::$sql->q_update( 'rx_rd_device', [ 'sb_info' => $sb_info ], 'where only=?s', $w['only'] )
			&& wa::$sql->q_insert( 'rx_rd_device', $q );
	});
}
function rd_oplog( string $q, array $w, array $e = NULL ):bool
{
	$q = [
		'time' => time(),
		'userid' => wa::$user['username'],
		'action' => $_GET[1],
		'query' => $q,
		'mark_time' => 0,
		'json_data0' => json_encode( $w, JSON_UNESCAPED_UNICODE ),
		'json_data1' => '{}'
	];
	$e === NULL || $q['json_data1'] = json_encode( $e + $w, JSON_UNESCAPED_UNICODE );
	$q['only'] = md5( join( $q ) );
	return wa::$sql->q_insert( 'rx_rd_oplog', $q );
}
$rd_field_name = [
	'only' => '设备编号',
	'time' => '录入时间',
	'hold' => '所属产权',
	'room' => '选择所在机房',
	'note' => '备注信息',
	'useu' => '占用U数',
	'bandwidth' => '带宽',
	'defense' => '防御',
	'ip_main' => 'IP 主',
	'ip_vice' => 'IP 副',
	'sb_type' => '设备类型',
	'sb_info' => '设备信息',
	'status' => '状态',
	'pw_time' => '更新时间',
	'pw_editid' => '更新员工',
	'pw_userid' => '业务员工',
	'pw_team' => '业务团队',
	'pw_starts' => '开通时间',
	'pw_expire' => '到期时间',
	'pw_of' => '订单编号',
	'pw_be' => '关联信息',
	'pw_name' => '联系名称',
	'pw_tel' => '联系电话',
	'pw_imid' => '联系QQ',
	'pw_note' => '备注信息'
];
function rd_data_diff( webapp_htt $q, array $w, array $e )
{
	global $rd_field_name;
	$q = $q->tag_table();
	$q['class'] = 'wa_grid_table';
	$q['style'] = 'width:700px;background:rgba(255,255,255,0.6)';
	$q->thead->tr->td = '字段';
	$r = &$q->thead->tr->td[];
	
	$r[0] = '数据';
	if ( $e )
	{
		$r['style'] = 'width:50%';
		$r = &$q->thead->tr->td[];
		$r['style'] = 'width:50%';
		$r[0] = '更新';
		foreach ( array_diff( $w, $e ) as $t => $y )
		{
			$r = &$q->tbody->tr[];
			$r->td = isset( $rd_field_name[ $t ] ) ? $rd_field_name[ $t ] : $t;
			$r->td[]->pre = $y;
			$r->td[]->pre = $e[ $t ];
		};
	}
	else
	{
		$r['style'] = 'width:100%';
		foreach ( $w as $t => $y )
		{
			$r = &$q->tbody->tr[];
			$r->td = isset( $rd_field_name[ $t ] ) ? $rd_field_name[ $t ] : $t;
			$r->td[]->pre = $y;//(string)$y;
		};
	};



	

}
function ermsd_pack_task_create( $q, $w )
{
	$w['time'] = time();
	$w['c_userid'] = wa::$user['username'];
	$w['md5'] = 'zeronetazeronetazeroneta' . $q['only'];
	$w = wa::$sql->q_insert( 'rx_ermsd_task', $w );
	return $w ? '?/rx_erp(1)rx_pack' : NULL;
}