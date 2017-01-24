<?php
wa::locale();
wa::get_signin_user( 'rx_signin', function( $q, $w )
{
	return wa::set_signin_user( $q, $w, 'rx_hr_staff' ) && wa::$user['resign'] == 0;
}) || wa::end_string( wa_unauthorized );
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'rx_notice':
		wa::$buffers = isset( $_GET[2] ) && wa::$sql->q_query( 'select content from rx_public_notice where only=?s limit 1', $_GET[2] )->fetch_bind( $q ) ? $q['content'] : '';
		exit;
 	default:
 		include 'main.php';
};
?>