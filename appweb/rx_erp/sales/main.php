<?php
$sales_device_get = [
	'pw_userid' => [
		'test' => [ 1, 32 ],
		'name' => '业务员工',
		'type' => 'select',
		'note' => '业绩统计',
		'value' => [ '' => '请选择' ]
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
		'test' => [ 0, 2048 ],
		'name' => '业务备注',
		'type' => 'textarea',
		'note' => '信息注释'
	]
];












function sales_json_info_display( $q, $w, $e, $p )
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
function sales_json_info_display_my( $q, $w, $e, $p )
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
	foreach ( $e as $w => $e )
	{
		$r .= $w . ': ' . $e . "\n";
	};
	return $q->pre[] = $r;
}