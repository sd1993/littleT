<?php
//exit('数据表扩展2分钟,请等待当前操作,稍后重试!');
function rx_get_staff( $q )
{
	$w = [];
	foreach ( wa::$sql->q_query( 'select username,name,rx_team,office_imid from rx_hr_staff where resign=0 and `group`=?s and online="1"', $q ) as $q )
	{
		$w[ $q['username'] ] = [
			'name' => $q['name'],
			'team' => $q['rx_team'],
			'imid' => $q['office_imid']
		];
	};
	return $w;
}
function rx_pack_call( $q, $w, $r = NULL )
{
	if ( $q = wa::$sql->get_only( 'rx_public_pack', 'only', $q ) )
	{
		$e = [
			1 => 'rx_ws_callback',
			'send' => 'reload '
		];
		if ( $w == 'delete' )
		{
			$e[0] = '/rx_pack_' . md5( $q['d_group'] );
			$e['come'] = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '?/rx_erp(1)rx_pack';
			return wa::$sql->q_delete( 'rx_public_pack', 'where only=?s and start is null limit 1', $q['only'] ) && wa::$buffers = $e;
		};
		if ( $w == 'unpack' )
		{
			$e[0] = '/rx_pack_' . md5( $q['c_group'] );
			$e['come'] = rx_pack_unpack( $q );
			return $e['come'] && wa::$buffers = $e;
		};
		if ( $w == 'done' )
		{
			$e[0] = '/rx_pack_' . md5( $q['c_group'] );
			$e['come'] = rx_pack_done( $q );
			return $e['come'] && wa::$buffers = $e;
		};
		return $q['callback'] ? $q['callback']( $q, json_decode( $q['data'], TRUE ), $w, $r ) : TRUE;
	};
}

function rx_pack_send( $q, $w, $e = NULL, $r = NULL, array $t = NULL, $y = NULL )
{
	$q = [
		'time' => time(),
		'c_group' => wa::$user['group'],
		'c_team' => wa::$user['rx_team'],
		'c_userid' => wa::$user['username'],
		'd_group' => $q,
		'describe' => $w
	];
	$e && $q['device'] = $e;
	$r && $q['unpack'] = $r;
	$t && $q['data'] = json_encode( $t, JSON_UNESCAPED_UNICODE );
	$y && $q['callback'] = $y;
	$q['only'] = wa::short_hash( join( $q ) );
	return wa::$sql->q_insert( 'rx_public_pack', $q ) ? $q['only'] : 0;
}

function rx_pack_unpack( $q )
{
	if ( !$q['start'] )
	{
		$w = $q['unpack'] ? $q['unpack']( $q, json_decode( $q['data'], TRUE ), 'unpack' ) : '?/rx_erp(1)rx_pack';
		return $w && wa::$sql->q_update( 'rx_public_pack', [
			'start' => time(),
			'd_team' => wa::$user['rx_team'],
			'd_userid' => wa::$user['username']
		], 'where only=?s and start is null limit 1', $q['only'] )
		? $w
		: NULL;
	};
}

function rx_pack_done( $q )
{
	if ( $q['start'] && !$q['done'] )
	{
		$w = $q['callback'] ? $q['callback']( $q, json_decode( $q['data'], TRUE ), 'done' ) : '?/rx_erp(1)rx_pack';
		return $w && wa::$sql->q_update( 'rx_public_pack', [ 'done' => time() ],
			'where only=?s and start is not null and done is null limit 1', $q['only'] )
			? $w
			: NULL;
	};
}

