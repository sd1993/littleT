<?php
function td_json_info_display( $q, $w, $e, $p )
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