<?php
wa::$buffers->js[] = 'appweb/rx_erp/sales/script.js';
wa::set_action_get( 'rx_pack', function()
{




})::set_action_get( 'sales_empty_device', function()
{
	$q = [
		'ip_main' => FALSE,
		'ip_vice' => FALSE,
		'sb_info' => FALSE,
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
		0 => '提取设备'
	];
	//$w = wa::get_filter( [], [ 'status!="机柜设备"', 'pw_userid="' . wa::$user['name'] . '"' ] );

	$w = [ 'status="空闲"' ];
	if ( isset( $_GET[6] ) )
	{
		$e = wa::$sql->escape( '%' . $_GET[6] . '%' );
		$w[] = '(ip_main like ' . $e . ' or ip_vice like ' . $e . ')';
	};

	$w = wa::get_filter( [ 'sb_info' ], $w );
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
		$e->tag_a( '提取设备', '?/rx_erp(1)sales_device_get(2)' . $w['only'] )['onclick'] = 'return !$.window_open(this.href,"sales_box",{width:480,height:600})';

		$e = $q->get_parent()->add( 'tr' );
		$e['style'] = 'background:' . $bgcolor;
		$r = $e->add( 'td', "IP:\n" );
		$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
		$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$r = $e->add( 'td' )->set_attr( 'colspan', 10 );
		sales_json_info_display( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );
	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	]);
	$q['style'] = 'margin:21px auto';
	$w = count( $q->thead->tr->td );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

	$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'sb_info' => '设备信息' ], [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;

	$w->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:120px',
		'placeholder' => 'IP',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => 'CPU',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '内存',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '硬盘',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_button( '过滤' )->set_attrs([
		'style' => 'margin-right:8px',
		'class' => 'b',
		'onclick' => 'return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->span[] = '共找到 ' . $e . ' 个记录';


})::set_action_get( 'sales_my_device', function()
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
		0 => '设备维护 / 维护记录'
	];
	//$w = wa::get_filter( [], [ 'status!="机柜设备"', 'pw_userid="' . wa::$user['name'] . '"' ] );

	$w = [ 'status!="机柜设备"', 'pw_userid="' . wa::$user['name'] . '"' ];

	if ( isset( $_GET[6] ) )
	{
		$e = wa::$sql->escape( '%' . $_GET[6] . '%' );
		$w[] = '(ip_main like ' . $e . ' or ip_vice like ' . $e . ')';
	};

	$w = wa::get_filter( [ 'status', 'sb_info' ], $w );
//	var_dump($w);
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
		$e->tag_a( '维护设备', '?/rx_erp(1)sales_device_services(2)' . $w['only'] )['onclick'] = 'return !$.window_open(this.href,"sales_box",{width:480,height:480})';
		$e->span = ' | ';
		$e->tag_a( '维护记录' );



		$e = $q->get_parent()->add( 'tr' );
		$e['style'] = 'background:' . $bgcolor;
		$r = $e->add( 'td', "IP:\n" );
		$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
		$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
		sales_json_info_display_my( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

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

	$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'status' => '设备状态', 'sb_info' => '设备信息' ], [
		//'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;


	$r = $w->add_before( 'div' );
	$r['style'] = 'padding-bottom:8px';
	$t = $r->tag_button( '测试设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_my_device(7)status.eq.%E6%B5%8B%E8%AF%95(8)pw_starts.asc")';

	$t = $r->tag_button( '托管设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_my_device(7)status.eq.%E6%89%98%E7%AE%A1(8)pw_expire.asc")';

	$t = $r->tag_button( '出租设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_my_device(7)status.eq.%E5%87%BA%E7%A7%9F(8)pw_expire.asc")';

	$t = $r->tag_button( '变更设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_my_device(7)status.eq.%E5%8F%98%E6%9B%B4")';

	$w->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:120px',
		'placeholder' => 'IP',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => 'CPU',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '内存',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '硬盘',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_button( '过滤' )->set_attrs([
		'style' => 'margin-right:8px',
		'class' => 'b',
		'onclick' => 'return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->span[] = '共找到 ' . $e . ' 个记录';



})::set_action_get( 'sales_device_get', function() use( &$sales_device_get )
{
	wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
	if ( isset( $_GET[2] ) && ( $q = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) )
	{
		$w = wa::htt_form_post( $sales_device_get );
		$w['action'] = '?/rx_erp/ajax(1)sales_device_get(2)' . $q['only'];
		$w->table->caption['style'] = 'text-align:left;line-height:32px;font-size:18px';
		$w->table->caption->pre = join( "\n", [
			'设备编号: ' . $q['only'],
			'所在机房: ' . $q['room'],
			'当前时间: ' . date( 'Y-m-d H:i:s' )
		] );
		$q = &$w->table->tbody->tr->td[1]->select;
		foreach ( wa::$sql->q_query( ' select name,rx_team from rx_hr_staff where `group`="销售部" and rx_team=?s', wa::$user['rx_team'] ? wa::$user['rx_team'] : '' ) as $e )
		{
			$r = &$q->option[];
			$r['value'] = $e['name'];
			$r[0] =  ( $e['rx_team'] ? $e['rx_team'] : '未分组' ) . ' ( ' . $e['name'] . ' )';
			$e['name'] === wa::$user['name'] && $r['selected'] = TRUE;
		}
	};
	
})::set_action_get( 'sales_group_device', function()
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
		'pw_editid' => [ FALSE, '更新员工' ]
	];
	//$w = wa::get_filter( [], [ 'status!="机柜设备"', 'pw_userid="' . wa::$user['name'] . '"' ] );

	$w = [];
	//var_dump(wa::$user["username"]);
	if(wa::$user["username"]==10476){//2016.10.13特殊处理
		foreach ( wa::$sql->q_query( 'select name from rx_hr_staff where `group`="销售部"' ) as $e )
		{
			$w[] = $e['name'];
		};
	}else{
		foreach ( wa::$sql->q_query( 'select name from rx_hr_staff where `group`="销售部" and rx_team=?s', wa::$user['rx_team'] ? wa::$user['rx_team'] : '' ) as $e )
		{
			$w[] = $e['name'];
		};
	}

	$w = [ 'status!="机柜设备"', 'pw_userid in("' . join( '","', $w ) . '")' ];

	if ( isset( $_GET[6] ) )
	{
		$e = wa::$sql->escape( '%' . $_GET[6] . '%' );
		$w[] = '(ip_main like ' . $e . ' or ip_vice like ' . $e . ')';
	};

	$w = wa::get_filter( [ 'status', 'sb_info' ], $w );
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

		$e = $q->get_parent()->add( 'tr' );
		$e['style'] = 'background:' . $bgcolor;
		$r = $e->add( 'td', "IP:\n" );
		$r->add( 'pre', strtr( $w['ip_main'], ' ', "\n" ) )['class'] = 'a_cf00';
		$r->add( 'pre', strtr( $w['ip_vice'], ' ', "\n" ) );
		$r = $e->add( 'td' )->set_attr( 'colspan', 3 );
		sales_json_info_display_my( $r, $w['sb_type'], json_decode( $w['sb_info'], TRUE ), $w['only'] );

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

		$e->add( 'td' )->add( 'pre', "业务备注:\n" . $w['pw_note'] )->set_style( 'width:224px' );




	}, [
		'merge_query' => $w,
		'stat_rows' => $e,
		'page_rows' => 21
	]);
	$q['style'] = 'margin:21px auto';
	$w = count( $q->thead->tr->td );
	$q->thead->tr->add_before( 'tr' )->add( 'td', '条件筛选' )->set_attr( 'colspan', $w );

	$w = $q->thead->tr->add_after( 'tr' )->add( 'td' )->set_attr( 'colspan', $w )->ins_filter( [ 'status' => '设备状态', 'sb_info' => '设备信息' ], [
		'onfocus' => "rd_filter_callback(this)"
	] )->dl->dt;


	$r = $w->add_before( 'div' );
	$r['style'] = 'padding-bottom:8px';
	$t = $r->tag_button( '测试设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_group_device(7)status.eq.%E6%B5%8B%E8%AF%95(8)pw_starts.asc")';

	$t = $r->tag_button( '托管设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_group_device(7)status.eq.%E6%89%98%E7%AE%A1(8)pw_expire.asc")';

	$t = $r->tag_button( '出租设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_group_device(7)status.eq.%E5%87%BA%E7%A7%9F(8)pw_expire.asc")';

	$t = $r->tag_button( '变更设备' );
	$t['style'] = 'margin-right:8px';
	$t['onclick'] = '$.go("?/rx_erp(1)sales_group_device(7)status.eq.%E5%8F%98%E6%9B%B4")';

	$r->span = '销售（' . ( wa::$user['rx_team'] ? wa::$user['rx_team'] : '未分组' ) . '）所有设备';

	$w->tag_input()->set_attrs([
		'value' => isset( $_GET[6] ) ? $_GET[6] : '',
		'style' => 'width:120px',
		'placeholder' => 'IP',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => 'CPU',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '内存',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_input()->set_attrs([
		'style' => 'width:60px',
		'placeholder' => '硬盘',
		'onkeydown' => 'if(event.keyCode==13)return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->tag_button( '过滤' )->set_attrs([
		'style' => 'margin-right:8px',
		'class' => 'b',
		'onclick' => 'return sales_device_filter($.query("input",this.parentNode))'
	]);
	$w->span[] = '共找到 ' . $e . ' 个记录';














})::set_action_get( 'sales_my_services', function()
{
	wa::$buffers->script = 'rx_ws_init("/sd_task_wait")';
	$q = [
		'sbxx' => FALSE,
		'note' => FALSE,
		'time' => [ 'desc', '提交时间' ],
		'userid' => [ FALSE, '提交员工' ],
		'riqq' => [ FALSE, '报障QQ' ],
		'imqq' => [ FALSE, '提交QQ' ],
		'pkno' => [ FALSE, '设备编号' ],
		'pkip' => [ FALSE, '设备IP' ],
		'start' => [ FALSE, '开始时间' ],
		'name' => [ FALSE, '跟进员工' ],
		'over' => [ FALSE, '完成时间' ]
	];
	$w = wa::get_filter( [], [ 'user_name="' . wa::$user['name'] . '"' ] );
	$e = wa::$sql->get_rows( 'rx_sd_task', $w );

	$q = wa::htt_data_table( 'rx_sd_task', $q, function( $q, $w )
	{
		$e = &$q->td[];
		$e['onclick'] = 'rx_tr_resh(this.parentNode)';
		$e['style'] = 'cursor:pointer;text-decoration:underline';
		$e[0] = date( 'Y-m-d H:i:s', $w['time'] );
		$q->td[] = $w['userid'];
		if ( $w['riqq'] )
		{
			$r = &$q->td[];
			$r->tag_a( $w['riqq'], 'tencent://message/?uin=' . $w['riqq'] );
		}
		else
		{
			$q->td[] = '无填写';
		};
		$r = &$q->td[];
		$r->tag_a( $w['imqq'], 'tencent://message/?uin=' . $w['imqq'] );
		$q->td[] = $w['pkno'];
		$q->td[] = $w['pkip'];
		if ( $w['start'] )
		{
			$q->td[] = date( 'Y-m-d H:i:s', $w['start'] );
			$q->td[] = $w['name'];
			$q->td[] = $w['over'] ? date( 'Y-m-d H:i:s', $w['over'] ) : '等待完成';
		}
		else
		{
			$q->td[] = '等待开始';
			$q->td[] = '等待分配';
			$q->td[] = '等待完成';
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
	
})::set_action_get( 'sales_device_services', function() use( &$rx_work_types, &$rx_work_post )
{
	wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';
	if ( isset( $_GET[2] ) && ( $q = wa::$sql->get_only( 'rx_rd_device', 'only', $_GET[2] ) ) && $q['pw_userid'] == wa::$user['name'] )
	{
		$w = wa::htt_form_post( $rx_work_post );
		$w['action'] = '?/rx_erp/ajax(1)sales_device_services(2)' . $q['only'];
		$w->table->caption['style'] = 'text-align:left;line-height:32px;font-size:18px';
		$w->table->caption->pre = join( "\n", [
			'设备编号: ' . $q['only'],
			'所在机房: ' . $q['room'],
			'当前时间: ' . date( 'Y-m-d H:i:s' )
		] );
		$w->table->tbody->tr->td[1]['style'] = 'width:330px';
		$w->table->tbody->tr->td[1]->div->input['placeholder'] = '当填写保障QQ时，将自动核对设备联系QQ是否存在';
		$w->table->tbody->tr[2]->td[1]->div->textarea['style'] = 'height:140px';
		$q = &$w->table->tbody->tr[1]->td[1]->select;
		foreach ( $rx_work_types as $e => $r )
		{
			$t = &$q->optgroup[];
			$t['label'] = [ 'ermsd' => '德胜机房运维中心', 'td' => '技术部', 'rd' => '资源部', 'sd' => '客服部' ][ $e ];
			foreach ( $r as $r )
			{
				$y = &$t->option[];
				$y['value'] = $e . ',' . $r;
				$y[0] = $r;
			};
		};
	};
})::set_action_get( 'sales_staff_update', function()
{
	rx_staff_update( '销售部', [
		'rx_team' => [ '' => '不分配',
			'霹雳队' => '霹雳队',
			'精英队' => '精英队',
			'飞虎队' => '飞虎队',
			'圆梦队' => '圆梦队',
			'先锋队' => '先锋队',
			'野狼队' => '野狼队',
			'烈火队' => '烈火队',
			'雄鹰队' => '雄鹰队',
			'冲锋队' => '冲锋队'
		],
		'rx_action' => [
			'sales_staff_update' => '允许部门员工更新（重要）',
			//'rx_pack' => '允许处理数据包',
			//'rx_notice_insert' => '允许发布通知',
			'sales_my_services' => '允许访问我的客服维护记录',
			'sales_empty_device' => '允许访问空闲设备',
			'sales_my_device' => '允许访问我的业务设备',
			'sales_group_device' => '允许访问本组业务设备',
			'sales_device_get' => '允许设备提取'
		]
	] );
})::end_action(function()
{
	rx_notice_record( '销售部' );
});