function rx_pack_record( $q, $w = [] )
{
	wa::$buffers->script = 'rx_ws_init("/rx_pack_' . md5( $q ) . '")';
	$rx_staff = rx_get_staff( $q );
	$w[] = '(c_group="' . $q . '" or d_group="' . $q . '")';
	if ( isset( $_GET[6] ) )
	{
		$w[] = $_GET[6] ? 'done is null' : 'start is null';
	};
	$w = wa::get_filter( [ 'only', 'time', 'start', 'done', 'describe', 'c_userid', 'd_userid' ], $w );
	$e = wa::$sql->get_rows( 'rx_public_pack', $w );
	$q = wa::htt_data_table( 'rx_public_pack', [
		'only' => FALSE,
		'data' => FALSE,
		'time' => [ 'desc', '创建时间' ],
		'c_group' => [ FALSE, '创建部门' ],
		'c_userid' => [ FALSE, '创建ID' ],
		'describe' => [ FALSE, '描述' ],
		'device' => [ FALSE, '设备编号' ],
		'start' => [ FALSE, '开始时间' ],
		'd_group' => [ FALSE, '完成部门' ],
		'd_userid' => [ FALSE, '完成ID' ],
		'done' => [ FALSE, '完成时间' ],
		0 => '操作'
	], function( $w, $e ) use( $q, &$rx_staff )
	{
		static $bgcolor;
		$w['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
		$r['data-confirm'] = '不能撤消';
		$r['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
		$w->add( 'td', date( 'Y-m-d H:i:s', $e['time'] ) );
		$w->add( 'td', $e['c_group'] );
		$w->add( 'td', isset( $rx_staff[ $e['c_userid'] ] ) ? $rx_staff[ $e['c_userid'] ]['name'] : $e['c_userid'] );
		$w->add( 'td', $e['describe'] );
		$w->add( 'td', $e['device'] );
		$e['start']
			? $w->add( 'td', date( 'Y-m-d H:i:s', $e['start'] ) )['style'] = 'color:green'
			: $w->add( 'td', '等待处理' )['style'] = 'color:red';
		$w->add( 'td', $e['d_group'] );
		$w->add( 'td', isset( $rx_staff[ $e['d_userid'] ] ) ? $rx_staff[ $e['d_userid'] ]['name'] : $e['d_userid'] );
		$e['done']
			? $w->add( 'td', date( 'Y-m-d H:i:s', $e['done'] ) )['style'] = 'color:green'
			: $w->add( 'td', '等待完成' )['style'] = 'color:red';
		$r = $w->add( 'td' );
		if ( $e['d_group'] == $q )
		{
			if ( $e['start'] )
			{
				$e['done']
					? $r[0] = '延迟 ' . number_format( $e['start'] - $e['time'] ) . '/s, 用时 ' . number_format( $e['done'] - $e['start'] ) . '/s'
					: $r->tag_a( '完成', '?/rx_erp/ajax(1)rx_pack(2)done(3)' . $e['only'] )['onclick'] = 'return wa.ajax_query(this.href)';
			}
			else
			{
				$r->tag_a( '解包', '?/rx_erp/ajax(1)rx_pack(2)unpack(3)' . $e['only'] )['onclick'] = 'return wa.ajax_query(this.href)';
			};
		}
		else
		{
			if ( $e['start'] )
			{
				$r[0] = '延迟 ' . number_format( $e['start'] - $e['time'] ) . '/s';
				$e['done'] && $r[0] .= ', 用时 ' . number_format( $e['done'] - $e['start'] ) . '/s';
			}
			else
			{
				$q = $r->tag_a( '删除', '?/rx_erp/ajax(1)rx_pack(2)delete(3)' . $e['only'] );
				$q['data-confirm'] = '不能撤消';
				$q['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';
			};
		};
		// if ( $e['data'] && ( $e = json_decode( $e['data'], TRUE ) ) && isset( $e['note'] ) )
		// {
		// 	$q = $w->get_parent()->add( 'tr' )->add( 'td' );
		// 	$q['style'] = 'background:' . $bgcolor;
		// 	$q['colspan'] = 10;
		// 	$w = $q->add( 'pre', strtr( $e['note'], "\n", '	' ) );
		// };
	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	]);
	$q['style'] = 'margin:21px auto';
	$q->thead->tr->add_before( 'tr' )->add( 'td' )->set_attr( 'colspan', 10 )->ins_filter([
		'c_userid' => '创建ID',
		'd_userid' => '完成ID',
		'describe' => '描述' ] );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', 10 );
	$w = &$q->thead->tr[1]->td->form->dl->dt;
	// $e->tag_select(
	// 	[ '' => '所有部门' ] + array_combine( $GLOBALS['rx_groups_name'], $GLOBALS['rx_groups_name'] ),
	// 	isset( $_GET[7] ) && preg_match( '/group\.eq\.([^\/]+)/', urldecode( $_GET[7] ), $w ) ? $w[1] : NULL
	// )->set_attr( 'onchange', 'wa.query_act({7:this.value?"group.eq."+$.urlencode(this.value):null})' );
	$w->tag_button( '等待处理的数据包' )['onclick'] = 'return wa.query_act({6:0})';
	$w->tag_button( '等待完成的数据包' )['onclick'] = 'return wa.query_act({6:1})';
	$w->tag_button( '所有数据包' )['onclick'] = 'return wa.query_act({6:null,7:null})';
	$w->add( 'span', '共找到 ' . $e . ' 个记录' )->set_class( 'a_c000' );
}
function rx_notice_delete()
{
	wa::$buffers = isset( $_GET[2] ) && wa::$sql->q_delete( 'rx_public_notice', 'where only=?s and from_userid=?s limit 1', $_GET[2], wa::$user['username'] )
		? [
			0 => '/rx_notice',
			1 => 'rx_ws_callback',
			'send' => 'reload ',
			'come' => '?/rx_erp' ]
		: [ '数据删除失败,请检查是否是自己发布的通知.', 'warn' ];
}
function rx_notice_insert( $q = NULL )
{
	// foreach ( $GLOBALS['rx_groups'] as $w )
	// {
	// 	isset( $w['icon'] ) && $e[ $w['name'] ] = $w['name'];
	// }
	$w = [
		// 'group' => [
		// 	'test' => [ 1, 11 ],
		// 	'name' => '通知部门',
		// 	'type' => 'radio',
		// 	'value' => [ wa::$user['group'] => '当前部门', '' => '所有部门' ]
		// ],
		'title' => [
			'test' => [ 1, 64 ],
			'name' => '通知标题',
			'type' => 'text'
		],
		'content' => [
			'test' => [ 1, 2048 ],
			'name' => '通知内容',
			'type' => 'textarea'
		]
	];
	if ( $q )
	{
		if ( $w = wa::get_form_post( $w ) )
		{
			$w['time'] = time();
			$w['group'] = $q;
			$w['from_userid'] = wa::$user['username'];
			$w['from_group'] = wa::$user['group'];
			$w['from_name'] = wa::$user['name'];
			$w['only'] = wa::short_hash( join( $w ) );
			wa::$buffers = wa::$sql->q_insert( 'rx_public_notice', $w )
				? [
					0 => '/rx_notice',
					1 => 'rx_ws_callback',
					'send' => 'reload ',
					'come' => '?/rx_erp' ]
				: [ '数据写入失败!请重试.', 'warn' ];
		};
		return;
	};
	wa::htt_form_post( $w )->set(function( $q ) 
	{
		$q['action'] = '?/rx_erp/ajax(1)rx_notice_insert';
		$q->table->tbody->tr->td[1]['style'] = 'width:420px';
		$w = $q->xpath( '//textarea[@name]' )[0]['style'] = 'height:210px;overflow-y:auto';
	});
}
function rx_notice_record( $q )
{
	wa::$buffers->script = 'rx_ws_init("/rx_notice")';
	wa::htt_data_table( 'rx_public_notice', [
		0 => '删除',
		'only' => FALSE,
		'time' => [ 'desc', '发布时间' ],
		'title' => [ FALSE, '标题（查看）' ],
		'from_userid' => [ FALSE, '来自编号' ],
		'from_group' => [ FALSE, '来自部门' ],
		'from_name' => [ FALSE, '来自姓名' ],
		'content' => FALSE
	], function( $q, $w )
	{
		$q->add( 'td' )->tag_a( '删除', '?/rx_erp/ajax(1)rx_notice_delete(2)' . $w['only'] )->set_attrs([
			'onclick' => 'return wa.ajax_query(this.href,this.dataset)',
			'data-confirm' => '不能撤消'
		]);
		$q->add( 'td', date( 'Y-m-d H:i:s', $w['time'] ) );
		$q->add( 'td', $w['title'] )->set_attrs([
			'style' => 'color:blue;cursor:pointer',
			'onclick' => 'rx_tr_resh(this.parentNode,rx_ajax_notice)'
		]);
		$q->add( 'td', $w['from_userid'] );
		$q->add( 'td', $w['from_group'] );
		$q->add( 'td', $w['from_name'] );
		$e = $q->get_parent()->add( 'tr' )->set_style( 'display:none;background:white' )->add( 'td' );
		$e['style'] = 'border:1px solid black';
		$e['colspan'] = 6;
		$e['data-only'] = $w['only'];
	}, [
		'merge_query' => 'where `group` is null or `group`=' . wa::$sql->escape( $q ),
		'page_rows' => 42
	] )->set_css([
		'margin' => '21px auto'
	])->thead->tr->td[2]['style'] = 'width:420px';
}
function rx_staff_update( $q, array $w = NULL )
{
	if ( $w === NULL )
	{
		if (
			isset( $_GET[2] )
			&& wa::$sql->q_query( 'select * from rx_hr_staff where username=?s and `group`=?s and resign=0', $_GET[2], $q )->fetch_bind( $q )
			&& ( $w = wa::get_form_post([
			'rx_team' => [ 'type' => 'text', 'test' => [ 0, 32 ] ],
			'office_imid' => [ 'type' => 'text', 'test' => [ 0, 32, '/^$|^\d{5,12}$/' ] ],
			'rx_action' => [ 'type' => 'text', 'test' => [ 0, 512 ] ],
			'resign' => [ 'type' => 'text', 'test' => [ 1, 8, '/^0$|^\d{8}$/' ] ] ]) ) )
		{
			 is_array( $w['rx_action'] ) && $w['rx_action'] = join( ',', $w['rx_action'] );
			 $w = wa::$sql->q_update( 'rx_hr_staff', $w, 'where username=?s limit 1', $q['username'] )
			 	? [ '?/rx_erp(1)' . $_GET[1], 'goto' ]
			 	: [ '更新失败!可能与更新之前的数据一样!', 'warn' ];
		}
		else
		{
			$w = [ '无效数据', 'warn' ];
		};
		return $w;
	};
	if ( isset( $_GET[2] ) && wa::$sql->q_query( 'select * from rx_hr_staff where username=?s and `group`=?s and resign=0 limit 1', $_GET[2], $q )->fetch_bind( $q ) )
	{
		$w = wa::htt_form_post( [
			'rx_team' => [
				'test' => [ 0, 32 ],
				'name' => '部门团队',
				'type' => 'select',
				'value' => $w['rx_team']
			],
			'office_imid' => [
				'test' => [ 0, 32, '/^$|^\d{5,12}$/' ],
				'name' => 'QQ',
				'type' => 'text',
				'note' => '办公即时通讯'
			],
			'rx_action' => [
				'test' => [ 0, 512 ],
				'name' => '员工权限',
				'type' => 'checkbox',
				'value' => $w['rx_action']
			],
			'resign' => [
				'test' => [ 1, 8, '/^0$|^\d{8}$/' ],
				'name' => '辞职日期',
				'type' => 'text',
				'note' => 'YYYYMMDD'
			]
		], $q );
		$w['action'] = '?/rx_erp/ajax(1)' . $_GET[1] . '(2)' . $_GET[2];
		$w->table->thead->tr->td = '更新员工：' . $q['username'] . ' = ' . $q['name'];
		$w->table->tbody->tr->td[1]['style'] = 'width:280px';
		return;
	};
	$w = '?/rx_erp(1)' . $_GET[1] . '(2)';
	wa::htt_data_table( 'rx_hr_staff', [
		'username' => [ TRUE, '编号' ],
		'name' => [ TRUE, '姓名' ],
		'rx_team' => [ TRUE, '部门团队' ],
		'office_date' => [ 'desc', '入职日期' ],
		'office_type' => [ TRUE, '入职类型' ],
		'office_imid' => [ TRUE, '办公即时通讯' ],
		'formal' => [ TRUE, '转正日期' ],
		'post' => [ TRUE, '职务' ],
		'edu' => [ TRUE, '学历' ],
		'phone' => [ TRUE, '联系电话' ],
		0 => '更新'
	], function( $q, $e ) use( $w )
	{
		$q->add( 'td', $e['username'] );
		$q->add( 'td', $e['name'] );
		$q->add( 'td', $e['rx_team'] );
		$q->add( 'td', $e['office_date'] );
		$q->add( 'td', $e['office_type'] );
		$q->add( 'td', $e['office_imid'] );
		$q->add( 'td', $e['formal'] );
		$q->add( 'td', $e['post'] );
		$q->add( 'td', $e['edu'] );
		$q->add( 'td', $e['phone'] );
		$q->add( 'td' )->tag_a( '更新', $w . $e['username'] );
	}, [
		'merge_query' => 'where resign=0 and `group`=' . wa::$sql->escape( $q )
	] )->set_css([
		'margin' => '21px auto'
	]);
}
function rx_info_display( $q, $w, $e )
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
$rx_work_types = [
	'ermsd' => [
		'重启服务器、重装C盘、全格重装系统',
		'更换网线、更新网卡驱动'
	],
	'td' => [
		'服务器互通、绑定MAC',
		'检查带宽、带宽图',
		'配置内网IP网络'
	],
	'rd' => [
		'机器下架、搬动位置、拆配件、加配件',
		'变更IP、变更带宽、变更防御'
	],
	'sd' => [
		'其他机房系统安装、云服务器安装、软件安装',
		'加CC防护、远程不上、检查网络、放行IP',
		'网络卡、游戏卡、网站卡、远程卡',
		'其他（不清楚问题类型）'
	]
];
$rx_work_post = [
	'riqq' => [
		'test' => [ 0, 16, '/^\d{0,16}$/' ],
		'name' => '报障QQ',
		'type' => 'text'
	],
	'to' => [
		'test' => [ 1, 128 ],
		'name' => '维护类别',
		'type' => 'select',
		'value' => [ '' => '请认真选择维护类型否则维护将得不到有效解决' ]
	],
	'note' => [
		'test' => [ 1, 2048 ],
		'name' => '详细说明',
		'type' => 'textarea'
	]
];
$rx_groups = [
	'gd' => [ 'name' => '总经办' ],
	'mfg' => [
		'name' => '产品部',
		'icon' => 'glyphicon-flag',
		'menu' => [
			[ '部门员工', '?/rx_erp(1)mfg_staff_update' ],
			[ '发布通知', '?/rx_erp(1)rx_notice_insert' ]
		]
	],
	'fd' => [ 'name' => '财务部' ],
	'md' => [ 'name' => '市场部' ],
	'ermsd' => [
		'name' => '德胜机房运维中心',
		'icon' => 'glyphicon-wrench',
		'menu' => [
			[ '数据包', '?/rx_erp(1)rx_pack(7)describe.ne.%E5%9B%9E%E6%94%B6%E8%AE%BE%E5%A4%87' ],
			[ '新的任务', 'javascript:$.window_open("?/rx_erp(1)ermsd_task_insert","ermsd_task_insert",{width:600,height:600})' ],
			[ '等待或进行的任务', '?/rx_erp(1)ermsd_task_work' ],
			[ '验收任务', '?/rx_erp(1)ermsd_task_done' ],
			[ '报表统计', '?/rx_erp(1)ermsd_task_stat' ],
			[ '我的任务', '?/rx_erp(1)ermsd_task_my' ],
			[ '我的记录', '?/rx_erp(1)ermsd_task_record_my' ],
			[ '设备查询', '?/rx_erp(1)ermsd_rd_device' ],
			[ '其他功能', [
				[ '员工在线设置', '?/rx_erp(1)ermsd_staff_online', 'glyphicon-sort' ],
				NULL,
				[ '换班提交', '?/rx_erp(1)ermsd_relief_insert', 'glyphicon-refresh' ],
				[ '换班记录', '?/rx_erp(1)ermsd_relief_record', 'glyphicon-th-list' ],
				NULL,
				[ '所有任务记录', '?/rx_erp(1)ermsd_task_record', 'glyphicon-th-list' ],
				[ '部门员工', '?/rx_erp(1)ermsd_staff_update', 'glyphicon-user' ],
				[ '发布通知', '?/rx_erp(1)rx_notice_insert', 'glyphicon-comment' ]
			] ]
			
		]
	],
	'rd' => [
		'name' => '资源部',
		'icon' => 'glyphicon-hdd',
		'menu' => [
			[ '数据包', '?/rx_erp(1)rx_pack' ],
			[ '维护回收', '?/rx_erp(1)rd_services(7)1' ],
			[ '维护其他', '?/rx_erp(1)rd_serverrd' ],
			[ 'ＩＰ管理', '?/rx_erp(1)rd_ip0_admin' ],
			[ '机柜管理', '?/rx_erp(1)rd_forcer_admin' ],
			[ '设备管理', '?/rx_erp(1)rd_device_admin' ],
			[ '删除记录', '?/rx_erp(1)rd_delete_record' ],
			[ '操作记录', '?/rx_erp(1)rd_action_record' ],
			[ '部门员工', '?/rx_erp(1)rd_staff_update' ],
			[ '发布通知', '?/rx_erp(1)rx_notice_insert' ]
		]
	],
	'sales' => [
		'name' => '销售部',
		'icon' => 'glyphicon-briefcase',
		'menu' => [
			[ '空闲设备', '?/rx_erp(1)sales_empty_device' ],
			[ '我的业务设备', '?/rx_erp(1)sales_my_device' ],
			[ '我的客服维护记录', '?/rx_erp(1)sales_my_services' ],
			[ '本组业务设备', '?/rx_erp(1)sales_group_device' ],
			[ '部门员工', '?/rx_erp(1)sales_staff_update' ]
		]
	],
	'hr' => [
		'name' => '人力资源部',
		'icon' => 'glyphicon-user',
		'menu' => [
			[ '员工资料录入', '?/rx_erp(1)hr_staff_insert' ],
			[ '员工资料列表', '?/rx_erp(1)hr_staff_list' ],
			[ '离职员工', '?/rx_erp(1)hr_staff_resign' ],
			[ '部门员工', '?/rx_erp(1)hr_staff_updated' ]
		]
	],
	'td' => [
		'name' => '技术部',
		'icon' => 'glyphicon-cog',
		'menu' => [
			[ '设备查询', '?/rx_erp(1)td_rd_device' ],
			[ '网安的任务', '?/rx_erp(1)td_task_wait' ],
			[ '网管的任务', '?/rx_erp(1)td_task_wait3' ],
			[ '我的任务记录', '?/rx_erp(1)td_task_my' ],
			[ '所有任务记录', '?/rx_erp(1)td_task_record' ],
			[ '部门员工', '?/rx_erp(1)td_staff_update' ]
		]
	],
	'sd' => [
		'name' => '客服部',
		'icon' => 'glyphicon-phone-alt',
		'menu' => [
			[ '数据包', '?/rx_erp(1)rx_pack' ],
			[ '设备查询', '?/rx_erp(1)sd_rd_device' ],
			[ '设备维护', 'javascript:$.window_open("?/rx_erp(1)sd_task_insert","sd_task_insert",{width:600,height:670})' ],
			[ '我的维护中', '?/rx_erp(1)sd_task_wait_my' ],
			[ '等待维护中', '?/rx_erp(1)sd_task_wait' ],
			[ '设备维护记录', '?/rx_erp(1)sd_task_record' ],
			//[ 'ＩＰ段列表', '?/rx_erp(1)sd_ip0_list' ],
			//[ '设备列表', '?/rx_erp(1)sd_device_list' ],
			//[ '我的数据包', '?/rx_erp(1)sd_pack_my' ],
			[ '部门员工', '?/rx_erp(1)sd_staff_update' ],
			[ '发布通知', '?/rx_erp(1)rx_notice_insert' ]
		]
	],
	'bogz' => [ 'name' => '广州分公司' ]
];
$rx_groups_name = [];
foreach ( $rx_groups as $q )
{
	$rx_groups_name[] = $q['name'];
};
if ( wa::$user['rx_group'] == '' )
{
	$q = '该员工未设置部门权限集!';
	goto rx_erp_deny;
};
if ( strpos( wa::$user['rx_group'], ',' ) === FALSE )
{
	$q = wa::$user['rx_group'];
	goto rx_erp_test;
};
if ( isset( $_COOKIE['rx_group'] ) )
{
	wa::$htt && wa::htt_nav( '部门首页', '?/rx_erp' );
	$q = strpos( ',' . wa::$user['rx_group'] . ',', ',' . $_COOKIE['rx_group'] . ',' ) === FALSE ? NULL : $_COOKIE['rx_group'];
	goto rx_erp_test;
};
if ( wa::$htt )
{
	wa::htt_title( '锐讯 - 选择部门' );
	$q = [];
	foreach ( array_intersect( array_keys( $rx_groups ), explode( ',', wa::$user['rx_group'] ) ) as $w )
	{
		$q[] = [
			$rx_groups[ $w ]['name'],
			'javascript:$.setcookie("rx_group","' . $w . '"),$.go("?/rx_erp");',
			isset( $rx_groups[ $w ]['icon'] ) ? $rx_groups[ $w ]['icon'] : NULL
		];
	};
	wa::htt_nav( '部门选择', $q );
};
$q = '多部门集员工请选择部门模块后继续操作';

rx_erp_deny:
wa::$htt
	? wa::$buffers->write->add( 'div', $q )->set_attr( 'style', 'padding:168px 0;font-size:32px;text-align:center;' )
	: wa::$buffers = $q;
exit;

rx_erp_test:
if ( isset( $rx_groups[ $q ], $rx_groups[ $q ]['icon'] ) )
{
	if ( wa::$htt )
	{
		wa::htt_title( '锐讯 - ' . $rx_groups[ $q ]['name'] );
		foreach ( $rx_groups[ $q ]['menu'] as $w )
		{
			wa::htt_nav( $w[0], $w[1] );
		};
		$w = '/home.php';
	}
	else
	{
		$w = '/ajax.php';
	};
	if ( isset( $_GET[1] )
		&& strpos( ',10454,10038,', ',' . wa::$user['username'] . ',' ) === FALSE
		&& strpos( ',' . wa::$user['rx_action'] . ',', ',' . $_GET[1] . ',' ) === FALSE )
	{
		$q = '未授权行为!';
		goto rx_erp_deny;
	};
	include $q . '/main.php';

	include $q . $w;

	exit;
};
$q = '该部门功能模块还未开放!';
goto rx_erp_deny;
?>