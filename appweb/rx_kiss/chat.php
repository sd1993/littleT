<?php
require 'main.php';
rx_kiss_signin_ajax();
class rx_chat extends rx_kiss
{
	function __construct()
	{
		wa::$buffers = 'Î´ÖªÃüÁî';
	}

	function __destruct()
	{
		wa::end_string( wa::$buffers );
	}

	function get_staff_lists()
	{
		$q = [];
		foreach ( wa::$sql->q_query( 'select username,rx_team,`group`,name from rx_hr_staff where resign=0' ) as $w )
		{
			$q[ $w['group'] ][ $w['username'] ] = [ $w['name'], $w['rx_team'] ?? '' ];
		};
		wa::$buffers = json_encode( $q, JSON_UNESCAPED_UNICODE );
	}

	function get_chat_logs()
	{
		$q = isset( $_GET[2] )
			? wa::$sql->q_query( 'select time,`from`,content from rx_kiss_chat where `from`=?s and `to`=?s order by time asc limit 40', $_GET[2], wa::$user['username'] )
			: wa::$sql->q_query( 'select only,`from`,content from rx_kiss_chat where `to` is null order by only desc limit 40' );
		$w = [];
		foreach ( $q as $q )
		{
			$w[] = [
				'time' => substr( $q['only'], 0, 10 ),
				'from' => $q['from'],
				'content' => $q['content']
			];
		};
		wa::$buffers = json_encode( $w, JSON_UNESCAPED_UNICODE );
	}

	function post_send_to()
	{
		wa::$buffers = $this->rx_kiss_chat_send_to( $_POST['value'] ?? '', $_GET[2] ?? NULL );
		// $q = [
		// 	'only' => microtime( TRUE ) * 10000,
		// 	'form' => wa::$user['username'],
		// 	'content' => 'asdasd'
		// ];
		// $q['only'] = $q['only'] . wa::short_hash( join( $q ) );
		// if ( isset( $_GET[2] ) )
		// {
		// 	if ( preg_match( WA_USER_MATCH, $_GET[2] ) )
		// 	{
		// 		$q['to'] = $_GET[2];
		// 		wa::$buffers = [ 'ws_callback', 2 ];

		// 	}
		// 	else
		// 	{
		// 		$q['group'] = $_GET[2];
		// 		wa::$buffers = [ 'ws_callback', 1 ];
		// 	};
		// }
		// else
		// {
		// 	wa::$buffers = [ 'ws_callback', 1 ];
		// };

		
		wa::end_str_status();

	}
}
wa::end_action_class( 'rx_chat' );