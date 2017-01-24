<?php
if ( isset( $_GET[1] ) )
{
	$key = 'server_No';
	$val = $_GET[1];
	$seq = 'no=' . $val;
};
if ( isset( $_GET[2] ) )
{
	$key = 'IP';
	$val = $_GET[2];
	$seq = 'ip=' . $val;
};
isset( $key ) || exit;
foreach( json_decode( file_get_contents( 'http://127.0.0.1/ajax_sbxx.php?'.$seq ), TRUE ) as $data )
{
	if ( $key == 'IP' )
	{
		if ( strpos( ' ' . $data['IP'] . ' ' . $data['vice_IP'] . ' ', ' ' . $val . ' ' ) !== FALSE )
		{
			preg_match_all( '/\d{1,3}(\.\d{1,3}){3}/', $data['IP'], $q );
			$data['ip_main'] = join( "\n", $q[0] );
			preg_match_all( '/\d{1,3}(\.\d{1,3}){3}/', $data['vice_IP'], $q );
			$data['ip_vice'] = join( "\n", $q[0] );
			$data['IP'] = $val;
			echo json_encode( $data, JSON_UNESCAPED_UNICODE );
			exit;
		};
	}
	else
	{
		if ( strpos( $data['server_No'] . "\n", $val . "\n" ) !== FALSE )
		{

			$data['IP'] = explode( " ", $data['IP'] );
			$data['IP'] = trim( $data['IP'][0] );
			echo json_encode( $data, JSON_UNESCAPED_UNICODE );
			exit;
		};
	};
};
?>