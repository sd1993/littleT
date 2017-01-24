<?php
wa::locale();
wa::htt();
wa::htt_nav( 'Menu', '?/' );
defined( 'WA_APPWEB_PHP' ) && wa::$buffers->write->set(function( $q )
{
	wa::htt_title( 'WA Error' );
	$q->ins_dialog( 'Require', 'File "' . WA_APPWEB_PHP . '"', 'r' );
	exit;
});
wa::sql();
isset( $_GET[1] ) && wa::$sql->q_query( 'select * from wa_menu where only=?s limit 1', $_GET[1] )->fetch_callback(function( $q )
{
	wa::htt_title( $q['name'] );
	wa::$buffers->css[] = 'appweb/wa/home.css';
	$q['merge_query'] = 'where menu=' . wa::$sql->escape( $q['only'] );
	$q['stat_rows'] = wa::$sql->get_rows( 'wa_data', $q['merge_query'] );

	$q['type'] === 'image' ? home_image( $q ) : home_table( $q );
	exit;
});
wa::htt_title( 'WA Menu' );
wa::htt_data_table( 'wa_menu', [
	'only' => FALSE,
	'root' => FALSE,
	'time' => [ FALSE, 'Create Time' ],
	0 => 'Root Directory',
	'type' => [ FALSE, 'Type' ],
	'name' => [ FALSE, 'Name' ]
], function( $q, $w )
{
	$q->td = date( WA_TIME_FORMAT, $w['time'] );
	$q->td[] = $w['root'];
	$q->td[] = $w['type'];
	$e = &$q->td[];
	$e->tag_a( $w['name'], '?/(1)' .$w['only'] );
}, [
	'merge_query' => 'where need is null',
	'page_rows' => 18
])['style'] = 'margin:42px auto';
function home_table( array &$q )
{
	wa::htt_data_table( 'wa_data', [
		'only' => FALSE,
		'time' => [ TRUE, 'Update Time' ],
		'type' => [ TRUE, 'Type' ],
		'size' => [ TRUE, 'Size' ],
		'hits' => [ TRUE, 'Hits' ],
		'name' => [ FALSE, 'Name' ]
	], function( $q, $w )
	{
		$q->td = date( WA_TIME_FORMAT, $w['time'] );
		$q->td[] = $w['type'];
		$q->td[] = wa::get_format_size( $w['size'] );
		$q->td[] = $w['hits'];
		$e = &$q->td[];
		$e->a = $w['name'];
		$e->a['href'] = '?' . $w['only'];
		$e->a['target'] = 'wa_access';
	}, [
		'merge_query' => $q['merge_query'],
		'stat_rows' => $q['stat_rows'],
		'page_rows' => 21
	])['style'] = 'margin:42px auto';
}
function home_image( array &$q )
{
	$w = wa::get_stat_page( $q['stat_rows'], 12 );
	$e = wa::$buffers->write->tag_table();
	$e['class'] = 'wa_home_image';
	if ( $w['page_max'] > 1 )
	{
		$r = &$e->tfoot->tr->td;
		$r['colspan'] = 4;
		$r->ins_page( $w );
	};
	$r = -1;
	foreach ( wa::$sql->q_query( 'select only,time,size,type,hits,name from wa_data where menu=?s limit ?i,12', $q['only'], $w['skip_rows'] ) as $w )
	{
		++$r % 4 || $t = &$e->tbody->tr[];
		$y = &$t->td[]->div;
		$y['onclick'] = 'alert("'.$w['only'].'")';
		$y = &$y->div;
		$y->div->pre = join( "\n", [
			'Time: ' . date( WA_TIME_FORMAT, $w['time'] ),
			'Size: ' . wa::get_format_size( $w['size'] ),
			'Type: ' . $w['type'],
			'Hits: ' . $w['hits'],
			'Name: ' . $w['name']
		] );
		$y->div[]['style'] = 'background-image:url("' . '?/wa/thumbnail(1)' . wa::encrypt( $q['root'] . $w['only'] ) . '");';


	}

}