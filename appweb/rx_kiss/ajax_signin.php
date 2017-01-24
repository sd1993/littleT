<?php
wa::locale();
wa::end_str_signin( TRUE, function( $q, $w )
{
	return wa::set_signin_user( $q, $w, 'rx_hr_staff' ) && wa::$user['resign'] == 0;
});