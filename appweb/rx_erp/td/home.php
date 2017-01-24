<?php
wa::$buffers->js[] = 'appweb/rx_erp/td/script.js';
wa::set_action_get( 'rx_pack', function()
{


})::set_action_get( 'td_rd_device', function()
{
	$q = [
		'ip_main' => FALSE,
		'ip_vice' => FALSE,
		'sb_info' => FALSE,

		'pw_userid' => FALSE,
		'pw_team' => FALSE,
		'pw_starts' => TRUE,
		
		'pw_expire' => TRUE,

		'pw_of' => FALSE,
		'pw_be' => FALSE,
		'pw_name' => FALSE,
		'pw_tel' => FALSE,
		'pw_imid' => FALSE,
		'pw_note' => FALSE,

		//'time' => [ TRUE, '录入时间' ],
		'only' => [ TRUE, '设备编号' ],
		'hold' => [ FALSE, '产权' ],

		'room' => [ FALSE, '所在机房' ],
		'note' => [ FALSE, '设备备注' ],
		'sb_type' => [ FALSE, '设备类型' ],
		'bandwidth' => [ FALSE, '带宽' ],
		'defense' => [ FALSE, '防御' ],
		'status' => [ FALSE, '状态' ],
		'pw_time' => [ 'desc', '更新时间' ],
		'pw_editid' => [ FALSE, '更新员工' ],
		0 => '设备维护'
	];

	$w = [ 'status!="机柜设备"' ];

	if ( isset( $_GET[6] ) )
	{
		$e = wa::$sql->escape( '%' . $_GET[6] . ' %' );
		$w[] = '(concat(ip_main," ") like ' . $e . ' or concat(ip_vice," ") like ' . $e . ')';
	};

	$w = wa::get_filter( [ 'only', 'pw_userid', 'pw_imid' ], $w );
	$e = wa::$sql->get_rows( 'rx_rd_device', $w );
	$q = wa::htt_data_table( 'rx_rd_device', $q, function( $q, $w )
	{
		static $bgcolor;
		$q['style'] = 'background:' . ( $bgcolor == '#e0ffe0' ? $bgcolor = '#e0e0ff' : $bgcolor = '#e0ffe0' );
		//$q->td[] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['only'];
		$q->td[] = $w['hold'];
		$q->td[] = $w['room'];
		$q->td[] = $w['note'];
		$q->td[] = $w['sb_type'];
		$q->td[] = $w['bandwidth'];
		$q->td[] = $w['defense'];
		$q->td[] = $w['status'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['pw_time'] );
		$q->td[] = $w['pw_editid'];

		$e = &$q->td[];
		//$e->tag_a( '设备维护' )['onclick'] = 'return !$.window_open("?/rx_erp(1)sd_task_insert","sd_task_insert",{width:600,height:640})';

		$e = $q->get_parent()->add( 'tr' );
		$e['style'] = 'background:' . $bgcolor;
		$r = $e->add( 'td', "IP:\n" );
		$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
		$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
		td_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

		$r = [ '状态:' ];
		$w['pw_userid'] && $r[] = '业务员工: ' . $w['pw_userid'];
		$w['pw_starts'] && $r[] = '开通时间: ' . date( 'Y-m-d H:i:s', $w['pw_starts'] );
		$w['pw_expire'] && $r[] = '到期时间: ' . date( 'Y-m-d H:i:s', $w['pw_expire'] );
		$w['pw_of'] && $r[] = '订单编号: ' . $w['pw_of'];
		$w['pw_be'] && $r[] = '关联信息: ' . $w['pw_be'];
		$w['pw_name'] && $r[] = '联系名称: ' . $w['pw_name'];
		$w['pw_tel'] && $r[] = '联系电话: ' . $w['pw_tel'];
		$e->add( 'td' )->set_attr( 'colspan', 3 )->add( 'pre', join( "\n", $r ) );

		$e->add( 'td' )->set_attr( 'colspan', 2 )->add( 'pre', "联系QQ:\n" . $w['pw_imid'] );

		$e->add( 'td' )->set_attr( 'colspan', 2 )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );




	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	]);
	$q['style'] = 'margin:21px auto';
	$w = count( $q->thead->tr->td );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

	$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'only' => '设备编号', 'pw_userid' => '业务员工', 'pw_imid' => '联系QQ' ], [
		//'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;

	$w->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:240px',
		'placeholder' => '输入 IP 或者 设备编号 按 Enter 搜索',
		'onkeydown' => 'if(event.keyCode==13)return td_device_filter($.query("input",this.parentNode))'
	]);



	$w->span[] = '共找到 ' . $e . ' 个记录';

})::set_action_get( 'td_task_wait', function()
{
	wa::$buffers->script = 'rx_ws_init("/td_task_wait")';
	$q = [
		'only' => FALSE,
		'sbxx' => FALSE,
		'note' => FALSE,
		'time' => [ 'desc', '提交时间' ],
		'user_name' => [ FALSE, '业务员' ],
		'pkip' => [ FALSE, '设备IP' ],
		'pkno' => [ FALSE, '设备编号' ],
		'userid' => [ FALSE, '提交员工' ],
		'riqq' => [ FALSE, '报障QQ' ],
		'imqq' => [ FALSE, '提交QQ' ],
		4 => '领取任务'
	];
	$w = wa::get_filter( [], [ '`to`=2', 'start is null' ] );
	$e = wa::$sql->get_rows( 'rx_sd_task', $w );
	$sd_staff = rx_get_staff( '客服部' );
	$q = wa::htt_data_table( 'rx_sd_task', $q, function( $q, $w ) use( &$sd_staff )
	{
		$r = &$q->td[];
		$r['onclick'] = 'rx_tr_resh(this.parentNode)';
		$r['style'] = 'cursor:pointer;text-decoration:underline';
		$r[0] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['user_name'];
		$q->td[] = $w['pkip'];
		$q->td[] = $w['pkno'];
		$q->td[] = isset( $sd_staff[ $w['userid'] ] ) ? $sd_staff[ $w['userid'] ]['name'] : $w['userid'];
		$e = &$q->td[];
		$e->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
		$e = &$q->td[];
		$e->tag_a( $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
		$r = &$q->td[];
		$r->tag_a( '领取任务', '?/rx_erp/ajax(1)td_task_wait(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href);';
		$q = $q->get_parent()->add( 'tr' );
		$q['style'] = 'display:none';
		$r = &$q->td[];
		$r['colspan'] = 3;
		$r->pre = $w['sbxx'];

		$r = &$q->td[];
		$r['colspan'] = 5;
		$r->pre = $w['note'];

	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	] );
	$q['style'] = 'margin:21px auto';

})::set_action_get( 'td_task_wait3', function()
{
	wa::$buffers->script = 'rx_ws_init("/td_task_wait")';
	$q = [
		'only' => FALSE,
		'sbxx' => FALSE,
		'note' => FALSE,
		'time' => [ 'desc', '提交时间' ],
		'user_name' => [ FALSE, '业务员' ],
		'pkip' => [ FALSE, '设备IP' ],
		'pkno' => [ FALSE, '设备编号' ],
		'userid' => [ FALSE, '提交员工' ],
		'riqq' => [ FALSE, '报障QQ' ],
		'imqq' => [ FALSE, '提交QQ' ],
		4 => '领取任务'
	];
	$w = wa::get_filter( [], [ '`to`=3', 'start is null' ] );
	$e = wa::$sql->get_rows( 'rx_sd_task', $w );
	$sd_staff = rx_get_staff( '客服部' );
	$q = wa::htt_data_table( 'rx_sd_task', $q, function( $q, $w ) use( &$sd_staff )
	{
		$r = &$q->td[];
		$r['onclick'] = 'rx_tr_resh(this.parentNode)';
		$r['style'] = 'cursor:pointer;text-decoration:underline';
		$r[0] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['user_name'];
		$q->td[] = $w['pkip'];
		$q->td[] = $w['pkno'];
		$q->td[] = isset( $sd_staff[ $w['userid'] ] ) ? $sd_staff[ $w['userid'] ]['name'] : $w['userid'];
		$e = &$q->td[];
		$e->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
		$e = &$q->td[];
		$e->tag_a( $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
		$r = &$q->td[];
		$r->tag_a( '领取任务', '?/rx_erp/ajax(1)td_task_wait(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href);';
		$q = $q->get_parent()->add( 'tr' );
		$q['style'] = 'display:none';
		$r = &$q->td[];
		$r['colspan'] = 3;
		$r->pre = $w['sbxx'];

		$r = &$q->td[];
		$r['colspan'] = 5;
		$r->pre = $w['note'];

	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	] );
	$q['style'] = 'margin:21px auto';

})::set_action_get( 'td_task_my', function()
{
	wa::$buffers->script = 'rx_ws_init("/td_task_wait")';
	$q = [
		'only' => FALSE,
		'sbxx' => FALSE,
		'note' => FALSE,
		'time' => [ 'desc', '提交时间' ],
		'user_name' => [ FALSE, '业务员' ],
		'pkip' => [ FALSE, '设备IP' ],
		'pkno' => [ FALSE, '设备编号' ],
		'userid' => [ FALSE, '提交员工' ],
		'riqq' => [ FALSE, '报障QQ' ],
		'imqq' => [ FALSE, '提交QQ' ],
		'start' => [ FALSE, '开始时间' ],
		'over' => [ FALSE, '完成时间' ]
	];
	$w = wa::get_filter( [], [ '`to`>1', 'name="' . wa::$user['username'] . '"' ] );
	$e = wa::$sql->get_rows( 'rx_sd_task', $w );
	$sd_staff = rx_get_staff( '客服部' );
	$q = wa::htt_data_table( 'rx_sd_task', $q, function( $q, $w ) use( &$sd_staff )
	{
		$r = &$q->td[];
		$r['onclick'] = 'rx_tr_resh(this.parentNode)';
		$r['style'] = 'cursor:pointer;text-decoration:underline';
		$r[0] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['user_name'];
		$q->td[] = $w['pkip'];
		$q->td[] = $w['pkno'];
		$q->td[] = isset( $sd_staff[ $w['userid'] ] ) ? $sd_staff[ $w['userid'] ]['name'] : $w['userid'];
		$e = &$q->td[];
		$e->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
		$e = &$q->td[];
		$e->tag_a( $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
		$q->td[] = date( 'Y-m-d H:i:s', $w['start'] );
		$r = &$q->td[];
		if ( $w['over'] )
		{
			$r[0] = date( 'Y-m-d H:i:s', $w['over'] );
		}
		else
		{
			$r->tag_a( '回复', '?/rx_erp/ajax(1)td_task_my(2)' . $w['only'] )->set_attrs([
				'data-prompt' => '请输入',
				'onclick' => 'return wa.ajax_query(this.href,this.dataset);'

			]);
			$r->span = ' | ';
			$r->tag_a( '完成', '?/rx_erp/ajax(1)td_task_my(2)' . $w['only'] )['onclick'] = 'return wa.ajax_query(this.href);';
		};



		$q = $q->get_parent()->add( 'tr' );
		$q['style'] = 'display:none';
		$r = &$q->td[];
		$r['colspan'] = 3;
		$r->pre = $w['sbxx'];

		$r = &$q->td[];
		$r['colspan'] = 6;
		$r->pre = $w['note'];

	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	] );
	$q['style'] = 'margin:21px auto';

})::set_action_get( 'td_task_record', function()
{
	wa::$buffers->script = 'rx_ws_init("/td_task_wait")';
	$q = [
		'only' => FALSE,
		'sbxx' => FALSE,
		'note' => FALSE,
		'time' => [ 'desc', '提交时间' ],
		'user_name' => [ FALSE, '业务员' ],
		'pkip' => [ FALSE, '设备IP' ],
		'pkno' => [ FALSE, '设备编号' ],
		'userid' => [ FALSE, '提交员工' ],
		'riqq' => [ FALSE, '报障QQ' ],
		'imqq' => [ FALSE, '提交QQ' ],
		'name' => [ FALSE, '员工ID' ],
		'start' => [ FALSE, '开始时间' ],
		'over' => [ FALSE, '完成时间' ]
	];
	$w = wa::get_filter( [], [ '`to`>1', 'start is not null' ] );
	$e = wa::$sql->get_rows( 'rx_sd_task', $w );
	$sd_staff = rx_get_staff( '客服部' );
	$q = wa::htt_data_table( 'rx_sd_task', $q, function( $q, $w ) use( &$sd_staff )
	{
		$r = &$q->td[];
		$r['onclick'] = 'rx_tr_resh(this.parentNode)';
		$r['style'] = 'cursor:pointer;text-decoration:underline';
		$r[0] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['user_name'];
		$q->td[] = $w['pkip'];
		$q->td[] = $w['pkno'];
		$q->td[] = isset( $sd_staff[ $w['userid'] ] ) ? $sd_staff[ $w['userid'] ]['name'] : $w['userid'];
		$e = &$q->td[];
		$e->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
		$e = &$q->td[];
		$e->tag_a( $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
		$q->td[] = $w['name'];
		$q->td[] = date( 'Y-m-d H:i:s', $w['start'] );
		$r = &$q->td[];
		if ( $w['over'] )
		{
			$r[0] = date( 'Y-m-d H:i:s', $w['over'] );
		}
		else
		{
			$r[0] = '等待完成';
		};
		$q = $q->get_parent()->add( 'tr' );
		$q['style'] = 'display:none';
		$r = &$q->td[];
		$r['colspan'] = 3;
		$r->pre = $w['sbxx'];

		$r = &$q->td[];
		$r['colspan'] = 7;
		$r->pre = $w['note'];

	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	] );
	$q['style'] = 'margin:21px auto';

})::set_action_get( 'td_staff_update', function()
{
	rx_staff_update( '技术部', [
		'rx_team' => [ '' => '不分配' ],
		'rx_action' => [
			'td_staff_update' => '允许部门员工更新（重要）',
			//'rx_pack' => '允许处理数据包',
			//'rx_notice_insert' => '允许发布通知',
			'td_rd_device' => '允许访问设备查询',
			'td_task_wait' => '允许访问网安的任务',
			'td_task_wait3' => '允许访问网管的任务',
			'td_task_my' => '允许访问我的任务记录',
			'td_task_record' => '允许访问所有任务记录'
		]
	] );
})::end_action(function()
{
	rx_notice_record( '技术部' );
});